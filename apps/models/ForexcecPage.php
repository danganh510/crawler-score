<?php

namespace Forexceccom\Models;

class ForexcecPage extends \Phalcon\Mvc\Model
{
    const GENERAL = 'general';

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
     * @Column(type="string", length=5, nullable=false)
     */
    protected $page_location_country_code;

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
     * @Column(type="string", length=255, nullable=false)
     */
    protected $page_meta_keyword;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $page_style;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $page_content;

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
     * Method to set the value of field page_location_country_code
     *
     * @param string $page_location_country_code
     * @return $this
     */
    public function setPageLocationCountryCode($page_location_country_code)
    {
        $this->page_location_country_code = $page_location_country_code;

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
     * Returns the value of field page_id
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Returns the value of field page_location_country_code
     *
     * @return string
     */
    public function getPageLocationCountryCode()
    {
        return $this->page_location_country_code;
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
     * Returns the value of field page_meta_keyword
     *
     * @return string
     */
    public function getPageMetaKeyword()
    {
        return $this->page_meta_keyword;
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
    public function getPageContent($langString = null, $txt_lead_form = null)
    {
        $page_content = $this->page_content;
        if (is_string($page_content) && is_string($langString)) {
            $page_content = str_replace('|||LANG|||', $langString, $page_content);
        }
        if (is_string($page_content) && is_string($txt_lead_form)) {
            $page_content = str_replace('|||LEADFORM|||', $txt_lead_form, $page_content);
        }
        return $page_content;
    }
    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("sandbox_Forexceccomcompanycorpcom_new");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_page';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecPage[]|ForexcecPage
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecPage
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeValidation()
    {
        if (empty($this->page_name)) {
            $this->page_name = new \Phalcon\Db\RawValue('\'\'');
        }
        if (empty($this->page_title)) {
            $this->page_title = new \Phalcon\Db\RawValue('\'\'');
        }
        if (empty($this->page_keyword)) {
            $this->page_keyword = new \Phalcon\Db\RawValue('\'\'');
        }
        if (empty($this->page_meta_description)) {
            $this->page_meta_description = new \Phalcon\Db\RawValue('\'\'');
        }
        if (empty($this->page_meta_keyword)) {
            $this->page_meta_keyword = new \Phalcon\Db\RawValue('\'\'');
        }
        if (empty($this->page_style)) {
            $this->page_style = new \Phalcon\Db\RawValue('\'\'');
        }
        if (empty($this->page_content)) {
            $this->page_content = new \Phalcon\Db\RawValue('\'\'');
        }
    }

    public static function findFirstById($id)
    {
        return ForexcecPage::findFirst(array(
            'page_id = :id:',
            'bind' => array('id' => $id)
        ));
    }

    public static function findFirstByIdAndLocationCountryCode($id, $country_code)
    {
        return ForexcecPage::findFirst(array(
            'page_id = :id: AND page_location_country_code = :country_code:',
            'bind' => array('id' => $id, 'country_code' => $country_code)
        ));
    }

    public static function findById($id)
    {
        return ForexcecPage::find(array(
            'page_id = :id:',
            'bind' => array('id' => $id),
        ));
    }
}
