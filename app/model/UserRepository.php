<?php

namespace App\Model;

class UserRepository extends Repository {

    public static $ROLES = array(
        '3' => 'administrátor',
        '2' => 'užívateľ'
    );

}
