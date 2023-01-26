<?php

namespace Forexceccom\Repositories;

use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecCommunicationChannelLang;

class CommunicationChannelLang extends Component
{
        public static function deleteById($id){
            $arr_lang = ForexcecCommunicationChannelLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static function findFirstByIdAndLang($id,$lang_code){
            return ForexcecCommunicationChannelLang::findFirst(array (
                "communication_channel_id = :ID: AND communication_channel_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }

}



