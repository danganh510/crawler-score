<?php

namespace Forexceccom\Models;

class ForexcecTableTranslate extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $translate_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $translate_language;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $translate_table;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $translate_order;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $translate_cron_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $translate_active;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $translate_reason;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $translate_cron;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $translate_insert_time;

    /**
     * Method to set the value of field translate_id
     *
     * @param integer $translate_id
     * @return $this
     */
    public function setTranslateId($translate_id)
    {
        $this->translate_id = $translate_id;

        return $this;
    }

    /**
     * Method to set the value of field translate_language
     *
     * @param string $translate_language
     * @return $this
     */
    public function setTranslateLanguage($translate_language)
    {
        $this->translate_language = $translate_language;

        return $this;
    }

    /**
     * Method to set the value of field translate_table
     *
     * @param string $translate_table
     * @return $this
     */
    public function setTranslateTable($translate_table)
    {
        $this->translate_table = $translate_table;

        return $this;
    }

    /**
     * Method to set the value of field translate_order
     *
     * @param integer $translate_order
     * @return $this
     */
    public function setTranslateOrder($translate_order)
    {
        $this->translate_order = $translate_order;

        return $this;
    }

    /**
     * Method to set the value of field translate_cron_id
     *
     * @param integer $translate_cron_id
     * @return $this
     */
    public function setTranslateCronId($translate_cron_id)
    {
        $this->translate_cron_id = $translate_cron_id;

        return $this;
    }

    /**
     * Method to set the value of field translate_active
     *
     * @param string $translate_active
     * @return $this
     */
    public function setTranslateActive($translate_active)
    {
        $this->translate_active = $translate_active;

        return $this;
    }

    /**
     * Method to set the value of field translate_reason
     *
     * @param string $translate_reason
     * @return $this
     */
    public function setTranslateReason($translate_reason)
    {
        $this->translate_reason = $translate_reason;

        return $this;
    }

    /**
     * Method to set the value of field translate_cron
     *
     * @param string $translate_cron
     * @return $this
     */
    public function setTranslateCron($translate_cron)
    {
        $this->translate_cron = $translate_cron;

        return $this;
    }

    /**
     * Method to set the value of field translate_insert_time
     *
     * @param integer $translate_insert_time
     * @return $this
     */
    public function setTranslateInsertTime($translate_insert_time)
    {
        $this->translate_insert_time = $translate_insert_time;

        return $this;
    }

    /**
     * Returns the value of field translate_id
     *
     * @return integer
     */
    public function getTranslateId()
    {
        return $this->translate_id;
    }

    /**
     * Returns the value of field translate_language
     *
     * @return string
     */
    public function getTranslateLanguage()
    {
        return $this->translate_language;
    }

    /**
     * Returns the value of field translate_table
     *
     * @return string
     */
    public function getTranslateTable()
    {
        return $this->translate_table;
    }

    /**
     * Returns the value of field translate_order
     *
     * @return integer
     */
    public function getTranslateOrder()
    {
        return $this->translate_order;
    }

    /**
     * Returns the value of field translate_cron_id
     *
     * @return integer
     */
    public function getTranslateCronId()
    {
        return $this->translate_cron_id;
    }

    /**
     * Returns the value of field translate_active
     *
     * @return string
     */
    public function getTranslateActive()
    {
        return $this->translate_active;
    }

    /**
     * Returns the value of field translate_reason
     *
     * @return string
     */
    public function getTranslateReason()
    {
        return $this->translate_reason;
    }

    /**
     * Returns the value of field translate_cron
     *
     * @return string
     */
    public function getTranslateCron()
    {
        return $this->translate_cron;
    }

    /**
     * Returns the value of field translate_insert_time
     *
     * @return integer
     */
    public function getTranslateInsertTime()
    {
        return $this->translate_insert_time;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("offshorecompanycorp");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_table_translate';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecTableTranslate[]|ForexcecTableTranslate
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecTableTranslate
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function getAll()
    {
        return self::findFirst(array(
            "translate_active = 'Y'",
            "order" => 'translate_order ASC',
        ));
    }

    public static function findFirstByCron($translate_cron)
    {
        return self::findFirst(array(
            "translate_cron = :translate_cron: AND translate_active = 'Y'",
            "order" => 'translate_order ASC',
            'bind' => array('translate_cron' => $translate_cron)
        ));
    }

    public static function checkCodeCountry($country_code)
    {
        return self::findFirst(array(
            'translate_language = :COUNTRYCODE:',
            'bind' => array('COUNTRYCODE' => $country_code)
        ));
    }

    public static function findFirstById($id)
    {
        return self::findFirst(array(
            "translate_id=:ID:",
            'bind' => array('ID' => $id)
        ));
    }


}
