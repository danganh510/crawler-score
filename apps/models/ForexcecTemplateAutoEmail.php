<?php

namespace Forexceccom\Models;

class ForexcecTemplateAutoEmail extends \Phalcon\Mvc\Model
{

    const ARRAY_FORM = [
        'leadform' => 'LEAD FORM',
        'api_lw' => 'LW USER',
    ];

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $email_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $email_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $email_subject;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $email_content;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $email_form;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $email_status;

    /**
     *
     * @var integer
     * @Column(type="integer", length=5, nullable=true)
     */
    protected $email_day_send;

    /**
     * Method to set the value of field email_id
     *
     * @param integer $email_id
     * @return $this
     */
    public function setEmailId($email_id)
    {
        $this->email_id = $email_id;

        return $this;
    }

    /**
     * Method to set the value of field email_type
     *
     * @param string $email_type
     * @return $this
     */
    public function setEmailType($email_type)
    {
        $this->email_type = $email_type;

        return $this;
    }

    /**
     * Method to set the value of field email_subject
     *
     * @param string $email_subject
     * @return $this
     */
    public function setEmailSubject($email_subject)
    {
        $this->email_subject = $email_subject;

        return $this;
    }

    /**
     * Method to set the value of field email_content
     *
     * @param string $email_content
     * @return $this
     */
    public function setEmailContent($email_content)
    {
        $this->email_content = $email_content;

        return $this;
    }

    /**
     * Method to set the value of field email_form
     *
     * @param string $email_form
     * @return $this
     */
    public function setEmailForm($email_form)
    {
        $this->email_form = $email_form;

        return $this;
    }

    /**
     * Method to set the value of field email_status
     *
     * @param string $email_status
     * @return $this
     */
    public function setEmailStatus($email_status)
    {
        $this->email_status = $email_status;

        return $this;
    }

    /**
     * Method to set the value of field email_day_send
     *
     * @param integer $email_day_send
     * @return $this
     */
    public function setEmailDaySend($email_day_send)
    {
        $this->email_day_send = $email_day_send;

        return $this;
    }

    /**
     * Returns the value of field email_id
     *
     * @return integer
     */
    public function getEmailId()
    {
        return $this->email_id;
    }

    /**
     * Returns the value of field email_type
     *
     * @return string
     */
    public function getEmailType()
    {
        return $this->email_type;
    }

    /**
     * Returns the value of field email_subject
     *
     * @return string
     */
    public function getEmailSubject()
    {
        return $this->email_subject;
    }

    /**
     * Returns the value of field email_content
     *
     * @return string
     */
    public function getEmailContent()
    {
        return $this->email_content;
    }

    /**
     * Returns the value of field email_form
     *
     * @return string
     */
    public function getEmailForm()
    {
        return $this->email_form;
    }

    /**
     * Returns the value of field email_status
     *
     * @return string
     */
    public function getEmailStatus()
    {
        return $this->email_status;
    }

    /**
     * Returns the value of field email_day_send
     *
     * @return integer
     */
    public function getEmailDaySend()
    {
        return $this->email_day_send;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("tmp");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_template_auto_email';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecTemplateAutoEmail[]|ForexcecTemplateAutoEmail
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecTemplateAutoEmail
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
