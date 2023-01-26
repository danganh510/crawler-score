<?php

namespace Forexceccom\Models;

class ForexcecCountry extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $country_id;

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $country_area_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $country_name;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=true)
     */
    protected $country_code;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $country_order;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $country_nationality;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $country_active;

    /**
     * Method to set the value of field country_id
     *
     * @param integer $country_id
     * @return $this
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;

        return $this;
    }

    /**
     * Method to set the value of field country_area_id
     *
     * @param integer $country_area_id
     * @return $this
     */
    public function setCountryAreaId($country_area_id)
    {
        $this->country_area_id = $country_area_id;

        return $this;
    }

    /**
     * Method to set the value of field country_name
     *
     * @param string $country_name
     * @return $this
     */
    public function setCountryName($country_name)
    {
        $this->country_name = $country_name;

        return $this;
    }

    /**
     * Method to set the value of field country_code
     *
     * @param string $country_code
     * @return $this
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;

        return $this;
    }

    /**
     * Method to set the value of field country_order
     *
     * @param integer $country_order
     * @return $this
     */
    public function setCountryOrder($country_order)
    {
        $this->country_order = $country_order;

        return $this;
    }

    /**
     * Method to set the value of field country_nationality
     *
     * @param string $country_nationality
     * @return $this
     */
    public function setCountryNationality($country_nationality)
    {
        $this->country_nationality = $country_nationality;

        return $this;
    }

    /**
     * Method to set the value of field country_active
     *
     * @param string $country_active
     * @return $this
     */
    public function setCountryActive($country_active)
    {
        $this->country_active = $country_active;

        return $this;
    }

    /**
     * Returns the value of field country_id
     *
     * @return integer
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Returns the value of field country_area_id
     *
     * @return integer
     */
    public function getCountryAreaId()
    {
        return $this->country_area_id;
    }

    /**
     * Returns the value of field country_name
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->country_name;
    }

    /**
     * Returns the value of field country_code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * Returns the value of field country_order
     *
     * @return integer
     */
    public function getCountryOrder()
    {
        return $this->country_order;
    }

    /**
     * Returns the value of field country_nationality
     *
     * @return string
     */
    public function getCountryNationality()
    {
        return $this->country_nationality;
    }

    /**
     * Returns the value of field country_active
     *
     * @return string
     */
    public function getCountryActive()
    {
        return $this->country_active;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("Forexceccom");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_country';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCountry[]|ForexcecCountry
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCountry
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $countryId
     * @return ForexcecCountry
     */
    public static function findFirstById($countryId)
    {
        return ForexcecCountry::findFirst(array(
            "country_id =:ID:",
            'bind' => array('ID' => $countryId)
        ));
    }
}
