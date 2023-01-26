<?php

namespace Forexceccom\Models;

class ForexcecBannerLang extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $banner_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $banner_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $banner_title;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $banner_subtitle;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $banner_content;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $banner_image;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $banner_image_mobile;

    /**
     * Method to set the value of field banner_id
     *
     * @param integer $banner_id
     * @return $this
     */
    public function setBannerId($banner_id)
    {
        $this->banner_id = $banner_id;

        return $this;
    }

    /**
     * Method to set the value of field banner_lang_code
     *
     * @param string $banner_lang_code
     * @return $this
     */
    public function setBannerLangCode($banner_lang_code)
    {
        $this->banner_lang_code = $banner_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field banner_title
     *
     * @param string $banner_title
     * @return $this
     */
    public function setBannerTitle($banner_title)
    {
        $this->banner_title = $banner_title;

        return $this;
    }

    /**
     * Method to set the value of field banner_subtitle
     *
     * @param string $banner_subtitle
     * @return $this
     */
    public function setBannerSubTitle($banner_subtitle)
    {
        $this->banner_subtitle = $banner_subtitle;

        return $this;
    }

    /**
     * Method to set the value of field banner_content
     *
     * @param string $banner_content
     * @return $this
     */
    public function setBannerContent($banner_content)
    {
        $this->banner_content = $banner_content;

        return $this;
    }

    /**
     * Method to set the value of field banner_image
     *
     * @param string $banner_image
     * @return $this
     */
    public function setBannerImage($banner_image)
    {
        $this->banner_image = $banner_image;

        return $this;
    }

    /**
     * Method to set the value of field banner_image_mobile
     *
     * @param string $banner_image_mobile
     * @return $this
     */
    public function setBannerImageMobile($banner_image_mobile)
    {
        $this->banner_image_mobile = $banner_image_mobile;

        return $this;
    }

    /**
     * Returns the value of field banner_id
     *
     * @return integer
     */
    public function getBannerId()
    {
        return $this->banner_id;
    }

    /**
     * Returns the value of field banner_lang_code
     *
     * @return string
     */
    public function getBannerLangCode()
    {
        return $this->banner_lang_code;
    }

    /**
     * Returns the value of field banner_title
     *
     * @return string
     */
    public function getBannerTitle()
    {
        return $this->banner_title;
    }

    /**
     * Returns the value of field banner_subtitle
     *
     * @return string
     */
    public function getBannerSubtitle()
    {
        return $this->banner_subtitle;
    }


    /**
     * Returns the value of field banner_content
     *
     * @return string
     */
    public function getBannerContent($langString = null)
    {
        if (is_string($this->banner_content) && is_string($langString)) {
            return str_replace('|||LANG|||', $langString, $this->banner_content);
        }
        return $this->banner_content;
    }

    /**
     * Returns the value of field banner_image
     *
     * @return string
     */
    public function getBannerImage()
    {
        return $this->banner_image;
    }

    /**
     * Returns the value of field banner_image_mobile
     *
     * @return string
     */
    public function getBannerImageMobile()
    {
        return $this->banner_image_mobile;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_banner_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecBannerLang[]|ForexcecBannerLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecBannerLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecBannerLang[]|ForexcecBannerLang
     */
    public static function findById($banner_id)
    {
        return ForexcecBannerLang::find(array(
            "banner_id =:ID:",
            'bind' => array('ID' => $banner_id)
        ));
    }

}