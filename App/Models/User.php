<?php

namespace App\Models;

class User extends BaseModel
{
    protected static $table = 'users';

    /** @var int */
    public $id;

    /** @var string */
    public $username;

    /** @var string */
    public $session_key;
}