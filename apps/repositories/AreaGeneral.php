<?php

namespace Forexceccom\Repositories;

use General\Models\Area;
use Phalcon\Mvc\User\Component;

class AreaGeneral extends Component
{

    public static function getAllArea()
    {
        return Area::find(array(
            "area_active='Y' ",
            "area_order" => "area_name ASC"
        ));
    }

    public static function getCombobox($id)
    {
        $list_area = self::getAllArea();
        $output = '';
        foreach ($list_area as $area) {
            $selected = '';
            if ($area->getAreaId() == $id) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $area->getAreaId() . "'>" . $area->getAreaName() . "</option>";

        }
        return $output;
    }


}
