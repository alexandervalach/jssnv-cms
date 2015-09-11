<?php

namespace App\Model;

class UserRepository extends Repository{
    public static $ROLES = array(
        '1' => 'admin',
        '2' => 'user',
        '3' => 'powerUser'
    );
}
