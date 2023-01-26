<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecCommunicationChannelCountry;
use Phalcon\Di;
use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecCommunicationChannel;

class CommunicationChannel extends Component
{
    const FOLDERCOMMUNICATION = "/../caches/communications/";
    const FILE_CACHED_COMMUNICATION = "cached_communication";

    public static function getAllCommunicationChannelCountryCode($location,$lang = 'en')
    {
        $globalVariable = Di::getDefault()->get('globalVariable');
        $modelsManager = Di::getDefault()->get('modelsManager');
        $result = array();
        $para = array();
        $listCommunicationChannel = array();
        $repoCountry = new Country();
        $countryCodes = $repoCountry->getAllCountries();
        $text_country_arr = '';
        if(count($countryCodes)>0){
            foreach ($countryCodes as $keyCountryCode => $countryCode){
                $text_country_arr .= ($keyCountryCode!=0 ? "," : "")."'".$countryCode['country_code']."'";
            }
        }

        if ($lang && $lang != $globalVariable->defaultLanguage) {
            $sql = "SELECT cc.*, ccl.*,ccc.* FROM Forexceccom\Models\ForexcecCommunicationChannel cc
                INNER JOIN Forexceccom\Models\ForexcecCommunicationChannelLang ccl
                ON cc.communication_channel_id = ccl.communication_channel_id AND ccl.communication_channel_lang_code = :LANG:
                INNER JOIN Forexceccom\Models\ForexcecCommunicationChannelCountry ccc  
                ON ccc.communication_channel_id = cc.communication_channel_id ";
            if(strlen($text_country_arr) > 0){
                $sql .= "AND ccc.communication_channel_country_code IN ($text_country_arr)";
            }
            $sql .= "WHERE ccc.communication_channel_active = 'Y' 
                ORDER BY ccc.communication_channel_order 
                ";
            $para['LANG'] = $lang;
            $lists = $modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0) {
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecCommunicationChannel(),array_merge($item->cc->toArray(), $item->ccl->toArray()));
                }
            }
        } else {
            if($location && $location != $globalVariable->defaultLocation){
                $sql = "SELECT cc.*, ccc.* FROM Forexceccom\Models\ForexcecCommunicationChannel cc                
                INNER JOIN Forexceccom\Models\ForexcecCommunicationChannelCountry ccc  
                ON ccc.communication_channel_id = cc.communication_channel_id ";
                if(strlen($text_country_arr) > 0){
                    $sql .= "AND ccc.communication_channel_country_code IN ($text_country_arr)";
                }
                $sql .= "WHERE ccc.communication_channel_active = 'Y' 
                ORDER BY ccc.communication_channel_order 
                ";

                $lists = $modelsManager->executeQuery($sql,$para);
                if($lists && sizeof($lists)>0) {
                    foreach ($lists as $item){
                        $result[] = \Phalcon\Mvc\Model::cloneResult(
                            new ForexcecCommunicationChannel(),array_merge($item->cc->toArray(), $item->ccc->toArray()));
                    }
                }
            } else{
                $sql = "SELECT cc.*, ccc.* FROM Forexceccom\Models\ForexcecCommunicationChannel cc                
                INNER JOIN Forexceccom\Models\ForexcecCommunicationChannelCountry ccc  
                ON ccc.communication_channel_id = cc.communication_channel_id 
                AND ccc.communication_channel_country_code = 'GX'
                WHERE ccc.communication_channel_active = 'Y' 
                ORDER BY ccc.communication_channel_order 
                ";
                $lists = $modelsManager->executeQuery($sql,$para);
                if($lists && sizeof($lists)>0) {
                    foreach ($lists as $item){
                        $result[] = \Phalcon\Mvc\Model::cloneResult(
                            new ForexcecCommunicationChannel(),array_merge($item->cc->toArray(), $item->ccc->toArray()));
                    }
                }
            }

        }
        foreach ($result as $data) {
            $ar = array(
                "communication_channel_id" => $data->getCommunicationChannelId(),
                "communication_channel_name" => $data->getCommunicationChannelName(),
                "communication_channel_icon" => $data->getCommunicationChannelIcon(),
                "communication_channel_type" => $data->getCommunicationChannelType(),
                "communication_channel_order" => $data->getCommunicationChannelOrder(),
                "communication_channel_active" => $data->getCommunicationChannelActive(),
            );
            $listCommunicationChannel[] = $ar;
        }
        return $listCommunicationChannel;
    }

    public static function checkName($communication_channel_name,$communication_channel_type,$communication_channel_id){
        return ForexcecCommunicationChannel::findFirst(
            [
                'communication_channel_name = :communication_channel_name: AND communication_channel_type = :communication_channel_type: AND communication_channel_id != :communication_channel_id:',
                'bind' => array(
                    'communication_channel_name' => $communication_channel_name,
                    'communication_channel_type' => $communication_channel_type,
                    'communication_channel_id' => $communication_channel_id
                ),
            ]
        );
    }

    public static function getComboBox($typeInput){
        $allType = ForexcecCommunicationChannel::allTypes();
        $result = '';
        foreach($allType as $type){
            $selected ="";
            if ($type==$typeInput){
                $selected ="selected='selected'";
            }
            $result.="<option ".$selected."  value=".$type.">".$type."</option>";
        }
        return $result;
    }

    public static function countCountryById($id){
        return ForexcecCommunicationChannelCountry::find(array(
            'communication_channel_id = :ID:',
            'bind' => array('ID'=>$id)))->count();
    }

    public static function getCache($language,$location) {
        $type_language = $language.'/';
        $folder =__DIR__.self::FOLDERCOMMUNICATION.$type_language;
        $cachedConfigFileName = $folder.self::FILE_CACHED_COMMUNICATION.'.txt';
        $cachedConfigFileNameLocation = $folder.self::FILE_CACHED_COMMUNICATION.'-'.$location.'.txt';

        $message = '';
        if (file_exists($cachedConfigFileNameLocation)){
            $message = file_get_contents($cachedConfigFileNameLocation);
        } else {
            if (file_exists($cachedConfigFileName)) {
                $message = file_get_contents($cachedConfigFileName);
            }
        }
        return $message;
    }

    public static function getNameByCChannelNameAndLang($CChannelName,$lang) {
        $globalVariable = Di::getDefault()->get('globalVariable');
        $communication_channel_model = ForexcecCommunicationChannel::findFirst(array(
            "communication_channel_name=:CChannelName:",
            'bind' => array('CChannelName' => $CChannelName)
        ));
        if (!$communication_channel_model){
            return $CChannelName;
        } elseif ($lang != $globalVariable->defaultLanguage) {
            $communication_channel_model_lang = CommunicationChannelLang::findFirstByIdAndLang($communication_channel_model->getCommunicationChannelId(),$lang);
            return $communication_channel_model_lang ? $communication_channel_model_lang->getCommunicationChannelName() : '';
        }
        return $communication_channel_model ? $communication_channel_model->getCommunicationChannelName() : '';
    }
}



