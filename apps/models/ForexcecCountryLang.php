<?php

namespace Forexceccom\Models;

class ForexcecCountryLang extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="string", length=3, nullable=false)
     */
    protected $country_code;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $country_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $country_nationality;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $country_name;

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
     * Method to set the value of field country_lang_code
     *
     * @param string $country_lang_code
     * @return $this
     */
    public function setCountryLangCode($country_lang_code)
    {
        $this->country_lang_code = $country_lang_code;

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
     * Returns the value of field country_code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * Returns the value of field country_lang_code
     *
     * @return string
     */
    public function getCountryLangCode()
    {
        return $this->country_lang_code;
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
     * Returns the value of field country_name
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->country_name;
    }
    
    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("Forexceccomcompanycorp_com_new");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_country_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCountryLang[]|ForexcecCountryLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCountryLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findByCode($code)
    {
        return ForexcecCountryLang::find(array(
            "country_code =:code:",
            'bind' => array('code' => $code)
        ));
    }
}
