<?php

namespace Forexceccom\Models;

class ForexcecPayment extends \Phalcon\Mvc\Model
{

    const PAYMENT_CARD = 'credit_debit_card';
    const PAYMENT_CIRCLEPAY = 'circlepay';
    const PAYMENT_PAYPAL = 'paypal';
    const PAYMENT_B2BINPAY = 'b2binpay';

    const STATUS_SUCCESS = 'Success';
    const STATUS_FAIL = 'Fail';
    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';

    /**
     *
     * @var integer
     */
    protected $payment_id;

    /**
     *
     * @var integer
     */
    protected $payment_order;

    /**
     *
     * @var string
     */
    protected $payment_sign_type;

    /**
     *
     * @var string
     */
    protected $payment_pickup_url;

    /**
     *
     * @var string
     */
    protected $payment_receive_url;

    /**
     *
     * @var string
     */
    protected $payment_sign_temp;

    /**
     *
     * @var integer
     */
    protected $payment_user_id;

    /**
     *
     * @var double
     */
    protected $payment_order_amount;

    /**
     *
     * @var string
     */
    protected $payment_order_currency;

    /**
     *
     * @var string
     */
    protected $payment_method;

    /**
     *
     * @var string
     */
    protected $payment_bank_code;

    /**
     *
     * @var string
     */
    protected $payment_cardtype;

    /**
     *
     * @var string
     */
    protected $payment_isenrolled3d;

    /**
     *
     * @var string
     */
    protected $payment_ispassed3d;

    /**
     *
     * @var integer
     */
    protected $payment_insertdate;

    /**
     *
     * @var integer
     */
    protected $payment_date;

    /**
     *
     * @var string
     */
    protected $payment_status;

    /**
     *
     * @var string
     */
    protected $payment_transaction_id;

    /**
     *
     * @var string
     */
    protected $payment_transaction_date;

    /**
     *
     * @var string
     */
    protected $payment_creditcard;

    /**
     *
     * @var string
     */
    protected $payment_creditname;

    /**
     *
     * @var string
     */
    protected $payment_fail_reason;

    /**
     *
     * @var string
     */
    protected $payment_currency;

    /**
     *
     * @var double
     */
    protected $payment_exrate;

    /**
     *
     * @var integer
     */
    protected $payment_exrate_date;

    /**
     *
     * @var string
     */
    protected $payment_language;

    /**
     *
     * @var string
     */
    protected $payment_notify_success;

    /**
     *
     * @var int
     */
    protected $payment_notify_time;


    /**
     * Method to set the value of field payment_id
     *
     * @param integer $payment_id
     * @return $this
     */
    public function setPaymentId($payment_id)
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    /**
     * Method to set the value of field payment_order
     *
     * @param integer $payment_order
     * @return $this
     */
    public function setPaymentOrder($payment_order)
    {
        $this->payment_order = $payment_order;

        return $this;
    }

    /**
     * Method to set the value of field payment_sign_type
     *
     * @param string $payment_sign_type
     * @return $this
     */
    public function setPaymentSignType($payment_sign_type)
    {
        $this->payment_sign_type = $payment_sign_type;

        return $this;
    }

    /**
     * Method to set the value of field payment_pickup_url
     *
     * @param string $payment_pickup_url
     * @return $this
     */
    public function setPaymentPickupUrl($payment_pickup_url)
    {
        $this->payment_pickup_url = $payment_pickup_url;

        return $this;
    }

    /**
     * Method to set the value of field payment_receive_url
     *
     * @param string $payment_receive_url
     * @return $this
     */
    public function setPaymentReceiveUrl($payment_receive_url)
    {
        $this->payment_receive_url = $payment_receive_url;

        return $this;
    }

    /**
     * Method to set the value of field payment_sign_temp
     *
     * @param string $payment_sign_temp
     * @return $this
     */
    public function setPaymentSignTemp($payment_sign_temp)
    {
        $this->payment_sign_temp = $payment_sign_temp;

        return $this;
    }

    /**
     * Method to set the value of field payment_user_id
     *
     * @param integer $payment_user_id
     * @return $this
     */
    public function setPaymentUserId($payment_user_id)
    {
        $this->payment_user_id = $payment_user_id;

        return $this;
    }

    /**
     * Method to set the value of field payment_order_amount
     *
     * @param double $payment_order_amount
     * @return $this
     */
    public function setPaymentOrderAmount($payment_order_amount)
    {
        $this->payment_order_amount = $payment_order_amount;

        return $this;
    }

    /**
     * Method to set the value of field payment_order_currency
     *
     * @param string $payment_order_currency
     * @return $this
     */
    public function setPaymentOrderCurrency($payment_order_currency)
    {
        $this->payment_order_currency = $payment_order_currency;

        return $this;
    }

    /**
     * Method to set the value of field payment_method
     *
     * @param string $payment_method
     * @return $this
     */
    public function setPaymentMethod($payment_method)
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    /**
     * Method to set the value of field payment_bank_code
     *
     * @param string $payment_bank_code
     * @return $this
     */
    public function setPaymentBankCode($payment_bank_code)
    {
        $this->payment_bank_code = $payment_bank_code;

        return $this;
    }

    /**
     * Method to set the value of field payment_cardtype
     *
     * @param string $payment_cardtype
     * @return $this
     */
    public function setPaymentCardtype($payment_cardtype)
    {
        $this->payment_cardtype = $payment_cardtype;

        return $this;
    }

    /**
     * Method to set the value of field payment_isenrolled3d
     *
     * @param string $payment_isenrolled3d
     * @return $this
     */
    public function setPaymentIsenrolled3d($payment_isenrolled3d)
    {
        $this->payment_isenrolled3d = $payment_isenrolled3d;

        return $this;
    }

    /**
     * Method to set the value of field payment_ispassed3d
     *
     * @param string $payment_ispassed3d
     * @return $this
     */
    public function setPaymentIspassed3d($payment_ispassed3d)
    {
        $this->payment_ispassed3d = $payment_ispassed3d;

        return $this;
    }

    /**
     * Method to set the value of field payment_insertdate
     *
     * @param integer $payment_insertdate
     * @return $this
     */
    public function setPaymentInsertdate($payment_insertdate)
    {
        $this->payment_insertdate = $payment_insertdate;

        return $this;
    }

    /**
     * Method to set the value of field payment_date
     *
     * @param integer $payment_date
     * @return $this
     */
    public function setPaymentDate($payment_date)
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    /**
     * Method to set the value of field payment_status
     *
     * @param string $payment_status
     * @return $this
     */
    public function setPaymentStatus($payment_status)
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    /**
     * Method to set the value of field payment_transaction_id
     *
     * @param string $payment_transaction_id
     * @return $this
     */
    public function setPaymentTransactionId($payment_transaction_id)
    {
        $this->payment_transaction_id = $payment_transaction_id;

        return $this;
    }

    /**
     * Method to set the value of field payment_transaction_date
     *
     * @param string $payment_transaction_date
     * @return $this
     */
    public function setPaymentTransactionDate($payment_transaction_date)
    {
        $this->payment_transaction_date = $payment_transaction_date;

        return $this;
    }

    /**
     * Method to set the value of field payment_creditcard
     *
     * @param string $payment_creditcard
     * @return $this
     */
    public function setPaymentCreditcard($payment_creditcard)
    {
        $this->payment_creditcard = $payment_creditcard;

        return $this;
    }

    /**
     * Method to set the value of field payment_creditname
     *
     * @param string $payment_creditname
     * @return $this
     */
    public function setPaymentCreditname($payment_creditname)
    {
        $this->payment_creditname = $payment_creditname;

        return $this;
    }

    /**
     * Method to set the value of field payment_fail_reason
     *
     * @param string $payment_fail_reason
     * @return $this
     */
    public function setPaymentFailReason($payment_fail_reason)
    {
        $this->payment_fail_reason = $payment_fail_reason;

        return $this;
    }

    /**
     * Method to set the value of field payment_currency
     *
     * @param string $payment_currency
     * @return $this
     */
    public function setPaymentCurrency($payment_currency)
    {
        $this->payment_currency = $payment_currency;

        return $this;
    }

    /**
     * Method to set the value of field payment_exrate
     *
     * @param double $payment_exrate
     * @return $this
     */
    public function setPaymentExrate($payment_exrate)
    {
        $this->payment_exrate = $payment_exrate;

        return $this;
    }

    /**
     * Method to set the value of field payment_exrate_date
     *
     * @param integer $payment_exrate_date
     * @return $this
     */
    public function setPaymentExrateDate($payment_exrate_date)
    {
        $this->payment_exrate_date = $payment_exrate_date;

        return $this;
    }

    /**
     * Method to set the value of field payment_language
     *
     * @param string $payment_language
     * @return $this
     */
    public function setPaymentLanguage($payment_language)
    {
        $this->payment_language = $payment_language;

        return $this;
    }

    /**
     * Method to set the value of field payment_notify_success
     *
     * @param string $payment_notify_success
     * @return $this
     */
    public function setPaymentNotifySuccess($payment_notify_success)
    {
        $this->payment_notify_success = $payment_notify_success;

        return $this;
    }

    /**
     * Method to set the value of field payment_notify_time
     *
     * @param int $payment_notify_time
     * @return $this
     */
    public function setPaymentNotifyTime($payment_notify_time)
    {
        $this->payment_notify_time = $payment_notify_time;

        return $this;
    }

    /**
     * Returns the value of field payment_id
     *
     * @return integer
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * Returns the value of field payment_order
     *
     * @return integer
     */
    public function getPaymentOrder()
    {
        return $this->payment_order;
    }

    /**
     * Returns the value of field payment_sign_type
     *
     * @return string
     */
    public function getPaymentSignType()
    {
        return $this->payment_sign_type;
    }

    /**
     * Returns the value of field payment_pickup_url
     *
     * @return string
     */
    public function getPaymentPickupUrl()
    {
        return $this->payment_pickup_url;
    }

    /**
     * Returns the value of field payment_receive_url
     *
     * @return string
     */
    public function getPaymentReceiveUrl()
    {
        return $this->payment_receive_url;
    }

    /**
     * Returns the value of field payment_sign_temp
     *
     * @return string
     */
    public function getPaymentSignTemp()
    {
        return $this->payment_sign_temp;
    }

    /**
     * Returns the value of field payment_user_id
     *
     * @return integer
     */
    public function getPaymentUserId()
    {
        return $this->payment_user_id;
    }

    /**
     * Returns the value of field payment_order_amount
     *
     * @return double
     */
    public function getPaymentOrderAmount()
    {
        return $this->payment_order_amount;
    }

    /**
     * Returns the value of field payment_order_currency
     *
     * @return string
     */
    public function getPaymentOrderCurrency()
    {
        return $this->payment_order_currency;
    }

    /**
     * Returns the value of field payment_method
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * Returns the value of field payment_bank_code
     *
     * @return string
     */
    public function getPaymentBankCode()
    {
        return $this->payment_bank_code;
    }

    /**
     * Returns the value of field payment_cardtype
     *
     * @return string
     */
    public function getPaymentCardtype()
    {
        return $this->payment_cardtype;
    }

    /**
     * Returns the value of field payment_isenrolled3d
     *
     * @return string
     */
    public function getPaymentIsenrolled3d()
    {
        return $this->payment_isenrolled3d;
    }

    /**
     * Returns the value of field payment_ispassed3d
     *
     * @return string
     */
    public function getPaymentIspassed3d()
    {
        return $this->payment_ispassed3d;
    }

    /**
     * Returns the value of field payment_insertdate
     *
     * @return integer
     */
    public function getPaymentInsertdate()
    {
        return $this->payment_insertdate;
    }

    /**
     * Returns the value of field payment_date
     *
     * @return integer
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * Returns the value of field payment_status
     *
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->payment_status;
    }

    /**
     * Returns the value of field payment_transaction_id
     *
     * @return string
     */
    public function getPaymentTransactionId()
    {
        return $this->payment_transaction_id;
    }

    /**
     * Returns the value of field payment_transaction_date
     *
     * @return string
     */
    public function getPaymentTransactionDate()
    {
        return $this->payment_transaction_date;
    }

    /**
     * Returns the value of field payment_creditcard
     *
     * @return string
     */
    public function getPaymentCreditcard()
    {
        return $this->payment_creditcard;
    }

    /**
     * Returns the value of field payment_creditname
     *
     * @return string
     */
    public function getPaymentCreditname()
    {
        return $this->payment_creditname;
    }

    /**
     * Returns the value of field payment_fail_reason
     *
     * @return string
     */
    public function getPaymentFailReason()
    {
        return $this->payment_fail_reason;
    }

    /**
     * Returns the value of field payment_currency
     *
     * @return string
     */
    public function getPaymentCurrency()
    {
        return $this->payment_currency;
    }

    /**
     * Returns the value of field payment_exrate
     *
     * @return double
     */
    public function getPaymentExrate()
    {
        return $this->payment_exrate;
    }

    /**
     * Returns the value of field payment_exrate_date
     *
     * @return integer
     */
    public function getPaymentExrateDate()
    {
        return $this->payment_exrate_date;
    }

    /**
     * Returns the value of field payment_language
     *
     * @return string
     */
    public function getPaymentLanguage()
    {
        return $this->payment_language;
    }

    /**
     * Returns the value of field payment_notify_success
     *
     * @return string
     */
    public function getPaymentLNotifySuccess()
    {
        return $this->payment_notify_success;
    }

    /**
     * Returns the value of field payment_notify_time
     *
     * @return int
     */
    public function getPaymentLNotifyTime()
    {
        return $this->payment_notify_time;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_payment';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecPayment[]|ForexcecPayment|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecPayment|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * @param $paymentId
     * @return ForexcecPayment|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function getById($paymentId)
    {
        return ForexcecPayment::findFirst([
            'payment_id = :paymentId:',
            'bind' => [
                'paymentId' => $paymentId,
            ]
        ]);
    }

    public static function allPaymentStatus()
    {
        return array(
            self::STATUS_SUCCESS,
            self::STATUS_FAIL,
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
        );
    }

    public static function allPaymentMethod()
    {
        return array(
            self::PAYMENT_PAYPAL,
            self::PAYMENT_B2BINPAY,
            self::PAYMENT_CARD,
            self::PAYMENT_CIRCLEPAY,
        );
    }

}
