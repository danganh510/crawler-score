<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecRegisterPage;
use Phalcon\Mvc\User\Component;

class RegisterPage extends Component
{
    public static function getRegisterDomainCombobox($domain_search){
        $arrDomain = self::getByDomain();
        $string="";
        foreach($arrDomain as $domain){
            $selected = "";
            if($domain->page_domain==$domain_search) {
                $selected = "selected='selected'";
            }
            $string.="<option ".$selected." value='".$domain->page_domain."'>".$domain->page_domain."</option>";
        }
        return $string;

    }
    public static function getByDomain(){
      return ForexcecRegisterPage::find(
          [
              'columns' => 'page_domain',
              'group'       => 'page_domain',
          ]
      );


    }

}