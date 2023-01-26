<?php

namespace Score\Repositories;
use Score\Models\ForexcecBanner;
use Score\Models\ForexcecType;
use Phalcon\Mvc\User\Component;
use Phalcon\Di;

class Banner extends Component
{
    const CONTROLLER = 'controller';
    const TYPE = 'type';
    const ARTICLE = 'article';

    public static function getArrayController() {
        $globalVariable = Di::getDefault()->get('globalVariable');
        $dispatcher = new \Phalcon\Mvc\Dispatcher();
        $actionName = $dispatcher->getActionName();
        $array = array(
            self::CONTROLLER.'_index' => 'Home page',
            self::CONTROLLER.'_careers' => 'Careers',
            self::CONTROLLER.'_contactus' => 'Contact Us',
            self::CONTROLLER.'_faq' => 'FAQs',
            self::CONTROLLER.'_education' => 'Education',
            self::CONTROLLER.'_strategy' => 'Strategy',
        );
        $repoType = new Type();
        $types = $repoType->getAllByTypeParent($globalVariable->typeEducationId, $location_code = 'gx', $lang = 'en');
        foreach ($types as $type) {
            $array[self::CONTROLLER.'_education'.$type->getTypeKeyword()]= "__Education ".$type->getTypeName();
        }
        $array[self::CONTROLLER.'_educationhelpandresources']= "__Education Help and Resources";
        $array[self::CONTROLLER.'_tradingstrategies']= "Tradings Trategies";
        return $array;
    }

    public function getArrayType() {
        return array('2', '3', '4', '5', '6', '7', '8', '20');
    }

    public function getArticleGroup($controller_search,$location_code)
    {
        $result = '';
        $types = ForexcecType::find([
            'type_active = "Y" AND type_location_country_code = :locationCode:',
            'bind' => ['locationCode' => $location_code]
        ]);
        foreach ($types as $type) {
            if (Article::getFirstByType($type->getTypeId()) && in_array($type->getTypeId(),$this->getArrayType())) {
                $name = Type::getNameByID($type->getTypeId());
                $result .= "<optgroup label=".$name.">";
                $result .= $this->getArticle('',$type->getTypeId(),$controller_search,$location_code);
                $result .= '</optgroup>';
            }
        }
        return $result;
    }

    public function getArticle ($str = "", $type = 0, $inputslc,$location_code)
    {
        $sql = "SELECT article_keyword, article_type_id, article_name FROM Score\Models\ForexcecArticle 
                WHERE article_type_id = :typeId: AND article_active = 'Y' AND article_location_country_code = :location_code:
                Order By article_order ASC";
        $data = $this->modelsManager->executeQuery($sql,
            array(
                "typeId" => $type,
                'location_code' => $location_code
            ));
        $output="";
        foreach ($data as $key => $value) {
            if($value->article_keyword == 'trading' || $value->article_keyword == 'about-us' || $value->article_keyword == 'markets' || $value->article_keyword == 'platform' || $value->article_keyword == 'partnership'){
                $selected ="";
                if(self::CONTROLLER.'_'.str_replace('-','',$value->article_keyword) == $inputslc) {
                    $selected ="selected='selected'";
                }
                $output.= "<option ".$selected." value='".self::CONTROLLER.'_'.str_replace('-','',$value->article_keyword)."'>".$str.$value->article_name."</option>";
            }else{
                $selected ="";
                if(self::ARTICLE.'_'.$value->article_keyword == $inputslc)
                {
                    $selected ="selected='selected'";
                }
                $output.= "<option ".$selected." value='".self::ARTICLE.'_'.$value->article_keyword."'>".$str.$value->article_name."</option>";
            }
        }
        return $output;
    }

    public function getControllerCombobox($controller_search,$location_code='gx'){
        $arrController = self::getArrayController();
        $string = "<optgroup label=\"General\">";
        foreach($arrController as $controller => $title){
            $seleted = "";
            if($controller == $controller_search) {
                $seleted = "selected='selected'";
            }
            $string.="<option ".$seleted." value='".$controller."'>".$title."</option>";
        }
        $string .= '</optgroup>';
        $string .= $this->getArticleGroup($controller_search,$location_code);
        return $string;
    }

    public function getTypeCombobox($input=''){
        $arrType = array(
            ForexcecBanner::TYPEIMAGE,
            ForexcecBanner::TYPEIFRAME
        );
        $string = '';
        foreach ($arrType as $type){
            $seleted = "";
            if($type == $input){
                $seleted = "selected='selected'";
            }
            $string.="<option ".$seleted." value='".$type."'>".$type."</option>";
        }
        return $string;
    }

    public static function getValue($value,$type)
    {
        $result = '';
        $arsValue = explode('_',$value);
        if(count($arsValue) == 2 && $arsValue[0] == $type) {
            $result = $arsValue[1];
        }
        return $result;
    }

    public static function getNameController($controller,$article)
    {
        $result = '';
        if (!empty(trim($controller))) {
            $name = Article::getNameByKeyword(trim(substr($controller,strlen(self::CONTROLLER.'_'))));
            $result = isset(self::getArrayController()[$controller]) ? self::getArrayController()[$controller] : (strlen($name)>0 ? $name : '');
        } elseif (!empty(trim($article))) {
            $keyword = self::getValue($article,self::ARTICLE);
            $result = Article::getNameByKeyword($keyword);
        }
        return $result;
    }

    public static function getItem($controller,$article)
    {
        if(!empty(trim($controller))){
            $result = self::CONTROLLER.'_'.$controller;
        }else{
            $result = self::ARTICLE.'_'.$article;
        }
        return $result;
    }
}