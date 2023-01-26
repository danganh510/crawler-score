<?php

namespace Forexceccom\Repositories;

use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecPayment;

class Payment extends Component
{
    public static function getStatusCombobox($status_search){
        $arrStatus = ForexcecPayment::allPaymentStatus();
        $string="";
        foreach($arrStatus as $status){
            $selected = "";
            if($status==$status_search) {
                $selected = "selected='selected'";
            }
            $string.="<option ".$selected." value='".$status."'>".$status."</option>";
        }
        return $string;

    }

    public static function getMethodCombobox($method_search){
        $arrMethod = ForexcecPayment::allPaymentMethod();
        $string="";
        foreach($arrMethod as $method){
            $selected = "";
            if($method == $method_search) {
                $selected = "selected='selected'";
            }
            $string.="<option ".$selected." value='".$method."'>".$method."</option>";
        }
        return $string;

    }
    public static function getByLimit($limit){
        return ForexcecPayment::find(array(
            "order"      => "payment_insertdate DESC",
            "limit"      => $limit,
        ));
    }
}