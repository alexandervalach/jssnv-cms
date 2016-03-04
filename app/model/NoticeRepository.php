<?php

namespace App\Model;

class NoticeRepository extends Repository {

    public static $flag = array(
        'success' => 'zelený',
        'info' => 'modrý',
        'warning' => 'žltý',
        'danger' => 'červený'
    );

}
