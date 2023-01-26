<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecContactus;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class ContactusController extends ControllerBase
{
    public function indexAction()
    {
        $list_contactus = $this->getParameter();
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $paginator = new PaginatorModel(
            [
                'data' => $list_contactus,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
        ));
    }

    public function viewAction()
    {
        $contactUsId = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($contactUsId)) {
            $this->response->redirect('notfound');
            return;
        }
        $contactUsModel = ForexcecContactus::findFirst($contactUsId);
        if (!$contactUsModel) {
            $this->response->redirect('notfound');
            return;
        }
        $this->view->contactUsModel = $contactUsModel;
    }

    private function getParameter()
    {
        $sql = ForexcecContactus::query();
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string

        if (!empty($keyword)) {
            $sql->where("contact_id = :keyword: OR 
                                  contact_name like CONCAT('%',:keyword:,'%') OR 
                                  contact_email like CONCAT('%',:keyword:,'%')", ["keyword" => $keyword]);
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("contact_insert_time >= :from:", ["from" => $intFrom]);
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("contact_insert_time <= :to:", ["to" => $intTo]);
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql->orderBy("contact_insert_time DESC");
        return $sql->execute();
    }
}