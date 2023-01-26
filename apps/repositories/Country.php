<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecCountry;
use Phalcon\Mvc\User\Component;
use Phalcon\Di;

class Country extends Component
{

    public static function getAllCountries()
    {
        return ForexcecCountry::find(array(
            "columns" => "country_code,country_name",
            "country_active='Y' AND country_code !=''",
            "order" => "country_name ASC"
        ))->toArray();
    }

    public static function getCountryGlobalComboBox($country_code)
    {
        $globalVariable = Di::getDefault()->get('globalVariable');
        $global = $globalVariable->global;
        $list_country = self::getAllCountries();
        $output = "";
        $selected = "";
        $code = strtoupper($global['code']);

        if ($country_code == 'all') {
            $selected = "selected = 'selected'";
        }
        $output .= "<option " . $selected . " value='all'> All Location Country </option>";
        $selected = "";
        if ($country_code == $code) {
            $selected = "selected = 'selected'";
        }
        $output .= "<option " . $selected . " value='" . $code . "'>" . strtoupper($global['code']) . ' - ' . $global['name'] . "</option>";
        foreach ($list_country as $country) {
            $selected = "";
            if ($country['country_code'] == $country_code) {
                $selected = "selected = 'selected'";

            }
            $output .= "<option " . $selected . " value='" . $country['country_code'] . "'>" . strtoupper($country['country_code']) . ' - ' . $country['country_name'] . "</option>";
        }
        return $output;
    }

    public static function getComboboxByCode($code)
    {
        $jurisdiction = ForexcecCountry::find(array(
            'country_active = "Y" ',
            "order" => "country_name ASC",
        ));
        $output = '';
        foreach ($jurisdiction as $value) {
            $selected = '';
            if ($value->getCountryCode() == $code) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getCountryCode() . "'>" . $value->getCountryCode() . ' - ' . $value->getCountryName() . "</option>";

        }
        return $output;
    }

    public static function getNameByCode($code)
    {
        $result = ForexcecCountry::findFirst(array(
            'country_code = :code:',
            'bind' => array('code' => $code),
        ));
        return $result ? $result->getCountryName() : '';
    }

    public static function findByCode($countryCode)
    {
        return ForexcecCountry::findFirst(array(
            "country_code=:countryCode: AND country_active='Y'",
            "bind" => array("countryCode" => $countryCode)
        ));
    }
    public static function getCountryNameOrGlobalByCode($coutry_code)
    {
        $globalVariable = Di::getDefault()->get('globalVariable');
        if ($coutry_code == $globalVariable->global['code']) {
            return $globalVariable->global['name'];
        }
        return self::getNameByCode($coutry_code);
    }

    public static function checkCountryName($country_name, $country_code)
    {
        return ForexcecCountry::findFirst(array(
                'country_name = :NAME: AND country_code !=:countrycode:',
                'bind' => array('NAME' => $country_name, 'countrycode' => $country_code))
        );
    }

    public static function checkCountryNationality($country_nationality, $country_code)
    {
        return ForexcecCountry::findFirst(array(
                'country_nationality = :NATIONALITY: AND country_code !=:countrycode:',
                'bind' => array('NATIONALITY' => $country_nationality, 'countrycode' => $country_code))
        );
    }

    public static function getAllCountry() {
        return ForexcecCountry::find(array(
            "country_active = :active:",
            'bind' => [
                'active' => 'Y',
            ],
            "order" => "country_name ASC"
        ));
    }

    public static function getCountryCombobox($code)
    {
        $country = self::getAllCountry();
        $output = '';
        foreach ($country as $value)
        {
            $selected = '';
            if($value->getCountryCode() == $code)
            {
                $selected = 'selected';
            }
            $output.= "<option ".$selected." value='".$value->getCountryCode()."'>".$value->getCountryCode().' - '.$value->getCountryName()."</option>";

        }
        return $output;
    }
}
