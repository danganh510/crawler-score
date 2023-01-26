<?php

namespace Forexceccom\Models;

class ForexcecTranslateHistory extends \Phalcon\Mvc\Model
{
    const STATUS_SUCCESS = "Success";
    const STATUS_FAIL = "Fail";
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     */
    protected $history_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $history_record_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $history_site;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $history_table;

    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=false)
     */
    protected $history_source_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=false)
     */
    protected $history_target_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $history_format;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $history_status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $history_data_source;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $history_message;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $history_insert_time;

    /**
     * Method to set the value of field history_id
     *
     * @param integer $history_id
     * @return $this
     */
    public function setHistoryId($history_id)
    {
        $this->history_id = $history_id;

        return $this;
    }

    /**
     * Method to set the value of field history_record_id
     *
     * @param integer $history_record_id
     * @return $this
     */
    public function setHistoryRecordId($history_record_id)
    {
        $this->history_record_id = $history_record_id;

        return $this;
    }

    /**
     * Method to set the value of field history_site
     *
     * @param string $history_site
     * @return $this
     */
    public function setHistorySite($history_site)
    {
        $this->history_site = $history_site;

        return $this;
    }

    /**
     * Method to set the value of field history_table
     *
     * @param string $history_table
     * @return $this
     */
    public function setHistoryTable($history_table)
    {
        $this->history_table = $history_table;

        return $this;
    }

    /**
     * Method to set the value of field history_source_lang_code
     *
     * @param string $history_source_lang_code
     * @return $this
     */
    public function setHistorySourceLangCode($history_source_lang_code)
    {
        $this->history_source_lang_code = $history_source_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field history_target_lang_code
     *
     * @param string $history_target_lang_code
     * @return $this
     */
    public function setHistoryTargetLangCode($history_target_lang_code)
    {
        $this->history_target_lang_code = $history_target_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field history_format
     *
     * @param string $history_format
     * @return $this
     */
    public function setHistoryFormat($history_format)
    {
        $this->history_format = $history_format;

        return $this;
    }

    /**
     * Method to set the value of field history_status
     *
     * @param string $history_status
     * @return $this
     */
    public function setHistoryStatus($history_status)
    {
        $this->history_status = $history_status;

        return $this;
    }

    /**
     * Method to set the value of field history_data_source
     *
     * @param string $history_data_source
     * @return $this
     */
    public function setHistoryDataSource($history_data_source)
    {
        $this->history_data_source = $history_data_source;

        return $this;
    }

    /**
     * Method to set the value of field history_message
     *
     * @param string $history_message
     * @return $this
     */
    public function setHistoryMessage($history_message)
    {
        $this->history_message = $history_message;

        return $this;
    }

    /**
     * Method to set the value of field history_insert_time
     *
     * @param integer $history_insert_time
     * @return $this
     */
    public function setHistoryInsertTime($history_insert_time)
    {
        $this->history_insert_time = $history_insert_time;

        return $this;
    }

    /**
     * Returns the value of field history_id
     *
     * @return integer
     */
    public function getHistoryId()
    {
        return $this->history_id;
    }

    /**
     * Returns the value of field history_record_id
     *
     * @return integer
     */
    public function getHistoryRecordId()
    {
        return $this->history_record_id;
    }

    /**
     * Returns the value of field history_site
     *
     * @return string
     */
    public function getHistorySite()
    {
        return $this->history_site;
    }

    /**
     * Returns the value of field history_table
     *
     * @return string
     */
    public function getHistoryTable()
    {
        return $this->history_table;
    }

    /**
     * Returns the value of field history_source_lang_code
     *
     * @return string
     */
    public function getHistorySourceLangCode()
    {
        return $this->history_source_lang_code;
    }

    /**
     * Returns the value of field history_target_lang_code
     *
     * @return string
     */
    public function getHistoryTargetLangCode()
    {
        return $this->history_target_lang_code;
    }

    /**
     * Returns the value of field history_format
     *
     * @return string
     */
    public function getHistoryFormat()
    {
        return $this->history_format;
    }

    /**
     * Returns the value of field history_status
     *
     * @return string
     */
    public function getHistoryStatus()
    {
        return $this->history_status;
    }

    /**
     * Returns the value of field history_data_source
     *
     * @return string
     */
    public function getHistoryDataSource()
    {
        return $this->history_data_source;
    }

    /**
     * Returns the value of field history_message
     *
     * @return string
     */
    public function getHistoryMessage()
    {
        return $this->history_message;
    }

    /**
     * Returns the value of field history_insert_time
     *
     * @return integer
     */
    public function getHistoryInsertTime()
    {
        return $this->history_insert_time;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("db_test");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_translate_history';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecTranslateHistory[]|ForexcecTranslateHistory
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecTranslateHistory
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById ($id) {
        return ForexcecTranslateHistory::findFirst(array(
            'history_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
}
