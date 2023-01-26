<?php

namespace Forexceccom\Models;

/**
 * Class ForexcecActivity
 * @property ForexcecUserAgent $userAgent
 */
class ForexcecActivity extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $activity_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_action_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_controller;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_action;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $activity_user_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $activity_date_created;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_message;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $activity_data_log;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_ip;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $activity_user_agent_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_computer_screen;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $activity_browser_window_size;

    /**
     * Method to set the value of field activity_id
     *
     * @param integer $activity_id
     * @return $this
     */
    public function setActivityId($activity_id)
    {
        $this->activity_id = $activity_id;

        return $this;
    }

    /**
     * Method to set the value of field activity_action_id
     *
     * @param string $activity_action_id
     * @return $this
     */
    public function setActivityActionId($activity_action_id)
    {
        $this->activity_action_id = $activity_action_id;

        return $this;
    }

    /**
     * Method to set the value of field activity_controller
     *
     * @param string $activity_controller
     * @return $this
     */
    public function setActivityController($activity_controller)
    {
        $this->activity_controller = $activity_controller;

        return $this;
    }

    /**
     * Method to set the value of field activity_action
     *
     * @param string $activity_action
     * @return $this
     */
    public function setActivityAction($activity_action)
    {
        $this->activity_action = $activity_action;

        return $this;
    }

    /**
     * Method to set the value of field activity_user_id
     *
     * @param integer $activity_user_id
     * @return $this
     */
    public function setActivityUserId($activity_user_id)
    {
        $this->activity_user_id = $activity_user_id;

        return $this;
    }

    /**
     * Method to set the value of field activity_date_created
     *
     * @param integer $activity_date_created
     * @return $this
     */
    public function setActivityDateCreated($activity_date_created)
    {
        $this->activity_date_created = $activity_date_created;

        return $this;
    }

    /**
     * Method to set the value of field activity_message
     *
     * @param string $activity_message
     * @return $this
     */
    public function setActivityMessage($activity_message)
    {
        $this->activity_message = $activity_message;

        return $this;
    }

    /**
     * Method to set the value of field activity_data_log
     *
     * @param string $activity_data_log
     * @return $this
     */
    public function setActivityDataLog($activity_data_log)
    {
        $this->activity_data_log = $activity_data_log;

        return $this;
    }

    /**
     * Method to set the value of field activity_ip
     *
     * @param string $activity_ip
     * @return $this
     */
    public function setActivityIp($activity_ip)
    {
        $this->activity_ip = $activity_ip;

        return $this;
    }

    /**
     * Method to set the value of field activity_user_agent_id
     *
     * @param integer $activity_user_agent_id
     * @return $this
     */
    public function setActivityUserAgentId($activity_user_agent_id)
    {
        $this->activity_user_agent_id = $activity_user_agent_id;

        return $this;
    }

    /**
     * Method to set the value of field activity_computer_screen
     *
     * @param string $activity_computer_screen
     * @return $this
     */
    public function setActivityComputerScreen($activity_computer_screen)
    {
        $this->activity_computer_screen = $activity_computer_screen;

        return $this;
    }

    /**
     * Method to set the value of field activity_browser_window_size
     *
     * @param string $activity_browser_window_size
     * @return $this
     */
    public function setActivityBrowserWindowSize($activity_browser_window_size)
    {
        $this->activity_browser_window_size = $activity_browser_window_size;

        return $this;
    }

    /**
     * Returns the value of field activity_id
     *
     * @return integer
     */
    public function getActivityId()
    {
        return $this->activity_id;
    }

    /**
     * Returns the value of field activity_action_id
     *
     * @return string
     */
    public function getActivityActionId()
    {
        return $this->activity_action_id;
    }

    /**
     * Returns the value of field activity_controller
     *
     * @return string
     */
    public function getActivityController()
    {
        return $this->activity_controller;
    }

    /**
     * Returns the value of field activity_action
     *
     * @return string
     */
    public function getActivityAction()
    {
        return $this->activity_action;
    }

    /**
     * Returns the value of field activity_user_id
     *
     * @return integer
     */
    public function getActivityUserId()
    {
        return $this->activity_user_id;
    }

    /**
     * Returns the value of field activity_date_created
     *
     * @return integer
     */
    public function getActivityDateCreated()
    {
        return $this->activity_date_created;
    }

    /**
     * Returns the value of field activity_message
     *
     * @return string
     */
    public function getActivityMessage()
    {
        return $this->activity_message;
    }

    /**
     * Returns the value of field activity_data_log
     *
     * @return string
     */
    public function getActivityDataLog()
    {
        return $this->activity_data_log;
    }

    /**
     * Returns the value of field activity_ip
     *
     * @return string
     */
    public function getActivityIp()
    {
        return $this->activity_ip;
    }

    /**
     * Returns the value of field activity_user_agent_id
     *
     * @return integer
     */
    public function getActivityUserAgentId()
    {
        return $this->activity_user_agent_id;
    }

    /**
     * Returns the value of field activity_computer_screen
     *
     * @return string
     */
    public function getActivityComputerScreen()
    {
        return $this->activity_computer_screen;
    }

    /**
     * Returns the value of field activity_browser_window_size
     *
     * @return string
     */
    public function getActivityBrowserWindowSize()
    {
        return $this->activity_browser_window_size;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasOne(
            'activity_user_agent_id',
            'Forexceccom\Models\ForexcecUserAgent',
            'agent_id',
            [
                'alias' => 'userAgent'
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
        return 'forexcec_activity';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecActivity[]|ForexcecActivity
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecActivity
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
