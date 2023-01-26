<?php

namespace Forexceccom\Models;

/**
 * Class ForexcecContactus
 * @property ForexcecActivity $activity
 */
class ForexcecContactus extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $contact_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_number;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $contact_country;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $contact_communication_channel_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $contact_communication_channel_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $contact_communication_channel_number;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $contact_company;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $contact_comment;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $contact_insert_time;

    /**
     * Method to set the value of field contact_id
     *
     * @param integer $contact_id
     * @return $this
     */
    public function setContactId($contact_id)
    {
        $this->contact_id = $contact_id;

        return $this;
    }

    /**
     * Method to set the value of field contact_name
     *
     * @param string $contact_name
     * @return $this
     */
    public function setContactName($contact_name)
    {
        $this->contact_name = $contact_name;

        return $this;
    }

    /**
     * Method to set the value of field contact_email
     *
     * @param string $contact_email
     * @return $this
     */
    public function setContactEmail($contact_email)
    {
        $this->contact_email = $contact_email;

        return $this;
    }

    /**
     * Method to set the value of field contact_number
     *
     * @param string $contact_number
     * @return $this
     */
    public function setContactNumber($contact_number)
    {
        $this->contact_number = $contact_number;

        return $this;
    }

    /**
     * Method to set the value of field contact_country
     *
     * @param string $contact_country
     * @return $this
     */
    public function setContactCountry($contact_country)
    {
        $this->contact_country = $contact_country;

        return $this;
    }

    /**
     * Method to set the value of field contact_communication_channel_id
     *
     * @param integer $contact_communication_channel_id
     * @return $this
     */
    public function setContactCommunicationChannelId($contact_communication_channel_id)
    {
        $this->contact_communication_channel_id = $contact_communication_channel_id;

        return $this;
    }

    /**
     * Method to set the value of field contact_communication_channel_name
     *
     * @param string $contact_communication_channel_name
     * @return $this
     */
    public function setContactCommunicationChannelName($contact_communication_channel_name)
    {
        $this->contact_communication_channel_name = $contact_communication_channel_name;

        return $this;
    }

    /**
     * Method to set the value of field contact_communication_channel_number
     *
     * @param string $contact_communication_channel_number
     * @return $this
     */
    public function setContactCommunicationChannelNumber($contact_communication_channel_number)
    {
        $this->contact_communication_channel_number = $contact_communication_channel_number;

        return $this;
    }

    /**
     * Method to set the value of field contact_company
     *
     * @param string $contact_company
     * @return $this
     */
    public function setContactCompany($contact_company)
    {
        $this->contact_company = $contact_company;

        return $this;
    }

    /**
     * Method to set the value of field contact_comment
     *
     * @param string $contact_comment
     * @return $this
     */
    public function setContactComment($contact_comment)
    {
        $this->contact_comment = $contact_comment;

        return $this;
    }

    /**
     * Method to set the value of field contact_insert_time
     *
     * @param integer $contact_insert_time
     * @return $this
     */
    public function setContactInsertTime($contact_insert_time)
    {
        $this->contact_insert_time = $contact_insert_time;

        return $this;
    }

    /**
     * Returns the value of field contact_id
     *
     * @return integer
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    /**
     * Returns the value of field contact_name
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contact_name;
    }

    /**
     * Returns the value of field contact_email
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * Returns the value of field contact_number
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contact_number;
    }

    /**
     * Returns the value of field contact_country
     *
     * @return string
     */
    public function getContactCountry()
    {
        return $this->contact_country;
    }

    /**
     * Returns the value of field contact_communication_channel_id
     *
     * @return integer
     */
    public function getContactCommunicationChannelId()
    {
        return $this->contact_communication_channel_id;
    }

    /**
     * Returns the value of field contact_communication_channel_name
     *
     * @return string
     */
    public function getContactCommunicationChannelName()
    {
        return $this->contact_communication_channel_name;
    }

    /**
     * Returns the value of field contact_communication_channel_number
     *
     * @return string
     */
    public function getContactCommunicationChannelNumber()
    {
        return $this->contact_communication_channel_number;
    }

    /**
     * Returns the value of field contact_company
     *
     * @return string
     */
    public function getContactCompany()
    {
        return $this->contact_company;
    }

    /**
     * Returns the value of field contact_comment
     *
     * @return string
     */
    public function getContactComment()
    {
        return $this->contact_comment;
    }

    /**
     * Returns the value of field contact_insert_time
     *
     * @return integer
     */
    public function getContactInsertTime()
    {
        return $this->contact_insert_time;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_contactus';
    }

    public function initialize()
    {
        $this->hasOne(
            'contact_id',
            'Forexceccom\Models\ForexcecActivity',
            'activity_action_id',
            [
                'alias' => 'activity',
                'params' => [
                    'conditions' => 'activity_controller = "contactus"',
                ]
            ]
        );
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecContactus[]|ForexcecContactus
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecContactus
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById ($id) {
        return ForexcecContactus::findFirst(array(
            'contact_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
}
