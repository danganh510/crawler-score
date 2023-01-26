<?php

namespace Forexceccom\Models;
use Phalcon\Db\RawValue;
class ForexcecCurrency extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $currency_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $currency_title;

    /**
     *
     * @var string
     * @Column(type="string", length=3, nullable=false)
     */
    protected $currency_code;

    /**
     *
     * @var string
     * @Column(type="string", length=8, nullable=false)
     */
    protected $currency_symbol_left;

    /**
     *
     * @var string
     * @Column(type="string", length=8, nullable=false)
     */
    protected $currency_symbol_right;

    /**
     *
     * @var double
     * @Column(type="double", length=15, nullable=false)
     */
    protected $currency_value;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $currency_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $currency_date_modified;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $currency_order;

    public function beforeValidation()
    {
        if (empty($this->currency_symbol_left)) {
            $this->currency_symbol_left = new RawValue('\'\'');
        }
        if (empty($this->currency_symbol_right)) {
            $this->currency_symbol_right = new RawValue('\'\'');
        }
    }
    /**
     * Method to set the value of field currency_id
     *
     * @param integer $currency_id
     * @return $this
     */
    public function setCurrencyId($currency_id)
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    /**
     * Method to set the value of field currency_title
     *
     * @param string $currency_title
     * @return $this
     */
    public function setCurrencyTitle($currency_title)
    {
        $this->currency_title = $currency_title;

        return $this;
    }

    /**
     * Method to set the value of field currency_code
     *
     * @param string $currency_code
     * @return $this
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;

        return $this;
    }

    /**
     * Method to set the value of field currency_symbol_left
     *
     * @param string $currency_symbol_left
     * @return $this
     */
    public function setCurrencySymbolLeft($currency_symbol_left)
    {
        $this->currency_symbol_left = $currency_symbol_left;

        return $this;
    }

    /**
     * Method to set the value of field currency_symbol_right
     *
     * @param string $currency_symbol_right
     * @return $this
     */
    public function setCurrencySymbolRight($currency_symbol_right)
    {
        $this->currency_symbol_right = $currency_symbol_right;

        return $this;
    }

    /**
     * Method to set the value of field currency_value
     *
     * @param double $currency_value
     * @return $this
     */
    public function setCurrencyValue($currency_value)
    {
        $this->currency_value = $currency_value;

        return $this;
    }

    /**
     * Method to set the value of field currency_active
     *
     * @param string $currency_active
     * @return $this
     */
    public function setCurrencyActive($currency_active)
    {
        $this->currency_active = $currency_active;

        return $this;
    }

    /**
     * Method to set the value of field currency_date_modified
     *
     * @param integer $currency_date_modified
     * @return $this
     */
    public function setCurrencyDateModified($currency_date_modified)
    {
        $this->currency_date_modified = $currency_date_modified;

        return $this;
    }

    /**
     * Method to set the value of field currency_order
     *
     * @param integer $currency_order
     * @return $this
     */
    public function setCurrencyOrder($currency_order)
    {
        $this->currency_order = $currency_order;

        return $this;
    }

    /**
     * Returns the value of field currency_id
     *
     * @return integer
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * Returns the value of field currency_title
     *
     * @return string
     */
    public function getCurrencyTitle()
    {
        return $this->currency_title;
    }

    /**
     * Returns the value of field currency_code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * Returns the value of field currency_symbol_left
     *
     * @return string
     */
    public function getCurrencySymbolLeft()
    {
        return $this->currency_symbol_left;
    }

    /**
     * Returns the value of field currency_symbol_right
     *
     * @return string
     */
    public function getCurrencySymbolRight()
    {
        return $this->currency_symbol_right;
    }

    /**
     * Returns the value of field currency_value
     *
     * @return double
     */
    public function getCurrencyValue()
    {
        return $this->currency_value;
    }

    /**
     * Returns the value of field currency_active
     *
     * @return string
     */
    public function getCurrencyActive()
    {
        return $this->currency_active;
    }

    /**
     * Returns the value of field currency_date_modified
     *
     * @return integer
     */
    public function getCurrencyDateModified()
    {
        return $this->currency_date_modified;
    }

    /**
     * Returns the value of field currency_order
     *
     * @return integer
     */
    public function getCurrencyOrder()
    {
        return $this->currency_order;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_currency';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCurrency[]|\Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCurrency|\Phalcon\Mvc\Model
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * @param string $currencyCode
     * @return ForexcecCurrency|\Phalcon\Mvc\Model
     */
    public static function findFirstActiveByCode($currencyCode)
    {
        return self::findFirst(array("currency_code = :currency_code: AND currency_active='Y'", 'bind' => array('currency_code' => $currencyCode)));
    }

}
