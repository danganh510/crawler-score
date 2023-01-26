<?php

namespace Forexceccom\Models;

use Phalcon\Di;

class ForexcecCronDetail extends \Phalcon\Mvc\Model
{

    const STATUS_RUNNING = "Running";
    const STATUS_SUCCESS = "Success";
    const STATUS_FAIL = "Fail";
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $detai_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $detail_cron_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $detail_table;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $detail_status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $detail_data;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $detail_data_error;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $detail_total;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $detail_insert_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $detail_active;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $detail_lang_code;

    /**
     * Method to set the value of field detai_id
     *
     * @param integer $detai_id
     * @return $this
     */
    public function setDetaiId($detai_id)
    {
        $this->detai_id = $detai_id;

        return $this;
    }

    /**
     * Method to set the value of field detail_cron_id
     *
     * @param integer $detail_cron_id
     * @return $this
     */
    public function setDetailCronId($detail_cron_id)
    {
        $this->detail_cron_id = $detail_cron_id;

        return $this;
    }

    /**
     * Method to set the value of field detail_table
     *
     * @param string $detail_table
     * @return $this
     */
    public function setDetailTable($detail_table)
    {
        $this->detail_table = $detail_table;

        return $this;
    }

    /**
     * Method to set the value of field detail_status
     *
     * @param string $detail_status
     * @return $this
     */
    public function setDetailStatus($detail_status)
    {
        $this->detail_status = $detail_status;

        return $this;
    }

    /**
     * Method to set the value of field detail_data
     *
     * @param string $detail_data
     * @return $this
     */
    public function setDetailData($detail_data)
    {
        $this->detail_data = $detail_data;

        return $this;
    }

    /**
     * Method to set the value of field detail_data_error
     *
     * @param string $detail_data_error
     * @return $this
     */
    public function setDetailDataError($detail_data_error)
    {
        $this->detail_data_error = $detail_data_error;

        return $this;
    }

    /**
     * Method to set the value of field detail_total
     *
     * @param integer $detail_total
     * @return $this
     */
    public function setDetailTotal($detail_total)
    {
        $this->detail_total = $detail_total;

        return $this;
    }

    /**
     * Method to set the value of field detail_insert_time
     *
     * @param integer $detail_insert_time
     * @return $this
     */
    public function setDetailInsertTime($detail_insert_time)
    {
        $this->detail_insert_time = $detail_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field detail_active
     *
     * @param string $detail_active
     * @return $this
     */
    public function setDetailActive($detail_active)
    {
        $this->detail_active = $detail_active;

        return $this;
    }

    /**
     * Method to set the value of field detail_lang_code
     *
     * @param string $detail_lang_code
     * @return $this
     */
    public function setDetailLangCode($detail_lang_code)
    {
        $this->detail_lang_code = $detail_lang_code;

        return $this;
    }

    /**
     * Returns the value of field detai_id
     *
     * @return integer
     */
    public function getDetaiId()
    {
        return $this->detai_id;
    }

    /**
     * Returns the value of field detail_cron_id
     *
     * @return integer
     */
    public function getDetailCronId()
    {
        return $this->detail_cron_id;
    }

    /**
     * Returns the value of field detail_table
     *
     * @return string
     */
    public function getDetailTable()
    {
        return $this->detail_table;
    }

    /**
     * Returns the value of field detail_status
     *
     * @return string
     */
    public function getDetailStatus()
    {
        return $this->detail_status;
    }

    /**
     * Returns the value of field detail_data
     *
     * @return string
     */
    public function getDetailData()
    {
        return $this->detail_data;
    }

    /**
     * Returns the value of field detail_data_error
     *
     * @return string
     */
    public function getDetailDataError()
    {
        return $this->detail_data_error;
    }

    /**
     * Returns the value of field detail_total
     *
     * @return integer
     */
    public function getDetailTotal()
    {
        return $this->detail_total;
    }

    /**
     * Returns the value of field detail_insert_time
     *
     * @return integer
     */
    public function getDetailInsertTime()
    {
        return $this->detail_insert_time;
    }

    /**
     * Returns the value of field detail_active
     *
     * @return string
     */
    public function getDetailActive()
    {
        return $this->detail_active;
    }

    /**
     * Returns the value of field detail_lang_code
     *
     * @return string
     */
    public function getDetailLangCode()
    {
        return $this->detail_lang_code;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("offshorecompanycorp");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'forexcec_cron_detail';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCronDetail[]|ForexcecCronDetail
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ForexcecCronDetail
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function getDataByCronTableLangCode($id,$table,$lang_code){
        return ForexcecCronDetail::findFirst(array(
            'detail_cron_id = :ID: AND detail_table = :TABLE: AND detail_lang_code = :LANG_CODE:',
            'bind' => array('ID' => $id, 'TABLE' => $table, 'LANG_CODE' => $lang_code),
        ));
    }

    public static function updateStatus($table,$lang_code,$cronID,$status){
        $occ_cron_detail_model =  ForexcecCronDetail::findFirst(array(
            'detail_table = :TABLE: AND detail_lang_code = :LANG_CODE: AND detail_cron_id = :CRON_ID:',
            'bind' => array('TABLE' => $table, 'LANG_CODE' => $lang_code, 'CRON_ID' => $cronID),
        ));
        if ($occ_cron_detail_model){
            $occ_cron_detail_model->setDetailStatus($status);
            $occ_cron_detail_model->save();
        }
    }

    public static function timeCronRun($cron_id) {
      $modelsManager = Di::getDefault()->get('modelsManager');

      $sql = "SELECT max(detail_insert_time) - min(detail_insert_time) as time_run 
              FROM Forexceccom\Models\ForexcecCronDetail
              WHERE detail_cron_id = :ID:";
      $timeRun = array_column($modelsManager->executeQuery($sql,['ID'=>$cron_id])->toArray(),'time_run');
      $timeRun =  reset($timeRun);
      if ($timeRun == 0 ){
          $occCronDetail = self::findFirst(array(
             'detail_cron_id = :ID:',
             'bind' => array('ID' => $cron_id),
          ));
          if ($occCronDetail){
              $timeRun = time() - $occCronDetail->getDetailInsertTime();
          }
      }
      return $timeRun;
    }

    public static function getDataError($table, $status, $langCode)
    {
        $occCronDetail = self::findFirst(array(
            'detail_table = :TABLE: AND detail_status = :STATUS: AND detail_lang_code = :LANG_CODE: ',
            'bind' => array('TABLE' => $table, 'STATUS' => $status, 'LANG_CODE' => $langCode),
        ));
        $id_error = "('')";
        if ($occCronDetail && !empty($occCronDetail->getDetailDataError()) ){
            $id_error = str_replace('[','(',$occCronDetail->getDetailDataError());
            $id_error = str_replace(']',')',$id_error);
        }
        return $id_error;
    }
    public static function updateDataError($table,$lang_code,$id_insert){
        $occ_cron_detail_model =  ForexcecCronDetail::findFirst(array(
            'detail_table = :TABLE: AND detail_lang_code = :LANG_CODE:',
            'bind' => array('TABLE' => $table, 'LANG_CODE' => $lang_code),
        ));
        if ($occ_cron_detail_model){
            if (empty($occ_cron_detail_model->getDetailDataError())){
                $detail_data_error = "[" . json_encode($id_insert) . "]";
            }else{
                $detail_data_error = json_decode($occ_cron_detail_model->getDetailDataError());
                array_push($detail_data_error, $id_insert);
                $detail_data_error = json_encode($detail_data_error);
            }
            $occ_cron_detail_model->setDetailDataError($detail_data_error);
            $occ_cron_detail_model->save();

        }
    }

}
