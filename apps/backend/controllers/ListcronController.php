<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Utils\Validator;
use Forexceccom\Repositories\Language;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ListCronController extends ControllerBase
{
    public function indexAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $keyword = trim($this->request->get("txtSearch"));
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecCron WHERE 1";
        $arrParameter = array();
        if(!empty($keyword)){
            if($validator->validInt($keyword)) {
                $sql.=" AND (cron_id = :keyword:) ";
            } else {
                $sql.=" AND (cron_type like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql.=" ORDER BY cron_id DESC";
        $list_cron = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(
            [
                'data'  => $list_cron,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );

        $this->view->list_cron = $paginator->getPaginate();
    }

    public function detailAction()
    {
        $lang_code = strtolower($this->request->get('slLanguage'));
        $from = $this->request->get('txtFrom');
        $to = $this->request->get('txtTo');
        $keyword = trim($this->request->get("txtSearch"));
        $current_page = $this->request->get('page');
        $lang_combobox = Language::getCombo($lang_code);
        $cron_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($cron_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $arrParameter = array();
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecCronDetail WHERE detail_cron_id = :ID:";
        $arrParameter['ID'] = $cron_id;
        if(!empty($keyword)){
            if($validator->validInt($keyword)) {
                $sql.=" AND (detai_id = :keyword:) ";
            }else{
                $sql.=" AND detail_table like CONCAT('%',:keyword:,'%')";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }

        if(!empty($lang_code)){
            $sql.=" AND (detail_lang_code = :langCODE:)";
            $arrParameter['langCODE'] = $lang_code;
            $this->dispatcher->setParam("lang_code", $lang_code);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND detail_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND detail_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql.=" ORDER BY detai_id DESC";
        $list_cron_detail = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(
            [
                'data'  => $list_cron_detail,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $this->view->setVars([
            'cron_id' => $cron_id,
            'list_cron_detail' => $paginator->getPaginate(),
            'slLanguage' => $lang_combobox,

        ]);

    }


}