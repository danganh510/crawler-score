<?php

namespace Forexceccom\Backend\Controllers;


use Forexceccom\Models\ForexcecTranslateHistory;
use Forexceccom\Models\ForexcecIp;
use Forexceccom\Repositories\Activity;
use Forexceccom\Repositories\UserAgent;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class TranslatehistoryController extends ControllerBase
{

    public function indexAction()
    {
        $data = $this->getParameter();
        $keyword = $this->dispatcher->getParam("txtSearch");
        $from = $this->dispatcher->getParam('txtFrom');
        $to = $this->dispatcher->getParam('txtTo');
        $slStatus = $this->dispatcher->getParam('slStatus');
        $list_record = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $btn_export = $this->request->getPost("btnExportcsv");
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $paginator = new PaginatorModel(
            [
                'data' => $list_record,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $totalCharacter = 0;
        foreach ($list_record as $item) {
            if($item->getHistoryStatus() == ForexcecTranslateHistory::STATUS_SUCCESS){
                $totalCharacter += mb_strlen($item->getHistoryDataSource());
            }
        }
        if(isset($btn_export)){
            $this->view->disable();
            $results[] = array("Id","Site","Table","Record Id","Source Lang Code","Target Lang Code","Count Character","Format","Status","Insert Time");
            foreach ($list_record as $item)
            {
                $test = array(
                    $item->history_id,
                    $item->history_site,
                    $item->history_table,
                    $item->history_record_id,
                    $item->history_source_lang_code,
                    $item->history_target_lang_code,
                    mb_strlen($item->getHistoryDataSource()),
                    $item->history_format,
                    $item->history_status,
                    $this->my->formatDateTime($item->getHistoryInsertTime(),false),
                );
                $results[] = $test;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=translate_history_'.time().'.csv');
            $out = fopen('php://output', 'w');
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
        }
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'from' => $from,
            'to' => $to,
            'slStatus' => $slStatus,
            'keyword' => $keyword,
            'totalCharacter' => $totalCharacter,
        ));
    }
    public function viewAction()
    {
        $record_id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($record_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $record_model = ForexcecTranslateHistory::findFirstById($record_id);
        if (!$record_model) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translatehistory = array('translatehistory' => $record_model, 'location_info' => array(), 'user_agent' => array());

        $activityRepo = new Activity();
        $activities = $activityRepo->getByControllerAndAction($this->controllerName, 'translatehistory',$record_id);
        foreach ($activities as $activity) {
            $list_translatehistory = json_decode($activity['activity_data_log'], true);
            if (array_key_exists($record_id, $list_translatehistory['forexcec_translate_history'])) {
                $ipModel = ForexcecIp::findFirstByIpAddress($activity['activity_ip']);
                $location = '';
                if ($ipModel) {
                    $location = $ipModel->getIpCountry() . (!empty($ipModel->getIpCountry()) ? ' - ' : ' ') . $ipModel->getIpCity();
                }
                $location_info = array('ip_address' => $activity['activity_ip'], 'location' => $location);
                $arr_translatehistory['location_info'] = $location_info;
                $userAgent = UserAgent::getFirstUserAgentById($activity['activity_user_agent_id']);
                if ($userAgent) $arr_translatehistory['user_agent'] = $userAgent;
                break;
            }
        }
        $this->view->arr_translatehistory = $arr_translatehistory;
        $this->view->record_model = $record_model;
    }
    private function getParameter()
    {
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecTranslateHistory WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom"));
        $to = trim($this->request->get("txtTo"));
        $slStatus = trim($this->request->get("slStatus"));
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (history_id = :number:)";
                $arrParameter['number'] = $this->my->getIdFromFormatID($keyword, true);
            } else {
                $sql .= " AND (history_table like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if(!empty($slStatus)){
            $sql.=" AND (history_status = :slStatus:)";
            $arrParameter['slStatus'] = $slStatus;
            $this->dispatcher->setParam("slStatus", $slStatus);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND history_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND history_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql .= " ORDER BY history_insert_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}