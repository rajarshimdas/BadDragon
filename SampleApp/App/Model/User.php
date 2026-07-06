<?php

class User extends Model
{
    protected static $table = 'users';
    protected static $primaryKey = 'id';
    protected static $allowedOrderColumns = ['loginname', 'fullname', 'emailid', 'created_at'];
}

// CRUD operations example for the User model
//
// $userId = User::create([
//     'loginname' => 'alice',
//     'fullname' => 'Alice Smith',
//     'emailid' => 'alice@example.com',
//     'active' => 1,
// ]);
//
// $user = User::find($userId);
//
// User::updateById($userId, [
//     'fullname' => 'Alice Example',
//     'emailid' => 'alice.updated@example.com',
// ]);
//
// User::softDeleteById($userId);
//
// $users = User::orderByAllowed('created_at', 'DESC')->get();

