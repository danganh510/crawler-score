<?php

namespace Score\Models;

use Phalcon\Db\RawValue;

class ForexcecRole extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $role_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $role_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $role_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $role_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $role_function;

    /**
     * Method to set the value of field role_id
     *
     * @param integer $role_id
     * @return $this
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    /**
     * Method to set the value of field role_name
     *
     * @param string $role_name
     * @return $this
     */
    public function setRoleName($role_name)
    {
        $this->role_name = $role_name;

        return $this;
    }

    /**
     * Method to set the value of field role_active
     *
     * @param string $role_active
     * @return $this
     */
    public function setRoleActive($role_active)
    {
        $this->role_active = $role_active;

        return $this;
    }

    /**
     * Method to set the value of field role_order
     *
     * @param integer $role_order
     * @return $this
     */
    public function setRoleOrder($role_order)
    {
        $this->role_order = $role_order;

        return $this;
    }

    /**
     * Method to set the value of field role_function
     *
     * @param string $role_function
     * @return $this
     */
    public function setRoleFunction($role_function)
    {
        $this->role_function = $role_function;

        return $this;
    }

    /**
     * Returns the value of field role_id
     *
     * @return integer
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Returns the value of field role_name
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->role_name;
    }

    /**
     * Returns the value of field role_active
     *
     * @return string
     */
    public function getRoleActive()
    {
        return $this->role_active;
    }

    /**
     * Returns the value of field role_order
     *
     * @return integer
     */
    public function getRoleOrder()
    {
        return $this->role_order;
    }

    /**
     * Returns the value of field role_function
     *
     * @return string
     */
    public function getRoleFunction()
    {
        return $this->role_function;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("bincgcom");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'sc_role';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecRole[]|ForexcecRole
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecRole
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public function beforeValidation()
    {
        if (empty($this->role_name)) {
            $this->role_name = new RawValue('\'\'');
        }
        if (empty($this->role_function)) {
            $this->role_function = new RawValue('\'\'');
        }

    }
    public static function getFirstByName($name){
        return self::findFirst(array(
            'role_name = :role_name:',
            'bind' => array('role_name' => $name)
        ));
    }

    /**
     * @param integer $id
     * @return ForexcecRole
     */
    public static function getFirstLoginById($id){
        return self::findFirst(array(
            'role_id = :role_id: AND role_active="Y"',
            'bind' => array('role_id' => $id)
        ));
    }
    public static function getFirstById($id){
        return self::findFirst(array(
            'role_id = :role_id:',
            'bind' => array('role_id' => $id)
        ));
    }
    public static function getGuestUser(){
        return array('guest', 'user');
    }
    public static function getActions(){
        return array(
            'backendindex' => array('index','accessdenied'),
            'backendlogin' => array('index','logout'),
            'backendcron' => array('updateuserlwapi','sendemailauto','sendEmailTest','reportemailsent'),
            'backendsentemaillog' => array('changeissubscribe'),
        );
    }
    public static function getControllers(){
        return array(
            'setting' => array(
                'backendlanguage',
                'backendrole',
                'backendpage',
                'backendemailtemplate',
                'backendtemplateautoemail',
                'backendconfig',
                'backendlocation'),
            'content' => array(
                'backendtype',
                'backendarticle',
                'backendbanner',
                'backendcountry',
                'backendglossary',
                'backendoffice',
                'backendcommunicationchannel'),
            'translate'  => array('backendtranslate','backendlistcron','backendtooltranslate','backendtooltranslatetable','backendtranslatehistory'),
            'page' => array('backendinsertdata'),
            'register' => array('backendregisterpage')
        );
    }
}
