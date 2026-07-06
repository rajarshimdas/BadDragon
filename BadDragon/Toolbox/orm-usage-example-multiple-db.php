<?php
/*
 * Multiple database example for BadDragon/Toolbox/orm.php
 *
 * This sample shows how to connect to more than one database and run
 * queries against each connection by name.
 */

require_once __DIR__ . '/../Common.php';

$primaryConfig = [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'database' => 'app_db',
    'username' => 'dbuser',
    'password' => 'dbpass',
    'port'     => 3306,
];

$analyticsConfig = [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'database' => 'analytics_db',
    'username' => 'dbuser',
    'password' => 'dbpass',
    'port'     => 3306,
];

// Register two named connections.
DB::connect($primaryConfig, 'app');
DB::connect($analyticsConfig, 'analytics');

// Make 'app' the default connection for queries that do not specify one.
DB::use('app');

// Query the primary database explicitly.
$users = Query::table('users')
    ->on('app')
    ->select(['id', 'loginname', 'emailid', 'fullname'])
    ->where('active', '=', 1)
    ->limit(5)
    ->get();

print "Primary database users:\n";
print_r($users);

// Query the analytics database explicitly.
$visits = Query::table('visits')
    ->on('analytics')
    ->select(['id', 'user_id', 'created_at'])
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get();

print "Analytics database visits:\n";
print_r($visits);

// Switch the default connection and query without calling ->on(...).
DB::use('analytics');
$recentVisits = Query::table('visits')
    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-7 days')))
    ->limit(10)
    ->get();

print "Recent visits from the default analytics connection:\n";
print_r($recentVisits);

/*
MariaDB [a5db]> describe users;
+---------------+-----------------------+------+-----+---------------------+-------------------------------+
| Field         | Type                  | Null | Key | Default             | Extra                         |
+---------------+-----------------------+------+-----+---------------------+-------------------------------+
| id            | mediumint(8) unsigned | NO   | PRI | NULL                | auto_increment                |
| domain_id     | smallint(5) unsigned  | NO   | MUL | NULL                |                               |
| loginname     | varchar(50)           | NO   | UNI | NULL                |                               |
| passwd_md5    | char(32)              | NO   |     | NULL                |                               |
| fullname      | varchar(50)           | NO   |     | NULL                |                               |
| emailid       | varchar(150)          | NO   |     | NULL                |                               |
| remark        | varchar(250)          | NO   |     | NULL                |                               |
| created_at    | timestamp             | NO   |     | current_timestamp() |                               |
| updated_at    | timestamp             | YES  |     | NULL                | on update current_timestamp() |
| active        | tinyint(1)            | NO   |     | 1                   |                               |
| pw_valid_flag | tinyint(1)            | NO   |     | 0                   |                               |
+---------------+-----------------------+------+-----+---------------------+-------------------------------+
11 rows in set (0.033 sec)
*/

// CRUD operations example for the primary database, users table.
DB::use('app');

$loginname = 'crud_sample_' . time();
$passwdMd5 = md5('secret123');
$createdUserId = Query::table('users')
    ->insert([
        'domain_id' => 1,
        'loginname' => $loginname,
        'passwd_md5' => $passwdMd5,
        'fullname' => 'CRUD Sample',
        'emailid' => 'crud.sample@example.com',
        'remark' => 'created by ORM example',
        'active' => 1,
        'pw_valid_flag' => 1,
    ]);

print "Created user id: $createdUserId\n";

$createdUser = Query::table('users')
    ->where('id', '=', $createdUserId)
    ->first();

print "Read back the created user:\n";
print_r($createdUser);

Query::table('users')
    ->where('id', '=', $createdUserId)
    ->update([
        'fullname' => 'CRUD Sample Updated',
        'remark' => 'updated by ORM example',
    ]);

$updatedUser = Query::table('users')
    ->where('id', '=', $createdUserId)
    ->first();

print "Updated user:\n";
print_r($updatedUser);

Query::table('users')
    ->where('id', '=', $createdUserId)
    ->delete();

$deletedUser = Query::table('users')
    ->where('id', '=', $createdUserId)
    ->first();

print "Deleted user lookup:\n";
print_r($deletedUser);

// upsert example: insert or update based on the unique loginname column.
$upsertLoginname = 'upsert_sample';
$upsertPasswdMd5 = md5('upsert123');

Query::table('users')->upsert([
    'domain_id' => 1,
    'loginname' => $upsertLoginname,
    'passwd_md5' => $upsertPasswdMd5,
    'fullname' => 'Upsert Sample',
    'emailid' => 'upsert@example.com',
    'remark' => 'inserted by upsert example',
    'active' => 1,
    'pw_valid_flag' => 1,
], ['loginname']);

$upsertUser = Query::table('users')
    ->where('loginname', '=', $upsertLoginname)
    ->first();

print "Upserted user after insert:\n";
print_r($upsertUser);

Query::table('users')->upsert([
    'domain_id' => 1,
    'loginname' => $upsertLoginname,
    'passwd_md5' => $upsertPasswdMd5,
    'fullname' => 'Upsert Sample Updated',
    'emailid' => 'upsert.updated@example.com',
    'remark' => 'updated by upsert example',
    'active' => 1,
    'pw_valid_flag' => 1,
], ['loginname']);

$updatedUpsertUser = Query::table('users')
    ->where('loginname', '=', $upsertLoginname)
    ->first();

print "Upserted user after update:\n";
print_r($updatedUpsertUser);
