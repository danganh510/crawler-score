<?php

namespace General\Models;

class LanguageSupportTranslate extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $language_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $language_name;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $language_code;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $language_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $language_active;

    /**
     * Method to set the value of field language_id
     *
     * @param integer $language_id
     * @return $this
     */
    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;

        return $this;
    }

    /**
     * Method to set the value of field language_name
     *
     * @param string $language_name
     * @return $this
     */
    public function setLanguageName($language_name)
    {
        $this->language_name = $language_name;

        return $this;
    }

    /**
     * Method to set the value of field language_code
     *
     * @param string $language_code
     * @return $this
     */
    public function setLanguageCode($language_code)
    {
        $this->language_code = $language_code;

        return $this;
    }

    /**
     * Method to set the value of field language_order
     *
     * @param integer $language_order
     * @return $this
     */
    public function setLanguageOrder($language_order)
    {
        $this->language_order = $language_order;

        return $this;
    }

    /**
     * Method to set the value of field language_active
     *
     * @param string $language_active
     * @return $this
     */
    public function setLanguageActive($language_active)
    {
        $this->language_active = $language_active;

        return $this;
    }

    /**
     * Returns the value of field language_id
     *
     * @return integer
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * Returns the value of field language_name
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->language_name;
    }

    /**
     * Returns the value of field language_code
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * Returns the value of field language_order
     *
     * @return integer
     */
    public function getLanguageOrder()
    {
        return $this->language_order;
    }

    /**
     * Returns the value of field language_active
     *
     * @return string
     */
    public function getLanguageActive()
    {
        return $this->language_active;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db_general');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'language_support_translate';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LanguageSupportTranslate[]|LanguageSupportTranslate
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LanguageSupportTranslate
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function getAll () {
        return LanguageSupportTranslate::find(array(
            "language_active = 'Y'",
            'order' => 'language_code ASC'
        ));
    }
    public static function getAllOtherEn() {
        return $data = LanguageSupportTranslate::find(array(
            "columns" => "language_code",
            "language_code NOT IN ('en') AND language_active = 'Y'",
        ))->toArray();
        return $data ? array_column($data,'language_code'): '';
    }

}
