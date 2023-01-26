<?php
namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecCommunicationChannelCountry;
use Forexceccom\Models\ForexcecCountry;
use Phalcon\Di;
use Phalcon\Mvc\User\Component;

class CommunicationChannelCountry extends Component {

    public static function getCheckbox($id){
        $countries_select = self::getChannelCountryById($id) ?  self::getChannelCountryById($id) : [];
        $output ='';
        $countries = ForexcecCountry::find(array("country_active = 'Y'",'order'=> 'country_name ASC'));
        if ($countries->count() > 0){
            $output .= "<div class='clearfix'></div><h3></h3>";
            foreach ($countries as $value) {
                $selected = (in_array($value->getCountryCode(),$countries_select)) ? 'checked' : '';
                $output.= "<div class='role_block country_block col-md-3'><label class='container_checkbox'> ".$value->getCountryCode().' - '.$value->getCountryName()."
                        <input type='checkbox' class='form-control check' name='slcCountry[]' id='slcCountry' " . $selected . " value='" . $value->getCountryCode() . "' />
                        <span class='checkmark_checkbox'></span>
                    </label></div> ";;
            }
            $output .= "";
        }

        return $output;

    }

    // function get country By Type
    public static function getChannelCountryById($id){
        $sql = 'SELECT DISTINCT communication_channel_country_code
                FROM Forexceccom\Models\ForexcecCommunicationChannelCountry cc 
                INNER JOIN Forexceccom\Models\ForexcecCommunicationChannel c 
                ON c.communication_channel_id = cc.communication_channel_id
                WHERE c.communication_channel_id = :ID:';
        $modelsManager = Di::getDefault()->get('modelsManager');
        $data = $modelsManager->executeQuery($sql,array('ID' => $id));
        $country_codes = array();
        foreach ($data as $item){
            array_push($country_codes,$item->communication_channel_country_code);
        }
        return $country_codes;
    }

    public static function deleteAllByChannelId($channel_id){
        return ForexcecCommunicationChannelCountry::find(array(
            "communication_channel_id =:channel_id:",
            'bind' => array('channel_id' => $channel_id)
        ))->delete();
    }
}


?>