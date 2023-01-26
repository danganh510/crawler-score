<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecPartnership;
use Phalcon\Mvc\User\Component;


class Partnership extends Component
{
    public static function getByLimit($limit){
        return ForexcecPartnership::find(array(
            "order"      => "partnership_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}



