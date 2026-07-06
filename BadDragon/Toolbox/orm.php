<?php /*
+-------------------------------------------------------+
| ULTRA ORM - R0b                                       |
| Author: Rajarshi Das                                  |
+-------------------------------------------------------+
*/

class DB
{
    // support multiple named PDO connections
    private static $pdo = [];
    private static $current = 'default';
    private static $queries = [];
    private static $start = [];

    // connect and register a named connection (default name = 'default')
    public static function connect($config, $name = 'default')
    {
        $driver     = $config['driver'] ?? 'mysql';
        $host       = $config['host'] ?? 'localhost';
        $db         = $config['database'] ?? '';
        $port       = $config['port'] ?? null;

        if ($driver === 'sqlite')
            $dsn = "sqlite:$db";
        else {
            $dsn = "$driver:host=$host";
            if ($port) $dsn .= ";port=$port";
            $dsn .= ";dbname=$db;charset=utf8mb4";
        }

        self::$pdo[$name] = new PDO(
            $dsn,
            $config['username'] ?? null,
            $config['password'] ?? null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        // mark start time for this connection
        self::$start[$name] = microtime(true);
    }

    // set the active/default connection name used by query() and pdo() when no name passed
    public static function use($name)
    {
        if (!isset(self::$pdo[$name]))
            throw new Exception("Unknown DB connection $name");

        self::$current = $name;
    }

    // return PDO for a named connection or the current one
    public static function pdo($name = null)
    {
        if ($name === null) $name = self::$current;

        // if only one connection exists and requested name not present, return that PDO
        if (!isset(self::$pdo[$name]) && count(self::$pdo) === 1) {
            return reset(self::$pdo);
        }

        return self::$pdo[$name] ?? null;
    }

    // execute a query on a specific connection (or current if null)
    public static function query($sql, $bindings = [], $connection = null)
    {
        $t = microtime(true);

        $pdo = self::pdo($connection);
        if (!$pdo) throw new Exception('No PDO connection available');

        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);

        $connName = $connection ?? self::$current;
        self::$queries[] = [
            'conn' => $connName,
            'sql' => $sql,
            'time' => microtime(true) - $t
        ];

        return $stmt;
    }

    // transaction helpers that accept optional connection name
    public static function begin($connection = null)
    {
        $pdo = self::pdo($connection);
        $pdo->beginTransaction();
    }

    public static function commit($connection = null)
    {
        $pdo = self::pdo($connection);
        $pdo->commit();
    }

    public static function rollback($connection = null)
    {
        $pdo = self::pdo($connection);
        $pdo->rollBack();
    }

    public static function profile($connection = null)
    {
        if ($connection) {
            return [
                'queries' => array_values(array_filter(self::$queries, fn($q) => ($q['conn'] ?? null) === $connection)),
                'total_time' => microtime(true) - (self::$start[$connection] ?? microtime(true))
            ];
        }

        return [
            'queries' => self::$queries,
            'total_time' => microtime(true) - (self::$start[self::$current] ?? microtime(true))
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
    protected $connection = null;

    protected $operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN', 'NOT IN'];

    public function __construct($table)
    {
        $this->validate($table);
        $this->table = $table;
    }

    public static function table($table)
    {
        return new static($table);
    }

    // set connection name for this query
    public function on($connection)
    {
        $this->connection = $connection;
        return $this;
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

    public function where($col, $op, $val, $boolean = 'AND')
    {
        $this->validate($col);

        $op = strtoupper($op);
        if (!in_array($op, $this->operators))
            throw new Exception("Invalid operator");

        $clause = "$col $op ?";
        if ($this->where) {
            $this->where[] = strtoupper($boolean) . ' ' . $clause;
        } else {
            $this->where[] = $clause;
        }

        $this->bindings[] = $val;

        return $this;
    }

    public function orWhere($col, $op, $val)
    {
        return $this->where($col, $op, $val, 'OR');
    }

    public function whereIn($col, array $values, $boolean = 'AND', $not = false)
    {
        $this->validate($col);

        if (!$values) {
            throw new Exception("Values for whereIn cannot be empty");
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $operator = $not ? 'NOT IN' : 'IN';
        $clause = "$col $operator ($placeholders)";

        if ($this->where) {
            $this->where[] = strtoupper($boolean) . ' ' . $clause;
        } else {
            $this->where[] = $clause;
        }

        $this->bindings = array_merge($this->bindings, array_values($values));

        return $this;
    }

    public function orWhereIn($col, array $values)
    {
        return $this->whereIn($col, $values, 'OR');
    }

    public function whereNotIn($col, array $values)
    {
        return $this->whereIn($col, $values, 'AND', true);
    }

    public function orWhereNotIn($col, array $values)
    {
        return $this->whereIn($col, $values, 'OR', true);
    }

    public function whereNull($col, $boolean = 'AND')
    {
        $this->validate($col);
        $clause = "$col IS NULL";

        if ($this->where) {
            $this->where[] = strtoupper($boolean) . ' ' . $clause;
        } else {
            $this->where[] = $clause;
        }

        return $this;
    }

    public function whereNotNull($col, $boolean = 'AND')
    {
        $this->validate($col);
        $clause = "$col IS NOT NULL";

        if ($this->where) {
            $this->where[] = strtoupper($boolean) . ' ' . $clause;
        } else {
            $this->where[] = $clause;
        }

        return $this;
    }

    public function whereRaw($sql, $bindings = [], $boolean = 'AND')
    {
        if ($this->where) {
            $this->where[] = strtoupper($boolean) . ' ' . $sql;
        } else {
            $this->where[] = $sql;
        }

        $this->bindings = array_merge($this->bindings, $bindings);
        return $this;
    }

    public function join($table, $l, $op, $r, $type = 'JOIN')
    {
        $this->validate($table);
        $this->validate($l);
        $this->validate($r);

        $this->joins[] = strtoupper($type) . " $table ON $l $op $r";
        return $this;
    }

    public function leftJoin($table, $l, $op, $r)
    {
        return $this->join($table, $l, $op, $r, 'LEFT JOIN');
    }

    public function rightJoin($table, $l, $op, $r)
    {
        return $this->join($table, $l, $op, $r, 'RIGHT JOIN');
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

    public function orderByDesc($col)
    {
        return $this->orderBy($col, 'DESC');
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

    private function build($select = null)
    {
        $select = $select ?? $this->select;
        $sql = "SELECT {$select} FROM {$this->table}";

        if ($this->joins)
            $sql .= " " . implode(" ", $this->joins);

        if ($this->where)
            $sql .= " WHERE " . implode(" ", $this->where);

        if ($this->order)
            $sql .= " ORDER BY {$this->order}";

        if ($this->limit !== null)
            $sql .= " LIMIT {$this->limit}";

        if ($this->offset !== null)
            $sql .= " OFFSET {$this->offset}";

        return $sql;
    }

    protected function execute($sql)
    {
        return DB::query($sql, $this->bindings, $this->connection);
    }

    public function toSql()
    {
        return $this->build();
    }

    public function get()
    {
        return $this->execute($this->build())->fetchAll();
    }

    public function first()
    {
        $this->limit(1);
        return $this->execute($this->build())->fetch();
    }

    public function count($column = '*')
    {
        $sql = $this->build("COUNT($column) AS count");
        return (int)DB::query($sql, $this->bindings)->fetchColumn();
    }

    public function exists()
    {
        $sql = $this->build('1');
        return (bool)DB::query($sql . ' LIMIT 1', $this->bindings)->fetchColumn();
    }

    public function value($column)
    {
        $sql = $this->build($column);
        $row = DB::query($sql . ' LIMIT 1', $this->bindings)->fetch();
        return $row[$column] ?? null;
    }

    public function pluck($column, $key = null)
    {
        $select = $column;
        if ($key) {
            $select = "$column, $key";
        }

        $sql = $this->build($select);
        $rows = DB::query($sql, $this->bindings)->fetchAll();

        if ($key) {
            $result = [];
            foreach ($rows as $row) {
                $result[$row[$key]] = $row[$column];
            }
            return $result;
        }

        return array_column($rows, $column);
    }

    public function insert($data)
    {
        $cols = Schema::columns($this->table);

        $data = array_intersect_key($data, array_flip($cols));

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        DB::query($sql, array_values($data));

        return DB::pdo()->lastInsertId();
    }

    public function bulkInsert($rows)
    {
        if (!$rows) {
            return;
        }

        $cols = array_keys($rows[0]);
        $columns = implode(', ', $cols);

        $values = [];
        $bindings = [];

        foreach ($rows as $r) {
            $values[] = '(' . implode(', ', array_fill(0, count($cols), '?')) . ')';
            $bindings = array_merge($bindings, array_values($r));
        }

        $sql = "INSERT INTO {$this->table} ($columns) VALUES " . implode(', ', $values);

        DB::query($sql, $bindings);
    }

    public function update($data)
    {
        $set = [];

        foreach ($data as $k => $v) {
            $this->validate($k);
            $set[] = "$k = ?";
            $this->bindings[] = $v;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        $sql .= $this->where ? ' WHERE ' . implode(' ', $this->where) : '';

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
        $sql .= $this->where ? ' WHERE ' . implode(' ', $this->where) : '';

        DB::query($sql, $this->bindings);
    }

    protected function inferPrimaryKey()
    {
        $cols = Schema::columns($this->table);

        if (in_array('id', $cols)) {
            return 'id';
        }

        foreach ($cols as $col) {
            if (preg_match('/_id$/', $col)) {
                return $col;
            }
        }

        return $cols[0] ?? 'id';
    }

    public function softDelete($id = null, $col = null)
    {
        if ($id !== null) {
            if ($col === null) {
                $col = $this->inferPrimaryKey();
            }

            $this->validate($col);
            $this->where($col, '=', $id);
        }

        $sql = "UPDATE {$this->table} SET active = 0";
        $sql .= $this->where ? ' WHERE ' . implode(' ', $this->where) : '';

        DB::query($sql, $this->bindings);

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
    protected static $primaryKey = 'id';
    protected static $allowedOrderColumns = [];

    // Return the primary key column name for this model's table.
    // If a model declares `$primaryKey`, use it.
    // Otherwise inspect the table columns and prefer `id`, then any `*_id` column.
    public static function primaryKey()
    {
        $vars = get_class_vars(static::class);
        if (array_key_exists('primaryKey', $vars) && $vars['primaryKey']) {
            return $vars['primaryKey'];
        }

        $table = static::$table ?? null;
        if (!$table) return 'id';

        $cols = Schema::columns($table);

        if (in_array('id', $cols)) return 'id';

        foreach ($cols as $c) {
            if (preg_match('/_id$/', $c)) return $c;
        }

        return $cols[0] ?? 'id';
    }

    public static function query()
    {
        return Query::table(static::$table);
    }

    // start a query on a specific connection
    public static function on($connection)
    {
        return Query::table(static::$table)->on($connection);
    }

    public static function allowedOrderColumns()
    {
        return (array) static::$allowedOrderColumns;
    }

    public static function orderByAllowed($column, $direction = 'ASC')
    {
        $columns = static::allowedOrderColumns();
        if ($columns && !in_array($column, $columns, true)) {
            throw new Exception("Ordering by {$column} is not allowed");
        }

        return static::query()->orderBy($column, $direction);
    }

    public static function find($id)
    {
        $pk = static::primaryKey();
        return static::query()
            ->where($pk, '=', $id)
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

    public static function firstOrCreate($attributes, $values = [])
    {
        $query = static::query();

        foreach ($attributes as $key => $value) {
            $query->where($key, '=', $value);
        }

        $record = $query->first();
        if ($record) {
            return $record;
        }

        return static::create(array_merge($attributes, $values));
    }

    public static function updateOrCreate($attributes, $values = [])
    {
        $query = static::query();

        foreach ($attributes as $key => $value) {
            $query->where($key, '=', $value);
        }

        $record = $query->first();

        if ($record) {
            $pk = static::primaryKey();
            static::query()
                ->where($pk, '=', $record[$pk])
                ->update(array_merge($attributes, $values));

            return static::find($record[$pk]);
        }

        return static::create(array_merge($attributes, $values));
    }

    public static function upsert(array $data, $uniqueColumns)
    {
        if (!$data) {
            throw new Exception('Upsert data cannot be empty');
        }

        $uniqueColumns = (array) $uniqueColumns;
        if (!$uniqueColumns) {
            throw new Exception('Unique columns must be provided for upsert');
        }

        $query = static::query();
        $query->upsert($data, $uniqueColumns);

        $pk = static::primaryKey();
        if (isset($data[$pk])) {
            return static::find($data[$pk]);
        }

        $findQuery = static::query();
        foreach ($uniqueColumns as $column) {
            if (!array_key_exists($column, $data)) {
                throw new Exception("Missing unique column '{$column}' in upsert data");
            }
            $findQuery->where($column, '=', $data[$column]);
        }

        return $findQuery->first();
    }

    public static function updateById($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $pk = static::primaryKey();
        static::query()
            ->where($pk, '=', $id)
            ->update($data);
    }

    public static function updateWhere(array $conditions, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $query = static::query();

        foreach ($conditions as $key => $value) {
            $query->where($key, '=', $value);
        }

        $query->update($data);
    }

    public static function deleteById($id)
    {
        $pk = static::primaryKey();
        static::query()
            ->where($pk, '=', $id)
            ->delete();
    }

    public static function deleteWhere(array $conditions)
    {
        $query = static::query();

        foreach ($conditions as $key => $value) {
            $query->where($key, '=', $value);
        }

        $query->delete();
    }

    public static function softDelete($id)
    {
        $pk = static::primaryKey();
        return static::query()->where($pk, '=', $id)->softDelete();
    }

    public static function softDeleteById($id)
    {
        $pk = static::primaryKey();
        return static::query()->where($pk, '=', $id)->softDelete();
    }

    public static function softDeleteWhere(array $conditions)
    {
        $query = static::query();

        foreach ($conditions as $key => $value) {
            $query->where($key, '=', $value);
        }

        return $query->softDelete();
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
        $pk = $related::primaryKey();
        $value = is_array($id) ? ($id[$foreign] ?? null) : $id;
        return $related::query()
            ->where($pk, '=', $value)
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