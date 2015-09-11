<?php

namespace App\Model;

class NoticeRepository extends Repository {

    public static $flag = array(
        'info' => 'informácia',
        'success' => 'obyčajný oznam',
        'warning' => 'upozornenie',
        'danger' => 'dôležité'
    );

}
