<?php

namespace Forexceccom\Models;

class ForexcecListLwUser extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $forexcec_pub_user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $forexcec_email;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $forexcec_real_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $forexcec_account;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $forexcec_is_enable;

    /**
     *
     * @var integer
     * @Column(type="integer", length=30, nullable=false)
     */
    protected $forexcec_last_login_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=false)
     */
    protected $forexcec_register_time;




    /**
     * Method to set the value of field forexcec_pub_user_id
     *
     * @param string $forexcec_pub_user_id
     * @return $this
     */
    public function setForexcecPubUserId($forexcec_pub_user_id)
    {
        $this->forexcec_pub_user_id = $forexcec_pub_user_id;

        return $this;
    }

    /**
     * Method to set the value of field forexcec_email
     *
     * @param string $forexcec_email
     * @return $this
     */
    public function setForexcecEmail($forexcec_email)
    {
        $this->forexcec_email = $forexcec_email;

        return $this;
    }

    /**
     * Method to set the value of field forexcec_real_name
     *
     * @param string $forexcec_real_name
     * @return $this
     */
    public function setForexcecRealName($forexcec_real_name)
    {
        $this->forexcec_real_name = $forexcec_real_name;

        return $this;
    }

    /**
     * Method to set the value of field forexcec_account
     *
     * @param string $forexcec_account
     * @return $this
     */
    public function setForexcecAccount($forexcec_account)
    {
        $this->forexcec_account = $forexcec_account;

        return $this;
    }

    /**
     * Method to set the value of field forexcec_is_enable
     *
     * @param string $forexcec_is_enable
     * @return $this
     */
    public function setForexcecIsEnable($forexcec_is_enable)
    {
        $this->forexcec_is_enable = $forexcec_is_enable;

        return $this;
    }

    /**
     * Method to set the value of field forexcec_last_login_time
     *
     * @param integer $forexcec_last_login_time
     * @return $this
     */
    public function setForexcecLastLoginTime($forexcec_last_login_time)
    {
        $this->forexcec_last_login_time = $forexcec_last_login_time;

        return $this;
    }


    /**
     * Method to set the value of field forexcec_register_time
     *
     * @param integer $forexcec_register_time
     * @return $this
     */
    public function setForexcecRegisterTime($forexcec_register_time)
    {
        $this->forexcec_register_time = $forexcec_register_time;

        return $this;
    }


    /**
     * Returns the value of field forexcec_pub_user_id
     *
     * @return string
     */
    public function getForexcecPubUserId()
    {
        return $this->forexcec_pub_user_id;
    }

    /**
     * Returns the value of field forexcec_email
     *
     * @return string
     */
    public function getForexcecEmail()
    {
        return $this->forexcec_email;
    }

    /**
     * Returns the value of field forexcec_real_name
     *
     * @return string
     */
    public function getForexcecRealName()
    {
        return $this->forexcec_real_name;
    }

    /**
     * Returns the value of field forexcec_account
     *
     * @return string
     */
    public function getForexcecAccount()
    {
        return $this->forexcec_account;
    }

    /**
     * Returns the value of field forexcec_is_enable
     *
     * @return string
     */
    public function getForexcecIsEnable()
    {
        return $this->forexcec_is_enable;
    }

    /**
     * Returns the value of field forexcec_last_login_time
     *
     * @return integer
     */
    public function getForexcecLastLoginTime()
    {
        return $this->forexcec_last_login_time;
    }

    /**
     * Returns the value of field forexcec_register_time
     *
     * @return integer
     */
    public function getForexcecRegisterTime()
    {
        return $this->forexcec_register_time;
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
        return 'forexcec_list_lw_user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecListLwUser[]|ForexcecListLwUser
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecListLwUser
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
