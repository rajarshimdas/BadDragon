<?php
/*
 * ORM Usage Example for BadDragon/Toolbox/orm.php
 *
 * This example shows how to connect to the database, use the Query builder,
 * and work with models built on top of the ORM.
 */

require_once __DIR__ . '/../Common.php';

$config = [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'example_db',
    'username'  => 'dbuser',
    'password'  => 'dbpass',
    'port'      => 3306,
];

DB::connect($config);

// Basic SELECT with Query builder
$users = Query::table('users')
    ->select(['id', 'name', 'email'])
    ->where('active', '=', 1)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

print_r($users);

// Fetch first matching row
$user = Query::table('users')
    ->where('email', 'LIKE', '%@example.com')
    ->first();

print_r($user);

// Query builder helpers
$activeEmails = Query::table('users')
    ->whereIn('id', [1, 2, 3])
    ->pluck('email');

print_r($activeEmails);

$hasAdmin = Query::table('users')
    ->where('role', '=', 'admin')
    ->exists();

echo $hasAdmin ? "Found admin user\n" : "No admin user found\n";

// Insert a new record
$newUserId = Query::table('users')->insert([
    'name' => 'Jane Doe',
    'email' => 'jane.doe@example.com',
    'active' => 1,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);

echo "Inserted user id: $newUserId\n";

// Update records by condition
Query::table('users')
    ->where('id', '=', $newUserId)
    ->update([
        'name' => 'Jane D.',
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

// Soft delete by condition
Query::table('users')
    ->where('id', '=', $newUserId)
    ->softDelete();

// Restore a record by raw SQL example (optional)
// Query::table('users')->where('id', '=', $newUserId)->update(['active' => 1]);

// Bulk insert example
Query::table('users')->bulkInsert([
    [
        'name' => 'Bulk One',
        'email' => 'bulk1@example.com',
        'active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ],
    [
        'name' => 'Bulk Two',
        'email' => 'bulk2@example.com',
        'active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ],
]);

// Paginate result sets
$page2Users = Query::table('users')
    ->where('active', '=', 1)
    ->orderBy('id', 'ASC')
    ->paginate(2, 5);

print_r($page2Users);

// Model example: extend the base Model class
class User extends Model
{
    protected static $table = 'users';
}

$allUsers = User::all();
print_r($allUsers);

$single = User::find(1);
print_r($single);

$newModelUserId = User::create([
    'name' => 'Model User',
    'email' => 'model.user@example.com',
    'active' => 1,
]);

echo "Inserted model user id: $newModelUserId\n";

User::updateById($newModelUserId, [
    'name' => 'Updated Model User',
]);

// Conditional model update/delete
User::updateWhere(['active' => 1], ['updated_at' => date('Y-m-d H:i:s')]);
User::deleteWhere(['active' => 0]);

// Model soft delete helpers
User::softDeleteById($newModelUserId);
User::softDeleteWhere(['role' => 'guest']);

// Model convenience methods
$foundUser = User::firstOrCreate(
    ['email' => 'first.create@example.com'],
    ['name' => 'First Create', 'active' => 1]
);
print_r($foundUser);

$updatedUser = User::updateOrCreate(
    ['email' => 'update.or.create@example.com'],
    ['name' => 'Update Or Create', 'active' => 1]
);
print_r($updatedUser);

// Transaction example
try {
    DB::begin();

    Query::table('users')->insert([
        'name' => 'Tx User',
        'email' => 'tx.user@example.com',
        'active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    DB::commit();
} catch (Exception $e) {
    DB::rollback();
    echo 'Transaction failed: ' . $e->getMessage() . "\n";
}

// Print query profiling data
print_r(DB::profile());
