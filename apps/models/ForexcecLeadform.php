<?php
namespace Forexceccom\Models;

class ForexcecLeadform extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $leadform_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $leadform_first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $leadform_last_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $leadform_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $leadform_number;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $leadform_number_verify;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $leadform_nationality;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $leadform_account_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $leadform_insert_time;



    /**
     * Method to set the value of field leadform_id
     *
     * @param integer $leadform_id
     * @return $this
     */
    public function setLeadformId($leadform_id)
    {
        $this->leadform_id = $leadform_id;

        return $this;
    }

    /**
     * Method to set the value of field leadform_first_name
     *
     * @param string $leadform_first_name
     * @return $this
     */
    public function setLeadformFirstName($leadform_first_name)
    {
        $this->leadform_first_name = $leadform_first_name;

        return $this;
    }

    /**
     * Method to set the value of field leadform_last_name
     *
     * @param string $leadform_last_name
     * @return $this
     */
    public function setLeadformLastName($leadform_last_name)
    {
        $this->leadform_last_name = $leadform_last_name;

        return $this;
    }

    /**
     * Method to set the value of field leadform_email
     *
     * @param string $leadform_email
     * @return $this
     */
    public function setLeadformEmail($leadform_email)
    {
        $this->leadform_email = $leadform_email;

        return $this;
    }

    /**
     * Method to set the value of field leadform_number
     *
     * @param string $leadform_number
     * @return $this
     */
    public function setLeadformNumber($leadform_number)
    {
        $this->leadform_number = $leadform_number;

        return $this;
    }

    /**
     * Method to set the value of field leadform_number_verify
     *
     * @param string $leadform_number_verify
     * @return $this
     */
    public function setLeadformNumberVerify($leadform_number_verify)
    {
        $this->leadform_number_verify = $leadform_number_verify;

        return $this;
    }

    /**
     * Method to set the value of field leadform_nationality
     *
     * @param string $leadform_nationality
     * @return $this
     */
    public function setLeadformNationality($leadform_nationality)
    {
        $this->leadform_nationality = $leadform_nationality;

        return $this;
    }

    /**
     * Method to set the value of field leadform_account_type
     *
     * @param string $leadform_account_type
     * @return $this
     */
    public function setLeadformAccountType($leadform_account_type)
    {
        $this->leadform_account_type = $leadform_account_type;

        return $this;
    }



    /**
     * Method to set the value of field leadform_insert_time
     *
     * @param integer $leadform_insert_time
     * @return $this
     */
    public function setLeadformInsertTime($leadform_insert_time)
    {
        $this->leadform_insert_time = $leadform_insert_time;

        return $this;
    }

    /**
     * Returns the value of field leadform_id
     *
     * @return integer
     */
    public function getLeadformId()
    {
        return $this->leadform_id;
    }

    /**
     * Returns the value of field leadform_first_name
     *
     * @return string
     */
    public function getLeadformFirstName()
    {
        return $this->leadform_first_name;
    }

    /**
     * Returns the value of field leadform_last_name
     *
     * @return string
     */
    public function getLeadformLastName()
    {
        return $this->leadform_last_name;
    }

    /**
     * Returns the value of field leadform_email
     *
     * @return string
     */
    public function getLeadformEmail()
    {
        return $this->leadform_email;
    }

    /**
     * Returns the value of field leadform_number
     *
     * @return string
     */
    public function getLeadformNumber()
    {
        return $this->leadform_number;
    }

    /**
     * Returns the value of field leadform_number_verify
     *
     * @return string
     */
    public function getLeadformNumberVerify()
    {
        return $this->leadform_number_verify;
    }

    /**
     * Returns the value of field leadform_nationality
     *
     * @return string
     */
    public function getLeadformNationality()
    {
        return $this->leadform_nationality;
    }

    /**
     * Returns the value of field leadform_account_type
     *
     * @return string
     */
    public function getLeadformAccountType()
    {
        return $this->leadform_account_type;
    }


    /**
     * Returns the value of field leadform_insert_time
     *
     * @return integer
     */
    public function getLeadformInsertTime()
    {
        return $this->leadform_insert_time;
    }


    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasOne(
            'leadform_id',
            'Forexceccom\Models\ForexcecActivity',
            'activity_action_id',
            [
                'alias' => 'activity',
                'params' => [
                    'conditions' => 'activity_controller = "leadform"',
                ]
            ]
        );
        $this->hasOne(
            'leadform_email',
            'Forexceccom\Models\ForexcecSentEmailLog',
            'sent_email',
            [
                'alias' => 'sent_email_log',
                'params' => [
                    'conditions' => 'sent_email_type = "leadform"',
                ]
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_leadform';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecLeadform[]|ForexcecLeadform
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecLeadform
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
