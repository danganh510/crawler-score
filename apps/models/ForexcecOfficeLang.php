<?php

namespace Forexceccom\Models;

use Phalcon\Db\RawValue;

class ForexcecOfficeLang extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $office_id;
    /**
     *
     * @var string
     */
    protected $office_lang_code;

    /**
     *
     * @var string
     */
    protected $office_name;

    /**
     *
     * @var string
     */
    protected $office_address;


    /**
     * Method to set the value of field office_id
     *
     * @param integer $office_id
     * @return $this
     */
    public function setOfficeId($office_id)
    {
        $this->office_id = $office_id;

        return $this;
    }

    /**
     * Method to set the value of field office_lang_code
     *
     * @param string $office_lang_code
     * @return $this
     */
    public function setOfficeLangCode($office_lang_code)
    {
        $this->office_lang_code = $office_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field office_name
     *
     * @param string $office_name
     * @return $this
     */
    public function setOfficeName($office_name)
    {
        $this->office_name = $office_name;

        return $this;
    }

    /**
     * Method to set the value of field office_address
     *
     * @param string $office_address
     * @return $this
     */
    public function setOfficeAddress($office_address)
    {
        $this->office_address = $office_address;

        return $this;
    }


    /**
     * Returns the value of field office_id
     *
     * @return integer
     */
    public function getOfficeId()
    {
        return $this->office_id;
    }

    /**
     * Returns the value of field office_lang_code
     *
     * @return string
     */
    public function getOfficeLangCode()
    {
        return $this->office_lang_code;
    }

    /**
     * Returns the value of field office_name
     *
     * @return string
     */
    public function getOfficeName()
    {
        return $this->office_name;
    }

    /**
     * Returns the value of field office_address
     *
     * @return string
     */
    public function getOfficeAddress()
    {
        return $this->office_address;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("forexcec_com_vn");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_office_lang';
    }

    public function beforeValidation()
    {
        if (empty($this->office_address)) {
            $this->office_address = new RawValue('\'\'');
        }
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecOfficeLang[]|ForexcecOfficeLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecOfficeLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findById($id)
    {
        return ForexcecOfficeLang::find(array(
            "office_id =:ID:",
            'bind' => array('ID' => $id)
        ));
    }
}
