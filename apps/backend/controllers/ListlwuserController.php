<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecLeadform;
use Forexceccom\Models\ForexcecListLwUser;
use Forexceccom\Repositories\ListLwUser;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Forexceccom\Utils\Validator;

class ListlwuserController extends ControllerBase
{
    public function indexAction()
    {
        $list_lw_user = $this->getParameter();
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $paginator = new PaginatorModel(
            [
                'data' => $list_lw_user,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
        ));
        $btn_export = $this->request->getPost("btnExportcsv");
        if (isset($btn_export)) {
            $this->view->disable();
            $results[] = array("Id", "Email", "Real Name", "Enable", "Account","Last login" ,"Register Time" );
            foreach ($list_lw_user as $item) {
                /**
                 * @var ForexcecListLwUser $item
                 */
                $enable = ($item->getForexcecIsEnable() == 'true') ? 'Yes' : 'No';
                $last_login = $item->getForexcecLastLoginTime() ? $this->my->formatDateTime(($item->getForexcecLastLoginTime()),false) : "";
                $leadform = array(
                    $item->getForexcecPubUserId(),
                    $item->getForexcecEmail(),
                    $item->getForexcecRealName(),
                    $enable,
                    $item->getForexcecAccount(),
                    $last_login,
                    $this->my->formatDateTime(($item->getForexcecRegisterTime()),false),

                );
                $results[] = $leadform;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=lw_user_'.  time() . '.csv');
            $out = fopen('php://output', 'w');
            fputs( $out, "\xEF\xBB\xBF" );
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
        }
    }

    public function viewAction()
    {
        $id = $this->request->get('id');
        $model = ListLwUser::findFirstById($id);
        if (!$model) {
            $this->response->redirect('notfound');
            return;
        }
        $this->view->model = $model;
    }

    private function getParameter()
    {
        $sql = ForexcecListLwUser::query();
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string

        if (!empty($keyword)) {
            $sql->where("forexcec_pub_user_id = :keyword: OR 
                                  forexcec_email like CONCAT('%',:keyword:,'%') OR 
                                  forexcec_real_name like CONCAT('%',:keyword:,'%')", ["keyword" => $keyword]);
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("forexcec_register_time >= :from:", ["from" => $intFrom]);
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql->andWhere("forexcec_register_time <= :to:", ["to" => $intTo]);
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql->orderBy("forexcec_register_time DESC");
        return $sql->execute();
    }
}
