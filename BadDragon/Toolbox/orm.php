<?php /* 
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 11-Mar-2026                               |
| Updated On:                                           |
+-------------------------------------------------------+
| ChatGPT                                               |
+-------------------------------------------------------+
*/

class DB
{
    private static $pdo;
    private static $config;

    public static function connect($config)
    {
        self::$config = $config;

        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']};charset=utf8mb4";

        if ($config['driver'] === 'sqlite') {
            $dsn = "sqlite:" . $config['database'];
        }

        self::$pdo = new PDO(
            $dsn,
            $config['username'] ?? null,
            $config['password'] ?? null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public static function pdo()
    {
        return self::$pdo;
    }

    public static function query($sql, $bindings = [])
    {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
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
}

class Query
{
    private $table;
    private $select = "*";
    private $where = [];
    private $bindings = [];
    private $limit;
    private $offset;
    private $order;
    private $joins = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    public static function table($table)
    {
        return new static($table);
    }

    public function select($fields)
    {
        $this->select = is_array($fields) ? implode(",", $fields) : $fields;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->where[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function join($table, $left, $operator, $right)
    {
        $this->joins[] = "JOIN $table ON $left $operator $right";
        return $this;
    }

    public function orderBy($column, $dir = "ASC")
    {
        $this->order = "$column $dir";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    private function build()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if ($this->joins) {
            $sql .= " " . implode(" ", $this->joins);
        }

        if ($this->where) {
            $sql .= " WHERE " . implode(" AND ", $this->where);
        }

        if ($this->order) {
            $sql .= " ORDER BY {$this->order}";
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    public function get()
    {
        $stmt = DB::query($this->build(), $this->bindings);
        return $stmt->fetchAll();
    }

    public function first()
    {
        $this->limit(1);
        $stmt = DB::query($this->build(), $this->bindings);
        return $stmt->fetch();
    }

    public function insert($data)
    {
        $columns = implode(",", array_keys($data));
        $place = implode(",", array_fill(0, count($data), "?"));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($place)";

        DB::query($sql, array_values($data));

        return DB::pdo()->lastInsertId();
    }

    public function update($data)
    {
        $set = [];

        foreach ($data as $k => $v) {
            $set[] = "$k=?";
            $this->bindings[] = $v;
        }

        $set = implode(",", $set);

        $sql = "UPDATE {$this->table} SET $set";

        if ($this->where) {
            $sql .= " WHERE " . implode(" AND ", $this->where);
        }

        DB::query($sql, $this->bindings);
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";

        if ($this->where) {
            $sql .= " WHERE " . implode(" AND ", $this->where);
        }

        DB::query($sql, $this->bindings);
    }
}

abstract class Model
{
    protected static $table;
    protected static $primary = "id";
    protected $attributes = [];
    protected $original = [];

    public function __construct($data = [])
    {
        $this->attributes = $data;
        $this->original = $data;
    }

    public static function query()
    {
        return Query::table(static::$table);
    }

    public static function find($id)
    {
        $row = static::query()
            ->where(static::$primary, "=", $id)
            ->first();

        if (!$row) return null;

        return new static($row);
    }

    public static function all()
    {
        $rows = static::query()->get();
        return array_map(fn($r) => new static($r), $rows);
    }

    public function save()
    {
        $pk = static::$primary;

        if (isset($this->attributes[$pk])) {

            static::query()
                ->where($pk, "=", $this->attributes[$pk])
                ->update($this->attributes);

        } else {

            $id = static::query()->insert($this->attributes);
            $this->attributes[$pk] = $id;

        }

        return $this;
    }

    public function delete()
    {
        $pk = static::$primary;

        static::query()
            ->where($pk, "=", $this->attributes[$pk])
            ->delete();
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $val)
    {
        $this->attributes[$key] = $val;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    /* Relationships */

    public function hasMany($model, $foreign, $local = "id")
    {
        return $model::query()
            ->where($foreign, "=", $this->$local)
            ->get();
    }

    public function belongsTo($model, $foreign, $owner = "id")
    {
        return $model::query()
            ->where($owner, "=", $this->$foreign)
            ->first();
    }

    public function hasOne($model, $foreign, $local = "id")
    {
        return $model::query()
            ->where($foreign, "=", $this->$local)
            ->first();
    }
}

/* --------------------------------------------------
   Example Models
--------------------------------------------------- */

class User extends Model
{
    protected static $table = "users";

    public function posts()
    {
        return $this->hasMany(Post::class, "user_id");
    }
}

class Post extends Model
{
    protected static $table = "posts";

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}

/* --------------------------------------------------
   Example Usage
--------------------------------------------------- */

if (php_sapi_name() === "cli-server" || !debug_backtrace()) {

    DB::connect([
        "driver" => "mysql",
        "host" => "localhost",
        "database" => "test",
        "username" => "root",
        "password" => ""
    ]);

    // Create user
    $user = new User();
    $user->name = "John";
    $user->email = "john@test.com";
    $user->save();

    // Query
    $users = User::all();

    // Find
    $u = User::find(1);

    // Update
    $u->name = "John Updated";
    $u->save();

    // Relationship
    $posts = $u->posts();

    print_r($users);
}