<?php

namespace App\Model;

class NoticeRepository extends Repository {

    public static $flag = array(
        'success' => 'obyčajný',
        'info' => 'nová informácia',
        'warning' => 'upozornenie',
        'danger' => 'dôležité'
    );

}
