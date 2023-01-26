<?php

namespace Forexceccom\Models;

class ForexcecLocation extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $location_id;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=false)
     */
    protected $location_country_code;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $location_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $location_hotline;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_mobile_footer_support;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_footer_content;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_schema_contactpoint;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_schema_social;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_alternate_hreflang;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_footer_social;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $location_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $location_active;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $location_is_public;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $location_is_temp;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $location_cron_time;

    /**
     * Method to set the value of field location_id
     *
     * @param integer $location_id
     * @return $this
     */
    public function setLocationId($location_id)
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Method to set the value of field location_country_code
     *
     * @param string $location_country_code
     * @return $this
     */
    public function setLocationCountryCode($location_country_code)
    {
        $this->location_country_code = $location_country_code;

        return $this;
    }

    /**
     * Method to set the value of field location_lang_code
     *
     * @param string $location_lang_code
     * @return $this
     */
    public function setLocationLangCode($location_lang_code)
    {
        $this->location_lang_code = $location_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field location_hotline
     *
     * @param string $location_hotline
     * @return $this
     */
    public function setLocationHotline($location_hotline)
    {
        $this->location_hotline = $location_hotline;

        return $this;
    }

    /**
     * Method to set the value of field location_mobile_footer_support
     *
     * @param string $location_mobile_footer_support
     * @return $this
     */
    public function setLocationMobileFooterSupport($location_mobile_footer_support)
    {
        $this->location_mobile_footer_support = $location_mobile_footer_support;

        return $this;
    }

    /**
     * Method to set the value of field location_footer_content
     *
     * @param string $location_footer_content
     * @return $this
     */
    public function setLocationFooterContent($location_footer_content)
    {
        $this->location_footer_content = $location_footer_content;

        return $this;
    }

    /**
     * Method to set the value of field location_schema_contactpoint
     *
     * @param string $location_schema_contactpoint
     * @return $this
     */
    public function setLocationSchemaContactpoint($location_schema_contactpoint)
    {
        $this->location_schema_contactpoint = $location_schema_contactpoint;

        return $this;
    }

    /**
     * Method to set the value of field location_schema_social
     *
     * @param string $location_schema_social
     * @return $this
     */
    public function setLocationSchemaSocial($location_schema_social)
    {
        $this->location_schema_social = $location_schema_social;

        return $this;
    }

    /**
     * Method to set the value of field location_alternate_hreflang
     *
     * @param string $location_alternate_hreflang
     * @return $this
     */
    public function setLocationAlternateHrefLang($location_alternate_hreflang)
    {
        $this->location_alternate_hreflang = $location_alternate_hreflang;

        return $this;
    }

    /**
     * Method to set the value of field location_footer_social
     *
     * @param string $location_footer_social
     * @return $this
     */
    public function setLocationFooterSocial($location_footer_social)
    {
        $this->location_footer_social = $location_footer_social;

        return $this;
    }

    /**
     * Method to set the value of field location_order
     *
     * @param integer $location_order
     * @return $this
     */
    public function setLocationOrder($location_order)
    {
        $this->location_order = $location_order;

        return $this;
    }

    /**
     * Method to set the value of field location_active
     *
     * @param string $location_active
     * @return $this
     */
    public function setLocationActive($location_active)
    {
        $this->location_active = $location_active;

        return $this;
    }

    /**
     * Method to set the value of field location_is_public
     *
     * @param string $location_is_public
     * @return $this
     */
    public function setLocationIsPublic($location_is_public)
    {
        $this->location_is_public = $location_is_public;

        return $this;
    }

    /**
     * Method to set the value of field location_is_public
     *
     * @param string $location_is_temp
     * @return $this
     */
    public function setLocationIsTemp($location_is_temp)
    {
        $this->location_is_temp = $location_is_temp;

        return $this;
    }

    /**
     * Method to set the value of field location_cron_time
     *
     * @param integer $location_cron_time
     * @return $this
     */
    public function setLocationCronTime($location_cron_time)
    {
        $this->location_cron_time = $location_cron_time;

        return $this;
    }

    /**
     * Returns the value of field location_id
     *
     * @return integer
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Returns the value of field location_country_code
     *
     * @return string
     */
    public function getLocationCountryCode()
    {
        return $this->location_country_code;
    }

    /**
     * Returns the value of field location_lang_code
     *
     * @return string
     */
    public function getLocationLangCode()
    {
        return $this->location_lang_code;
    }

    /**
     * Returns the value of field location_hotline
     *
     * @return string
     */
    public function getLocationHotline()
    {
        return $this->location_hotline;
    }

    /**
     * Returns the value of field location_mobile_footer_support
     *
     * @return string
     */
    public function getLocationMobileFooterSupport()
    {
        return $this->location_mobile_footer_support;
    }

    /**
     * Returns the value of field location_footer_content
     *
     * @return string
     */
    public function getLocationFooterContent()
    {
        return $this->location_footer_content;
    }

    /**
     * Returns the value of field location_schema_contactpoint
     *
     * @return string
     */
    public function getLocationSchemaContactpoint()
    {
        return $this->location_schema_contactpoint;
    }

    /**
     * Returns the value of field location_schema_social
     *
     * @return string
     */
    public function getLocationSchemaSocial()
    {
        return $this->location_schema_social;
    }

    /**
     * Returns the value of field location_alternate_hreflang
     *
     * @return string
     */
    public function getLocationAlternateHrefLang()
    {
        return $this->location_alternate_hreflang;
    }

    /**
     * Returns the value of field location_footer_social
     *
     * @return string
     */
    public function getLocationFooterSocial()
    {
        return $this->location_footer_social;
    }

    /**
     * Returns the value of field location_order
     *
     * @return integer
     */
    public function getLocationOrder()
    {
        return $this->location_order;
    }

    /**
     * Returns the value of field location_active
     *
     * @return string
     */
    public function getLocationActive()
    {
        return $this->location_active;
    }

    /**
     * Returns the value of field location_is_public
     *
     * @return string
     */
    public function getLocationIsPublic()
    {
        return $this->location_is_public;
    }

    /**
     * Returns the value of field location_is_temp
     *
     * @return string
     */
    public function getLocationIsTemp()
    {
        return $this->location_is_temp;
    }

    /**
     * Returns the value of field location_cron_time
     *
     * @return integer
     */
    public function getLocationCronTime()
    {
        return $this->location_cron_time;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("Forexceccomcompanycorpcom_new");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_location';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecLocation[]|ForexcecLocation
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecLocation
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById($locationId)
    {
        return ForexcecLocation::findFirst(array(
            "location_id =:ID:",
            'bind' => array('ID' => $locationId)
        ));
    }
}
