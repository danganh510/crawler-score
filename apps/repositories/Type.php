<?php

namespace Forexceccom\Repositories;

use Phalcon\Mvc\User\Component;
use Forexceccom\Models\ForexcecType;

class Type extends Component
{
    public static function getIdByParent($id,$location_country = "gx") {
        if ($location_country == "") {
            $location_country = "gx";
        }
        $arrId = [];
        array_push($arrId,$id);
        $arrModel = ForexcecType::find([
            'type_parent_id = :id: AND type_location_country_code = :location_country:',
            'bind' => ['id' => $id,'location_country' => $location_country],
        ]);
        if (count($arrModel) > 0) {
            foreach ($arrModel as $model) {
                $arrParent = self::getIdByParent($model->getTypeId(),$location_country);
                if ($arrParent) {
                    $arrId = array_merge($arrId,$arrParent);
                }
            }
        }
        return $arrId;
    }
    public function checkKeyword($type_keyword, $type_parent_id, $type_id, $location_code = 'gx')
    {
        return ForexcecType::findFirst(
            [
                'type_keyword = :typekeyword: AND type_parent_id = :typeparentid: AND type_id != :typeid: AND type_location_country_code = :location_code:',
                'bind' => array('typekeyword' => $type_keyword, 'typeparentid' => $type_parent_id, 'typeid' => $type_id, 'location_code' => $location_code),
            ]
        );
    }

    public function getType($str = "", $parent = 0, $inputslc, $location_code = 'gx')
    {
        $sql = 'SELECT type_id, type_parent_id, type_name FROM Forexceccom\Models\ForexcecType WHERE type_parent_id = :parentID: AND type_location_country_code = :location_code: Order By type_order ASC';
        $data = $this->modelsManager->executeQuery($sql,
            array(
                'parentID' => $parent,
                'location_code' => $location_code
            ));
        $output = "";
        foreach ($data as $key => $value) {
            $selected = "";
            if ($value->type_id == $inputslc) {
                $selected = "selected='selected'";
            }
            $output .= "<option " . $selected . " value='" . $value->type_id . "'>" . $str . $value->type_name . "</option>";
            $output .= $this->getType($str . "----", $value->type_id, $inputslc, $location_code);

        }
        return $output;
    }

    public static function getByID($id, $location_code = 'gx')
    {
        return ForexcecType::findFirst(array(
            'type_id = :id: AND type_location_country_code = :location_code:',
            'bind' => array('id' => $id, 'location_code' => $location_code)
        ));
    }

    public static function getNameByID($id, $location_code = 'gx')
    {
        $result = ForexcecType::findFirstByIdAndLocationCountryCode($id, $location_code);
        return $result ? $result->getTypeName() : '';
    }
    
    public function getAllByTypeParent($type_id, $location_code = 'gx', $lang)
    {
        $result = array();
        $para = array(
            'TYPE_ID'=>$type_id,
            'LOCATION_CODE'=>$location_code
        );
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT t.*, tl.* FROM \Forexceccom\Models\ForexcecType t
                    INNER JOIN \Forexceccom\Models\ForexcecTypeLang tl 
                        ON t.type_id = tl.type_id AND tl.type_lang_code = :LANG: AND t.type_location_country_code = tl.type_location_country_code
                    WHERE t.type_active = 'Y' AND t.type_parent_id = :TYPE_ID: AND t.type_location_country_code = :LOCATION_CODE:
                    ORDER BY t.type_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecType(),array_merge($item->t->toArray(), $item->tl->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Forexceccom\Models\ForexcecType 
                WHERE type_active = 'Y' AND type_parent_id = :TYPE_ID: AND type_location_country_code = :LOCATION_CODE:
                ORDER BY type_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
}