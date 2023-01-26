<?php

namespace Forexceccom\Models;

class ForexcecPartnership extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $partnership_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $partnership_first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $partnership_last_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $partnership_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $partnership_number;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $partnership_country;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $partnership_communication_channel_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $partnership_communication_channel_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $partnership_communication_channel_number;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $partnership_type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $partnership_comment;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $partnership_insert_time;

    /**
     * Method to set the value of field partnership_id
     *
     * @param integer $partnership_id
     * @return $this
     */
    public function setPartnershipId($partnership_id)
    {
        $this->partnership_id = $partnership_id;

        return $this;
    }

    /**
     * Method to set the value of field partnership_first_name
     *
     * @param string $partnership_first_name
     * @return $this
     */
    public function setPartnershipFirstName($partnership_first_name)
    {
        $this->partnership_first_name = $partnership_first_name;

        return $this;
    }

    /**
     * Method to set the value of field partnership_last_name
     *
     * @param string $partnership_last_name
     * @return $this
     */
    public function setPartnershipLastName($partnership_last_name)
    {
        $this->partnership_last_name = $partnership_last_name;

        return $this;
    }

    /**
     * Method to set the value of field partnership_email
     *
     * @param string $partnership_email
     * @return $this
     */
    public function setPartnershipEmail($partnership_email)
    {
        $this->partnership_email = $partnership_email;

        return $this;
    }

    /**
     * Method to set the value of field partnership_number
     *
     * @param string $partnership_number
     * @return $this
     */
    public function setPartnershipNumber($partnership_number)
    {
        $this->partnership_number = $partnership_number;

        return $this;
    }

    /**
     * Method to set the value of field partnership_country
     *
     * @param string $partnership_country
     * @return $this
     */
    public function setPartnershipCountry($partnership_country)
    {
        $this->partnership_country = $partnership_country;

        return $this;
    }

    /**
     * Method to set the value of field partnership_communication_channel_id
     *
     * @param integer $partnership_communication_channel_id
     * @return $this
     */
    public function setPartnershipCommunicationChannelId($partnership_communication_channel_id)
    {
        $this->partnership_communication_channel_id = $partnership_communication_channel_id;

        return $this;
    }

    /**
     * Method to set the value of field partnership_communication_channel_name
     *
     * @param string $partnership_communication_channel_name
     * @return $this
     */
    public function setPartnershipCommunicationChannelName($partnership_communication_channel_name)
    {
        $this->partnership_communication_channel_name = $partnership_communication_channel_name;

        return $this;
    }

    /**
     * Method to set the value of field partnership_communication_channel_number
     *
     * @param string $partnership_communication_channel_number
     * @return $this
     */
    public function setPartnershipCommunicationChannelNumber($partnership_communication_channel_number)
    {
        $this->partnership_communication_channel_number = $partnership_communication_channel_number;

        return $this;
    }

    /**
     * Method to set the value of field partnership_type
     *
     * @param string $partnership_type
     * @return $this
     */
    public function setPartnershipType($partnership_type)
    {
        $this->partnership_type = $partnership_type;

        return $this;
    }

    /**
     * Method to set the value of field partnership_comment
     *
     * @param string $partnership_comment
     * @return $this
     */
    public function setPartnershipComment($partnership_comment)
    {
        $this->partnership_comment = $partnership_comment;

        return $this;
    }

    /**
     * Method to set the value of field partnership_insert_time
     *
     * @param integer $partnership_insert_time
     * @return $this
     */
    public function setPartnershipInsertTime($partnership_insert_time)
    {
        $this->partnership_insert_time = $partnership_insert_time;

        return $this;
    }

    /**
     * Returns the value of field partnership_id
     *
     * @return integer
     */
    public function getPartnershipId()
    {
        return $this->partnership_id;
    }

    /**
     * Returns the value of field partnership_first_name
     *
     * @return string
     */
    public function getPartnershipFirstName()
    {
        return $this->partnership_first_name;
    }

    /**
     * Returns the value of field partnership_last_name
     *
     * @return string
     */
    public function getPartnershipLastName()
    {
        return $this->partnership_last_name;
    }

    /**
     * Returns the value of field partnership_email
     *
     * @return string
     */
    public function getPartnershipEmail()
    {
        return $this->partnership_email;
    }

    /**
     * Returns the value of field partnership_number
     *
     * @return string
     */
    public function getPartnershipNumber()
    {
        return $this->partnership_number;
    }

    /**
     * Returns the value of field partnership_country
     *
     * @return string
     */
    public function getPartnershipCountry()
    {
        return $this->partnership_country;
    }

    /**
     * Returns the value of field partnership_communication_channel_id
     *
     * @return integer
     */
    public function getPartnershipCommunicationChannelId()
    {
        return $this->partnership_communication_channel_id;
    }

    /**
     * Returns the value of field partnership_communication_channel_name
     *
     * @return string
     */
    public function getPartnershipCommunicationChannelName()
    {
        return $this->partnership_communication_channel_name;
    }

    /**
     * Returns the value of field partnership_communication_channel_number
     *
     * @return string
     */
    public function getPartnershipCommunicationChannelNumber()
    {
        return $this->partnership_communication_channel_number;
    }

    /**
     * Returns the value of field partnership_type
     *
     * @return string
     */
    public function getPartnershipType()
    {
        return $this->partnership_type;
    }

    /**
     * Returns the value of field partnership_comment
     *
     * @return string
     */
    public function getPartnershipComment()
    {
        return $this->partnership_comment;
    }

    /**
     * Returns the value of field partnership_insert_time
     *
     * @return integer
     */
    public function getPartnershipInsertTime()
    {
        return $this->partnership_insert_time;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_partnership';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecPartnership[]|ForexcecPartnership
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecPartnership
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById ($id) {
        return ForexcecPartnership::findFirst(array(
            'partnership_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
}
