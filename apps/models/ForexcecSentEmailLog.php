<?php

namespace Forexceccom\Models;

class ForexcecSentEmailLog extends \Phalcon\Mvc\Model
{
    const FORM_LEADFORM = 'leadform';
    const FORM_LW_USER = 'api_lw';
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $sent_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $sent_email;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $sent_email_type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $sent_status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $sent_is_subscribe;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $sent_log_leadform;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $sent_log_lw_user;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $sent_log_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $sent_update_time;


    /**
     *
     * @var integer
     * @Column(type="integer", length=12, nullable=false)
     */
    protected $sent_insert_time;

    /**
     * Method to set the value of field sent_id
     *
     * @param integer $sent_id
     * @return $this
     */
    public function setSentId($sent_id)
    {
        $this->sent_id = $sent_id;

        return $this;
    }

    /**
     * Method to set the value of field sent_email
     *
     * @param string $sent_email
     * @return $this
     */
    public function setSentEmail($sent_email)
    {
        $this->sent_email = $sent_email;

        return $this;
    }

    /**
     * Method to set the value of field sent_email_type
     *
     * @param string $sent_email_type
     * @return $this
     */
    public function setSentEmailType($sent_email_type)
    {
        $this->sent_email_type = $sent_email_type;

        return $this;
    }

    /**
     * Method to set the value of field sent_status
     *
     * @param string $sent_status
     * @return $this
     */
    public function setSentStatus($sent_status)
    {
        $this->sent_status = $sent_status;

        return $this;
    }

    /**
     * Method to set the value of field sent_is_subscribe
     *
     * @param string $sent_is_subscribe
     * @return $this
     */
    public function setSentIsSubcribe($sent_is_subscribe)
    {
        $this->sent_is_subscribe = $sent_is_subscribe;

        return $this;
    }

    /**
     * Method to set the value of field sent_log_leadform
     *
     * @param string $sent_log_leadform
     * @return $this
     */
    public function setSentLogLeadform($sent_log_leadform)
    {
        $this->sent_log_leadform = $sent_log_leadform;

        return $this;
    }

    /**
     * Method to set the value of field sent_log_lw_user
     *
     * @param string $sent_log_lw_user
     * @return $this
     */
    public function setSentLogLwUser($sent_log_lw_user)
    {
        $this->sent_log_lw_user = $sent_log_lw_user;

        return $this;
    }

    /**
     * Method to set the value of field sent_log_time
     *
     * @param string $sent_log_time
     * @return $this
     */
    public function setSentLogTime($sent_log_time)
    {
        $this->sent_log_time = $sent_log_time;

        return $this;
    }

    /**
     * Method to set the value of field sent_update_time
     *
     * @param integer $sent_update_time
     * @return $this
     */
    public function setSentUpdateTime($sent_update_time)
    {
        $this->sent_update_time = $sent_update_time;

        return $this;
    }

    /**
     * Method to set the value of field sent_insert_time
     *
     * @param integer $sent_insert_time
     * @return $this
     */
    public function setSentInsertTime($sent_insert_time)
    {
        $this->sent_insert_time = $sent_insert_time;

        return $this;
    }

    /**
     * Returns the value of field sent_id
     *
     * @return integer
     */
    public function getSentId()
    {
        return $this->sent_id;
    }

    /**
     * Returns the value of field sent_email
     *
     * @return string
     */
    public function getSentEmail()
    {
        return $this->sent_email;
    }

    /**
     * Returns the value of field sent_email_type
     *
     * @return string
     */
    public function getSentEmailType()
    {
        return $this->sent_email_type;
    }

    /**
     * Returns the value of field sent_status
     *
     * @return string
     */
    public function getSentStatus()
    {
        return $this->sent_status;
    }
    /**
     * Returns the value of field sent_is_subscribe
     *
     * @return string
     */
    public function getSentIsSubscribe()
    {
        return $this->sent_is_subscribe;
    }

    /**
     * Returns the value of field sent_log_leadform
     *
     * @return string
     */
    public function getSentLogLeadform()
    {
        return $this->sent_log_leadform;
    }

    /**
     * Returns the value of field sent_log_lw_user
     *
     * @return string
     */
    public function getSentLogLwUser()
    {
        return $this->sent_log_lw_user;
    }

    /**
     * Returns the value of field sent_log_time
     *
     * @return string
     */
    public function getSentLogTime()
    {
        return $this->sent_log_time;
    }

    /**
     * Returns the value of field sent_update_time
     *
     * @return integer
     */
    public function getSentUpdateTime()
    {
        return $this->sent_update_time;
    }

    /**
     * Returns the value of field sent_insert_time
     *
     * @return integer
     */
    public function getSentInsertTime()
    {
        return $this->sent_insert_time;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("forexceccom");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_sent_email_log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecSentEmailLog[]|ForexcecSentEmailLog
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecSentEmailLog
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
