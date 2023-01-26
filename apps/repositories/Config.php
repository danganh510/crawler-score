<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecConfig;
use Phalcon\Mvc\User\Component;

class Config extends Component
{
    public static function findByID($key){
        return ForexcecConfig::findFirst(array(
                "config_key =:key:",
                'bind' => array('key' => $key))
        );
    }
    public static function findByLanguage($key,$language){
        return ForexcecConfig::findFirst(array("config_key =:key:  AND config_language=:language:",
            'bind' => array('key' => $key,'language'=>$language)));
    }

    /**
     * @param $key
     * @return ForexcecConfig|ForexcecConfig[]
     */
    public static function getByID($key){
        return ForexcecConfig::find(array(
                "config_key =:key:",
                'bind' => array('key' => $key))
        );
    }
    public static function deletedByKey($key){
        $list_config = ForexcecConfig::find(array("config_key =:key:",
            'bind' =>  array('key' => $key)));
        foreach ($list_config as $config){
            $config->delete();
        }
        return true;
    }
    public static function checkKeyword($key_new)
    {
        return ForexcecConfig::findFirst(array (
                'config_key = :keyID: ',
                'bind' => array('keyID' => $key_new),
            ));
    }

    public static function curl_get_contents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "ctoken=k3FRQ1U0bYHUVSu6");
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}




