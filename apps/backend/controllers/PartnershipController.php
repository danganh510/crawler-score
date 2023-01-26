<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecIp;
use Forexceccom\Models\ForexcecPartnership;
use Forexceccom\Repositories\UserAgent;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class PartnershipController extends ControllerBase
{
    function indexAction()
    {
        $data = $this->getParameter();
        $keyword = $this->dispatcher->getParam("txtSearch");
        $from = $this->dispatcher->getParam('txtFrom');
        $to = $this->dispatcher->getParam('txtTo');
        $list_partnership = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $paginator = new PaginatorModel(
            [
                'data' => $list_partnership,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'from' => $from,
            'to' => $to,
            'keyword' => $keyword
        ));
    }

    function viewAction()
    {
        $partnership_id = $this->request->get('id');
        $checkID = new \Forexceccom\Utils\Validator();
        if (!$checkID->validInt($partnership_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $partnership_model = ForexcecPartnership::findFirstById($partnership_id);
        if (empty($partnership_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_partnership = array('partnership' => $partnership_model, 'location_info' => array(), 'user_agent' => array());
        $activityRepo = new \Forexceccom\Repositories\Activity();
        $activity = $activityRepo->getByControllerAndAction($this->controllerName, 'partnershipform', $partnership_id);

        if ($activity) {

            $arr_partnership['screenSize'] = $activity['activity_computer_screen'];
            $arr_partnership['browserSize'] = $activity['activity_browser_window_size'];
            $ipModel = ForexcecIp::findFirstByIpAddress($activity['activity_ip']);
            $location = '';
            if ($ipModel) {
                $location = $ipModel->getIpCountry() . (!empty($ipModel->getIpCountry()) ? ' - ' : ' ') . $ipModel->getIpCity();
            }
            $location_info = array('ip_address' => $activity['activity_ip'], 'location' => $location);
            $arr_partnership['location_info'] = $location_info;
            $userAgent = UserAgent::getFirstUserAgentById($activity['activity_user_agent_id']);
            if ($userAgent) $arr_partnership['user_agent'] = $userAgent;


        }
        $this->view->arr_partnership = $arr_partnership;
    }

    private function getParameter()
    {
        $sql = "SELECT * FROM Forexceccom\Models\ForexcecPartnership WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (partnership_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (partnership_first_name like CONCAT('%',:keyword:,'%') or partnership_last_name like CONCAT('%',:keyword:,'%') OR partnership_email like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND partnership_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND partnership_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql .= " ORDER BY partnership_insert_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}