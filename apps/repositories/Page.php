<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecPage;
use Phalcon\Mvc\User\Component;

class Page extends Component
{
    public static function checkKeyword($keyword, $page_id, $country_code)
    {
        $result = ForexcecPage::findFirst(array(
            "page_keyword = :keyword: AND page_id != :pageID: AND page_location_country_code = :country_code:",
            "bind" => array(
                "keyword" => $keyword,
                "pageID" => $page_id,
                "country_code" => $country_code
            )
        ));
        if ($result) {
            return false;
        }
        return true;
    }

    /**
     * @param string $page_keyword
     * @return ForexcecPage
     */
    public function findFirstPageByPageKeyword($page_keyword, $location_code='gx', $lang = 'en')
    {
        $page = false;
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT f.*, fl.* FROM \Forexceccom\Models\ForexcecPage f
                INNER JOIN \Forexceccom\Models\ForexcecPageLang fl
                ON fl.page_id = f.page_id AND f.page_location_country_code = fl.page_location_country_code
                WHERE fl.page_lang_code = '$lang' AND f.page_location_country_code = '$location_code'
                AND f.page_keyword = '$page_keyword'
                ";
            $lists = $this->modelsManager->executeQuery($sql)->getFirst();
            if ($lists) {
                $page = \Phalcon\Mvc\Model::cloneResult(
                    new ForexcecPage(),
                    [
                        "page_id" => $lists->f->getPageId(),
                        "page_name" => $lists->fl->getPageName(),
                        "page_title" => $lists->fl->getPageTitle(),
                        "page_keyword" => $lists->f->getPageKeyword(),
                        "page_meta_keyword" => $lists->fl->getPageMetaKeyWord(),
                        "page_meta_description" => $lists->fl->getPageMetaDescription(),
                        "page_meta_image" => $lists->f->getPageMetaImage(),
                        "page_content" => $lists->fl->getPageContent(),
                    ]
                );
            }
        } else {
            $page = ForexcecPage::findFirst(array(
                'page_keyword = :page_keyword: AND page_location_country_code = :location_code:',
                'bind' => array(
                    'page_keyword' => $page_keyword,
                    'location_code' => $location_code,
                )
            ));
        }
        return $page;
    }

    public function AutoGenMetaPage($page_keyword, $value_default, $in_info = null, $location = 'gx', $lang = 'en', $more_value = null)
    {
        $page_object = $this->findFirstPageByPageKeyword($page_keyword, $location, $lang);
        if ($page_object) {
            $this->tag->setTitle($page_object->getPageTitle() . $more_value);
            //Set meta
            $this->view->meta_key = $page_object->getPageMetaKeyword() . $more_value;
            $this->view->meta_descript = $page_object->getPageMetaDescription() . $more_value;
            $this->view->menu_bread = $page_object->getPageName() . $more_value;
            // Set menu Active
            $this->view->menu_active = $in_info;
            $this->view->id_info = $page_object->getPageKeyword();
            $this->view->page_content = $page_object->getPageContent();
            $this->view->meta_social_image = $page_object->getPageMetaImage();
        } else {
            // if not found data in Page
            $this->tag->setTitle($value_default . $more_value);
            $this->view->meta_key = $value_default . $more_value;
            $this->view->meta_descript = $value_default . $more_value;
            // Set menu Active
            $this->view->menu_active = $in_info;
            // Set Breadcum
            $this->view->menu_bread = $value_default . $more_value;
            //Render info article keyword
            $this->view->id_info = $in_info;
            $this->view->page_content = '';
            $this->view->meta_social_image = '';
        }
    }

    public function generateStylePage($page_keyword, $location = 'gx', $lang = 'en')
    {
        $page_object = $this->findFirstPageByPageKeyword($page_keyword, $location, $lang);
        if (!$page_object) {
            $this->view->page_style = '';
        } else {
            /**
             * @var ForexcecPage $page_object
             */
            $this->view->page_style = $page_object->getPageStyle();
        }
    }

    public static function getByIdAndLocationCountryCode($page_id, $country_code)
    {
        return ForexcecPage::findFirst(array(
            'page_id = :page_id: AND page_location_country_code = :country_code:',
            'bind' => array('page_id' => $page_id, 'country_code' => $country_code)
        ));
    }
}
