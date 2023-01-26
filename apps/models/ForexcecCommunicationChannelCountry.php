<?php

namespace Forexceccom\Models;

class ForexcecCommunicationChannelCountry extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $communication_channel_id;

    /**
     *
     * @var string
     * @Column(type="string", length=3, nullable=true)
     */
    protected $communication_channel_country_code;

     /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $communication_channel_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $communication_channel_active;

    /**
     * Method to set the value of field communication_channel_id
     *
     * @param integer $communication_channel_id
     * @return $this
     */
    public function setCommunicationChannelId($communication_channel_id)
    {
        $this->communication_channel_id = $communication_channel_id;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_country_code
     *
     * @param string $communication_channel_country_code
     * @return $this
     */
    public function setCommunicationChannelCountryCode($communication_channel_country_code)
    {
        $this->communication_channel_country_code = $communication_channel_country_code;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_order
     *
     * @param integer $communication_channel_order
     * @return $this
     */
    public function setCommunicationChannelOrder($communication_channel_order)
    {
        $this->communication_channel_order = $communication_channel_order;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_active
     *
     * @param string $communication_channel_active
     * @return $this
     */
    public function setCommunicationChannelActive($communication_channel_active)
    {
        $this->communication_channel_active = $communication_channel_active;

        return $this;
    }

    /**
     * Returns the value of field communication_channel_id
     *
     * @return integer
     */
    public function getCommunicationChannelId()
    {
        return $this->communication_channel_id;
    }

    /**
     * Returns the value of field communication_channel_name
     *
     * @return string
     */
    public function getCommunicationChannelCountryCode()
    {
        return $this->communication_channel_country_code;
    }

    /**
     * Returns the value of field communication_channel_order
     *
     * @return integer
     */
    public function getCommunicationChannelOrder()
    {
        return $this->communication_channel_order;
    }

    /**
     * Returns the value of field communication_channel_active
     *
     * @return string
     */
    public function getCommunicationChannelActive()
    {
        return $this->communication_channel_active;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_communication_channel_country';
    }

    public static function findByChannelId($channel_id){
        return ForexcecCommunicationChannelCountry::find(array(
            "communication_channel_id = :channel_id:",
            'bind' => array('channel_id' => $channel_id)
        ));
    }

}
