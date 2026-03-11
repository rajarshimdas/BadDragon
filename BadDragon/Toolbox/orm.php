<?php /*
+-------------------------------------------------------+
| ULTRA ORM                                             |
| Author: Rajarshi Das                                  |
+-------------------------------------------------------+
*/

class DB
{
    private static $pdo;
    private static $cache = [];
    private static $queries = [];
    private static $start;

    public static function connect($config)
    {
        $driver = $config['driver'] ?? 'mysql';
        $host = $config['host'] ?? 'localhost';
        $db = $config['database'] ?? '';
        $port = $config['port'] ?? null;

        if ($driver === 'sqlite')
            $dsn = "sqlite:$db";
        else {
            $dsn = "$driver:host=$host";
            if ($port) $dsn .= ";port=$port";
            $dsn .= ";dbname=$db;charset=utf8mb4";
        }

        self::$pdo = new PDO(
            $dsn,
            $config['username'] ?? null,
            $config['password'] ?? null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        self::$start = microtime(true);
    }

    public static function query($sql, $bindings = [])
    {
        $key = md5($sql . serialize($bindings));

        if (isset(self::$cache[$key]))
            return self::$cache[$key];

        $t = microtime(true);

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($bindings);

        self::$queries[] = [
            'sql' => $sql,
            'time' => microtime(true) - $t
        ];

        return self::$cache[$key] = $stmt;
    }

    public static function pdo()
    {
        return self::$pdo;
    }

    public static function begin()
    {
        self::$pdo->beginTransaction();
    }

    public static function commit()
    {
        self::$pdo->commit();
    }

    public static function rollback()
    {
        self::$pdo->rollBack();
    }

    public static function profile()
    {
        return [
            'queries' => self::$queries,
            'total_time' => microtime(true) - self::$start
        ];
    }
}

/* ===================================================== */

class Schema
{
    private static $columns = [];

    public static function columns($table)
    {
        if (isset(self::$columns[$table]))
            return self::$columns[$table];

        $driver = DB::pdo()->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'mysql') {
            $rows = DB::query("SHOW COLUMNS FROM $table")->fetchAll();
            return self::$columns[$table] = array_column($rows, 'Field');
        }

        if ($driver === 'sqlite') {
            $rows = DB::query("PRAGMA table_info($table)")->fetchAll();
            return self::$columns[$table] = array_column($rows, 'name');
        }

        if ($driver === 'pgsql') {
            $rows = DB::query(
                "SELECT column_name FROM information_schema.columns WHERE table_name=?",
                [$table]
            )->fetchAll();
            return self::$columns[$table] = array_column($rows, 'column_name');
        }
    }
}

/* ===================================================== */

class Query
{
    protected $table;
    protected $select = "*";
    protected $where = [];
    protected $bindings = [];
    protected $joins = [];
    protected $order;
    protected $limit;
    protected $offset;

    protected $operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN'];

    public function __construct($table)
    {
        $this->validate($table);
        $this->table = $table;
    }

    public static function table($table)
    {
        return new static($table);
    }

    private function validate($id)
    {
        if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $id))
            throw new Exception("Invalid identifier $id");
    }

    public function select($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $f) $this->validate($f);
            $this->select = implode(',', $fields);
        } else {
            if ($fields !== "*") $this->validate($fields);
            $this->select = $fields;
        }
        return $this;
    }

    public function where($col, $op, $val)
    {
        $this->validate($col);

        $op = strtoupper($op);
        if (!in_array($op, $this->operators))
            throw new Exception("Invalid operator");

        $this->where[] = "$col $op ?";
        $this->bindings[] = $val;

        return $this;
    }

    public function join($table, $l, $op, $r)
    {
        $this->validate($table);
        $this->validate($l);
        $this->validate($r);

        $this->joins[] = "JOIN $table ON $l $op $r";
        return $this;
    }

    public function orderBy($col, $dir = "ASC")
    {
        $this->validate($col);
        $dir = strtoupper($dir);

        if (!in_array($dir, ['ASC', 'DESC']))
            throw new Exception("Invalid order");

        $this->order = "$col $dir";
        return $this;
    }

    public function limit($n)
    {
        $this->limit = (int)$n;
        return $this;
    }

    public function offset($n)
    {
        $this->offset = (int)$n;
        return $this;
    }

    private function build()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if ($this->joins)
            $sql .= " " . implode(" ", $this->joins);

        if ($this->where)
            $sql .= " WHERE " . implode(" AND ", $this->where);

        if ($this->order)
            $sql .= " ORDER BY {$this->order}";

        if ($this->limit !== null)
            $sql .= " LIMIT {$this->limit}";

        if ($this->offset !== null)
            $sql .= " OFFSET {$this->offset}";

        return $sql;
    }

    public function get()
    {
        return DB::query($this->build(), $this->bindings)->fetchAll();
    }

    public function first()
    {
        $this->limit(1);
        return DB::query($this->build(), $this->bindings)->fetch();
    }

    public function insert($data)
    {
        $cols = Schema::columns($this->table);

        $data = array_intersect_key($data, array_flip($cols));

        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        DB::query($sql, array_values($data));

        return DB::pdo()->lastInsertId();
    }

    public function bulkInsert($rows)
    {
        if (!$rows) return;

        $cols = array_keys($rows[0]);
        $columns = implode(',', $cols);

        $values = [];
        $bindings = [];

        foreach ($rows as $r) {
            $values[] = "(" . implode(',', array_fill(0, count($cols), '?')) . ")";
            $bindings = array_merge($bindings, array_values($r));
        }

        $values = implode(',', $values);

        $sql = "INSERT INTO {$this->table} ($columns) VALUES $values";

        DB::query($sql, $bindings);
    }

    public function update($data)
    {
        $set = [];

        foreach ($data as $k => $v) {
            $this->validate($k);
            $set[] = "$k=?";
            $this->bindings[] = $v;
        }

        $set = implode(',', $set);

        $sql = "UPDATE {$this->table} SET $set";

        if ($this->where)
            $sql .= " WHERE " . implode(" AND ", $this->where);

        DB::query($sql, $this->bindings);
    }

    public function upsert(array $data, $uniqueColumns)
    {
        $pdo = DB::pdo();
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if (!$data) {
            throw new Exception("Upsert data cannot be empty");
        }

        /* -----------------------------
        Validate column identifiers
        ------------------------------*/

        foreach ($data as $col => $val) {
            $this->validate($col);
        }

        $columns = array_keys($data);
        $bindings = array_values($data);

        $cols = implode(',', $columns);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $uniqueColumns = (array)$uniqueColumns;

        foreach ($uniqueColumns as $col) {
            $this->validate($col);
        }

        $updateColumns = array_diff($columns, $uniqueColumns);

        if (!$updateColumns) {
            throw new Exception("No columns available to update in UPSERT");
        }

        /* -----------------------------
        MYSQL
        ------------------------------*/

        if ($driver === 'mysql') {

            $updates = implode(',', array_map(
                fn($c) => "$c = VALUES($c)",
                $updateColumns
            ));

            $sql = "INSERT INTO {$this->table} ($cols)
                VALUES ($placeholders)
                ON DUPLICATE KEY UPDATE $updates";
        }

        /* -----------------------------
        POSTGRESQL / SQLITE
        ------------------------------*/ else {

            $unique = implode(',', $uniqueColumns);

            $updates = implode(',', array_map(
                fn($c) => "$c = EXCLUDED.$c",
                $updateColumns
            ));

            $sql = "INSERT INTO {$this->table} ($cols)
                VALUES ($placeholders)
                ON CONFLICT ($unique)
                DO UPDATE SET $updates";
        }

        DB::query($sql, $bindings);

        return true;
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";

        if ($this->where)
            $sql .= " WHERE " . implode(" AND ", $this->where);

        DB::query($sql, $this->bindings);
    }

    public function softDelete($id)
    {
        $this->validate('id');

        $sql = "UPDATE {$this->table} SET active = 0 WHERE id = ?";

        DB::query($sql, [$id]);

        return true;
    }

    public function paginate($page = 1, $perPage = 20)
    {
        $page = max(1, (int)$page);

        $this->limit($perPage);
        $this->offset(($page - 1) * $perPage);

        return $this->get();
    }
}

/* ===================================================== */

class Model
{
    protected static $table;

    public static function query()
    {
        return Query::table(static::$table);
    }

    public static function find($id)
    {
        return static::query()
            ->where('id', '=', $id)
            ->first();
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return static::query()->insert($data);
    }

    public static function updateById($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        static::query()
            ->where('id', '=', $id)
            ->update($data);
    }

    public static function deleteById($id)
    {
        static::query()
            ->where('id', '=', $id)
            ->delete();
    }

    /* Relationships */

    public static function hasMany($related, $foreign, $local = 'id', $id)
    {
        return $related::query()
            ->where($foreign, '=', $id)
            ->get();
    }

    public static function belongsTo($related, $foreign, $id)
    {
        return $related::query()
            ->where('id', '=', $id[$foreign])
            ->first();
    }

    public static function hasOne($related, $foreign, $local = 'id', $id)
    {
        return $related::query()
            ->where($foreign, '=', $id)
            ->first();
    }
}

/* ===================================================== */

/*
class Migration
{
    public static function create($table,$sql)
    {
        DB::query("CREATE TABLE IF NOT EXISTS $table ($sql)");
    }

    public static function drop($table)
    {
        DB::query("DROP TABLE IF EXISTS $table");
    }
}
*/