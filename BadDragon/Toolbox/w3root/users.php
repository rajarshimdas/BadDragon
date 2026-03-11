<div>List Active Users</div>
<?php
require_once __DIR__ . "/../orm.php";

DB::connect([
    "driver"    => "mysql",
    "host"      => "localhost",
    "database"  => "a5db",
    "username"  => "rajarshi",
    "password"  => "m7",
    // "port"   => "port"       # Optional
]);

// Instantiate the User class
class User extends Model
{
    protected static $table = "users";
}

// Create user
// $user = new User();
// $user->name = "John";
// $user->email = "john@test.com";
// $user->save();

// Query
$users = User::all();
// var_dump($users);

// Find
$u = User::find(1);
var_dump($u);

// // Update
// $u->name = "John Updated";
// $u->save();


// print_r($users);
