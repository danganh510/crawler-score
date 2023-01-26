<?php

namespace Forexceccom\Models;

class ForexcecArticle extends \Phalcon\Mvc\Model
{
    protected $article_id;
    protected $article_location_country_code;
    protected $article_type_id;
    protected $article_name;
    protected $article_icon;
    protected $article_keyword;
    protected $article_title;
    protected $article_meta_keyword;
    protected $article_meta_description;
    protected $article_meta_image;
    protected $article_summary;
    protected $article_content;
    protected $article_order;
    protected $article_active;
    protected $article_is_home;
    protected $article_insert_time;
    protected $article_update_time;
    protected $article_lession_id;

    /**
     * @return mixed
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * @param mixed $article_id
     */
    public function setArticleId($article_id)
    {
        $this->article_id = $article_id;
    }

    /**
     * @return mixed
     */
    public function getArticleLocationCountryCode()
    {
        return $this->article_location_country_code;
    }

    /**
     * @param mixed $article_location_country_code
     */
    public function setArticleLocationCountryCode($article_location_country_code)
    {
        $this->article_location_country_code = $article_location_country_code;
    }

    /**
     * @return mixed
     */
    public function getArticleTypeId()
    {
        return $this->article_type_id;
    }

    /**
     * @param mixed $article_type_id
     */
    public function setArticleTypeId($article_type_id)
    {
        $this->article_type_id = $article_type_id;
    }

    /**
     * @return mixed
     */
    public function getArticleName()
    {
        return $this->article_name;
    }

    /**
     * @param mixed $article_name
     */
    public function setArticleName($article_name)
    {
        $this->article_name = $article_name;
    }

    /**
     * @return mixed
     */
    public function getArticleIcon()
    {
        return $this->article_icon;
    }

    /**
     * @param mixed $article_icon
     */
    public function setArticleIcon($article_icon)
    {
        $this->article_icon = $article_icon;
    }

    /**
     * @return mixed
     */
    public function getArticleKeyword()
    {
        return $this->article_keyword;
    }

    /**
     * @param mixed $article_keyword
     */
    public function setArticleKeyword($article_keyword)
    {
        $this->article_keyword = $article_keyword;
    }

    /**
     * @return mixed
     */
    public function getArticleTitle()
    {
        return $this->article_title;
    }

    /**
     * @param mixed $article_title
     */
    public function setArticleTitle($article_title)
    {
        $this->article_title = $article_title;
    }

    /**
     * @return mixed
     */
    public function getArticleMetaKeyword()
    {
        return $this->article_meta_keyword;
    }

    /**
     * @param mixed $article_meta_keyword
     */
    public function setArticleMetaKeyword($article_meta_keyword)
    {
        $this->article_meta_keyword = $article_meta_keyword;
    }

    /**
     * @return mixed
     */
    public function getArticleMetaDescription()
    {
        return $this->article_meta_description;
    }

    /**
     * @param mixed $article_meta_description
     */
    public function setArticleMetaDescription($article_meta_description)
    {
        $this->article_meta_description = $article_meta_description;
    }

    /**
     * @return mixed
     */
    public function getArticleMetaImage()
    {
        return $this->article_meta_image;
    }

    /**
     * @param mixed $article_meta_image
     */
    public function setArticleMetaImage($article_meta_image)
    {
        $this->article_meta_image = $article_meta_image;
    }

    /**
     * @return mixed
     */
    public function getArticleSummary()
    {
        return $this->article_summary;
    }

    /**
     * @param mixed $article_summary
     */
    public function setArticleSummary($article_summary)
    {
        $this->article_summary = $article_summary;
    }

    /**
     * @return mixed
     */
    public function getArticleContent($langString = null, $script = false, $txt_lead_form_partner = null)
    {
        $artical_content_langString = $this->article_content;
        if (is_string($artical_content_langString) && is_string($langString)) {
            $artical_content_langString = str_replace('|||LANG|||', $langString, $artical_content_langString);
        }
        if (is_string($artical_content_langString) && $script) {
            $artical_content_langString = str_replace(array('|||SCRIPTBEFORE|||', '|||SCRIPTAFTER|||', '|||NOSCRIPTBEFORE|||', '|||NOSCRIPTAFTER|||'),array('<script>', '</script>', '<noscript>', '</noscript>'),$artical_content_langString);
        }
        if(is_string($artical_content_langString) && is_string($txt_lead_form_partner)){
            $artical_content_langString = str_replace('|||LEADFORMPARTNER|||', $txt_lead_form_partner, $artical_content_langString);
        }
        return $artical_content_langString;
    }

    /**
     * @param mixed $article_content
     */
    public function setArticleContent($article_content)
    {
        $this->article_content = $article_content;
    }

    /**
     * @return mixed
     */
    public function getArticleOrder()
    {
        return $this->article_order;
    }

    /**
     * @param mixed $article_order
     */
    public function setArticleOrder($article_order)
    {
        $this->article_order = $article_order;
    }

    /**
     * @return mixed
     */
    public function getArticleActive()
    {
        return $this->article_active;
    }

    /**
     * @param mixed $article_active
     */
    public function setArticleActive($article_active)
    {
        $this->article_active = $article_active;
    }

    /**
     * @return mixed
     */
    public function getArticleIsHome()
    {
        return $this->article_is_home;
    }

    /**
     * @param mixed $article_is_home
     */
    public function setArticleIsHome($article_is_home)
    {
        $this->article_is_home = $article_is_home;
    }

    /**
     * @return mixed
     */
    public function getArticleInsertTime()
    {
        return $this->article_insert_time;
    }

    /**
     * @param mixed $article_insert_time
     */
    public function setArticleInsertTime($article_insert_time)
    {
        $this->article_insert_time = $article_insert_time;
    }

    /**
     * @return mixed
     */
    public function getArticleUpdateTime()
    {
        return $this->article_update_time;
    }

    /**
     * @param mixed $article_update_time
     */
    public function setArticleUpdateTime($article_update_time)
    {
        $this->article_update_time = $article_update_time;
    }

    /**
     * @return mixed
     */
    public function getArticleLessionId()
    {
        return $this->article_lession_id;
    }

    /**
     * @param mixed $article_lession_id
     */
    public function setArticleLessionId($article_lession_id)
    {
        $this->article_lession_id = $article_lession_id;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecArticle[]|ForexcecArticle
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecArticle
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstByIdAndLocationCountryCode($id, $location_country_code)
    {
        return ForexcecArticle::findFirst(array(
            'article_id = :id: AND article_location_country_code = :location_country_code:',
            'bind' => array(
                'id' => $id,
                'location_country_code' => $location_country_code,
            )
        ));
    }

    public static function findById($id)
    {
        return ForexcecArticle::find(array(
            'article_id = :id:',
            'bind' => array('id' => $id),
        ));
    }


}
