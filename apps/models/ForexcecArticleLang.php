<?php

namespace Forexceccom\Models;

class ForexcecArticleLang extends \Phalcon\Mvc\Model
{
    protected $article_id;
    protected $article_lang_code;
    protected $article_location_country_code;
    protected $article_name;
    protected $article_title;
    protected $article_meta_keyword;
    protected $article_meta_description;
    protected $article_icon;
    protected $article_meta_image;
    protected $article_summary;
    protected $article_content;

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
    public function getArticleLangCode()
    {
        return $this->article_lang_code;
    }

    /**
     * @param mixed $article_lang_code
     */
    public function setArticleLangCode($article_lang_code)
    {
        $this->article_lang_code = $article_lang_code;
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
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecArticleLang[]|ForexcecArticleLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecArticleLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findByIdAndLocationCountryCode($article_id, $location_country_code)
    {
        return ForexcecArticleLang::find(array(
            "article_id =:ID: AND article_location_country_code = :location_country_code:",
            'bind' => array('ID' => $article_id, 'location_country_code' => $location_country_code)
        ));
    }

    public static function findFirstById($article_id)
    {
        return ForexcecArticleLang::findFirst(array(
            "article_id =:ID:",
            'bind' => array('ID' => $article_id)
        ));
    }

    public static function findById($article_id)
    {
        return ForexcecArticleLang::find(array(
            "article_id =:ID:",
            'bind' => array('ID' => $article_id)
        ));
    }
}