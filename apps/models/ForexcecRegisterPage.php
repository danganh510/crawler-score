<?php

namespace Forexceccom\Models;

class ForexcecRegisterPage extends \Phalcon\Mvc\Model
{
    const PAGE_DOMAIN = ['register.forexcec.com','promotion.forexcec.com'];

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $page_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $page_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $page_domain;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=true)
     */
    protected $page_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $page_title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $page_keyword;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $page_meta_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $page_meta_description;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $page_meta_image;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $page_style;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $page_content;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $page_gtm;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $page_default;

    /**
     *
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $page_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $page_active;

    /**
     * Method to set the value of field page_id
     *
     * @param integer $page_id
     * @return $this
     */
    public function setPageId($page_id)
    {
        $this->page_id = $page_id;

        return $this;
    }

    /**
     * Method to set the value of field page_name
     *
     * @param string $page_name
     * @return $this
     */
    public function setPageName($page_name)
    {
        $this->page_name = $page_name;

        return $this;
    }

    /**
     * Method to set the value of field page_domain
     *
     * @param string $page_domain
     * @return $this
     */
    public function setPageDomain($page_domain)
    {
        $this->page_domain = $page_domain;

        return $this;
    }

    /**
     * Method to set the value of field page_lang_code
     *
     * @param string $page_lang_code
     * @return $this
     */
    public function setPageLangCode($page_lang_code)
    {
        $this->page_lang_code = $page_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field page_title
     *
     * @param string $page_title
     * @return $this
     */
    public function setPageTitle($page_title)
    {
        $this->page_title = $page_title;

        return $this;
    }

    /**
     * Method to set the value of field page_keyword
     *
     * @param string $page_keyword
     * @return $this
     */
    public function setPageKeyword($page_keyword)
    {
        $this->page_keyword = $page_keyword;

        return $this;
    }

    /**
     * Method to set the value of field page_meta_keyword
     *
     * @param string $page_meta_keyword
     * @return $this
     */
    public function setPageMetaKeyword($page_meta_keyword)
    {
        $this->page_meta_keyword = $page_meta_keyword;

        return $this;
    }

    /**
     * Method to set the value of field page_meta_description
     *
     * @param string $page_meta_description
     * @return $this
     */
    public function setPageMetaDescription($page_meta_description)
    {
        $this->page_meta_description = $page_meta_description;

        return $this;
    }

    /**
     * Method to set the value of field page_meta_image
     *
     * @param string $page_meta_image
     * @return $this
     */
    public function setPageMetaImage($page_meta_image)
    {
        $this->page_meta_image = $page_meta_image;

        return $this;
    }

    /**
     * Method to set the value of field page_style
     *
     * @param string $page_style
     * @return $this
     */
    public function setPageStyle($page_style)
    {
        $this->page_style = $page_style;

        return $this;
    }

    /**
     * Method to set the value of field page_content
     *
     * @param string $page_content
     * @return $this
     */
    public function setPageContent($page_content)
    {
        $this->page_content = $page_content;

        return $this;
    }

    /**
     * Method to set the value of field page_gtm
     *
     * @param string $page_gtm
     * @return $this
     */
    public function setPageGtm($page_gtm)
    {
        $this->page_gtm = $page_gtm;

        return $this;
    }

    /**
     * Method to set the value of field page_default
     *
     * @param string $page_default
     * @return $this
     */
    public function setPageDefault($page_default)
    {
        $this->page_default = $page_default;

        return $this;
    }

    /**
     * Method to set the value of field page_order
     *
     * @param integer $page_order
     * @return $this
     */
    public function setPageOrder($page_order)
    {
        $this->page_order = $page_order;
        return $this;
    }

    /**
     * Method to set the value of field page_active
     *
     * @param string $page_active
     * @return $this
     */
    public function setPageActive($page_active)
    {
        $this->page_active = $page_active;

        return $this;
    }

    /**
     * Returns the value of field page_id
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Returns the value of field page_name
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->page_name;
    }

    /**
     * Returns the value of field page_domain
     *
     * @return string
     */
    public function getPageDomain()
    {
        return $this->page_domain;
    }

    /**
     * Returns the value of field page_lang_code
     *
     * @return string
     */
    public function getPagelangCode()
    {
        return $this->page_lang_code;
    }

    /**
     * Returns the value of field page_title
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->page_title;
    }

    /**
     * Returns the value of field page_keyword
     *
     * @return string
     */
    public function getPageKeyword()
    {
        return $this->page_keyword;
    }

    /**
     * Returns the value of field page_meta_keyword
     *
     * @return string
     */
    public function getPageMetaKeyword()
    {
        return $this->page_meta_keyword;
    }

    /**
     * Returns the value of field page_meta_description
     *
     * @return string
     */
    public function getPageMetaDescription()
    {
        return $this->page_meta_description;
    }

    /**
     * Returns the value of field page_meta_image
     *
     * @return string
     */
    public function getPageMetaImage()
    {
        return $this->page_meta_image;
    }

    /**
     * Returns the value of field page_style
     *
     * @return string
     */
    public function getPageStyle()
    {
        return $this->page_style;
    }

    /**
     * Returns the value of field page_content
     *
     * @return string
     */
    public function getPageContent()
    {
        return $this->page_content;
    }

    /**
     * Returns the value of field page_gtm
     *
     * @return string
     */
    public function getPageGtm()
    {
        return $this->page_gtm;
    }

    /**
     * Returns the value of field page_default
     *
     * @return string
     */
    public function getPageDefault()
    {
        return $this->page_default;
    }

    /**
     * Returns the value of field page_order
     *
     * @return integer
     */
    public function getPageOrder()
    {
        return $this->page_order;
    }

    /**
     * Returns the value of field page_active
     *
     * @return string
     */
    public function getPageActive()
    {
        return $this->page_active;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//    
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_register_page';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RegisterPage[]|RegisterPage
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RegisterPage
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById($id) {
        return self::findFirst([
            'page_id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

}
