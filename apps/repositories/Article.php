<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ForexcecArticle;

class Article extends Component
{
    public static function getFirstByType($type_id, $location_code = 'gx')
    {
        $result = ForexcecArticle::findFirst(array(
            "article_type_id = :typeID: AND article_location_country_code = :location_code:",
            "bind" => array("typeID" => $type_id, 'location_code' => $location_code)
        ));
        return $result;
    }

    public static function getByTypeAndActive($type_id, $active, $location_code = 'gx')
    {
        $result = ForexcecArticle::find(array(
            "article_type_id = :typeID: AND article_active = :active: AND article_location_country_code = :location_code:",
            "bind" => array("typeID" => $type_id, 'active' => $active, 'location_code' => $location_code)
        ));
        return $result;
    }

    public static function checkKeywordByCountryAndLocationCountry($article_keyword, $article_type_id, $id, $article_location_country_code)
    {

        return ForexcecArticle::findFirst(array(
                'article_keyword = :keyword: AND article_type_id = :article_type: AND article_location_country_code = :article_location_country_code: AND article_id != :id:',
                'bind' => array('keyword' => $article_keyword,
                    'article_type' => $article_type_id,
                    'article_location_country_code' => $article_location_country_code,
                    'id' => $id),
            )
        );
    }

    public static function getByID($article_id, $location_code = 'gx')
    {
        return ForexcecArticle::findFirst(array(
            'article_id = :article_id: AND article_location_country_code = :location_code:',
            'bind' => array('article_id' => $article_id, "location_code" => $location_code)
        ));
    }

    public static function getNameByKeyword($keyword, $location_code = 'gx'){
        $result = ForexcecArticle::findFirst(array(
            "article_keyword = :keyword: AND article_location_country_code = :location_code:",
            "bind" => array("keyword" => $keyword, "location_code" => $location_code)
        ));
        return $result?$result->getArticleName():'';
    }

    public function getByTypeAndOrder ($type_id, $location_code = 'gx', $lang, $limit = null){
        $result = array();
        $para = array(
            'TYPE_ID'=>$type_id,
            'LOCATION_CODE'=>$location_code
        );
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Score\Models\ForexcecArticle a 
                    INNER JOIN \Score\Models\ForexcecArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: AND a.article_location_country_code = al.article_location_country_code
                    WHERE a.article_active = 'Y' AND a.article_type_id = :TYPE_ID: AND a.article_location_country_code = :LOCATION_CODE:
                    ORDER BY a.article_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ForexcecArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Score\Models\ForexcecArticle 
                WHERE article_active = 'Y' AND article_type_id = :TYPE_ID: AND article_location_country_code = :LOCATION_CODE:
                ORDER BY article_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }

    public static function getMaxLessionId() {
        $result =  ForexcecArticle::findFirst([
            'order' => 'article_lession_id DESC',
        ]);
        return $result ? $result->getArticleLessionId() : 0;
    }
    public static function findFirstByLessionId($lession_id) {
        return ForexcecArticle::findFirst([
            'article_lession_id = :lession_id: AND article_active = "Y" ',
            'bind' => ['lession_id' => $lession_id],
        ]);
    }
}

