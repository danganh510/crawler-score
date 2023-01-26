<?php

namespace Forexceccom\Models;

class ForexcecOffice extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $office_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $office_name;


    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $office_position_x;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $office_position_y;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $office_address;


    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $office_phone;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $office_order;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $office_country_code;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $office_active;

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
     * Method to set the value of field office_country_code
     *
     * @param string $office_country_code
     * @return $this
     */
    public function setOfficeCountryCode($office_country_code)
    {
        $this->office_country_code = $office_country_code;

        return $this;
    }

    /**
     * Method to set the value of field office_position_x
     *
     * @param double $office_position_x
     * @return $this
     */
    public function setOfficePositionX($office_position_x)
    {
        $this->office_position_x = $office_position_x;

        return $this;
    }

    /**
     * Method to set the value of field office_position_y
     *
     * @param double $office_position_y
     * @return $this
     */
    public function setOfficePositionY($office_position_y)
    {
        $this->office_position_y = $office_position_y;

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
     * Method to set the value of field office_phone
     *
     * @param string $office_phone
     * @return $this
     */
    public function setOfficePhone($office_phone)
    {
        $this->office_phone = $office_phone;

        return $this;
    }

    /**
     * Method to set the value of field office_order
     *
     * @param integer $office_order
     * @return $this
     */
    public function setOfficeOrder($office_order)
    {
        $this->office_order = $office_order;

        return $this;
    }

    /**
     * Method to set the value of field office_active
     *
     * @param string $office_active
     * @return $this
     */
    public function setOfficeActive($office_active)
    {
        $this->office_active = $office_active;

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
     * Returns the value of field office_name
     *
     * @return string
     */
    public function getOfficeName()
    {
        return $this->office_name;
    }


    /**
     * Returns the value of field office_position_x
     *
     * @return double
     */
    public function getOfficePositionX()
    {
        return $this->office_position_x;
    }

    /**
     * Returns the value of field office_position_y
     *
     * @return double
     */
    public function getOfficePositionY()
    {
        return $this->office_position_y;
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
     * Returns the value of field office_phone
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->office_phone;
    }
    
    /**
     * Returns the value of field office_order
     *
     * @return integer
     */
    public function getOfficeOrder()
    {
        return $this->office_order;
    }

    /**
     * Returns the value of field office_active
     *
     * @return string
     */
    public function getOfficeActive()
    {
        return $this->office_active;
    }

    /**
     * Returns the value of field office_active
     *
     * @return string
     */
    public function getOfficeCountryCode()
    {
        return $this->office_country_code;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_office';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecOffice[]|ForexcecOffice
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecOffice
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById($id)
    {
        return ForexcecOffice::findFirst(array(
            'office_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
}
