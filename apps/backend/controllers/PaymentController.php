<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecCurrency;
use Forexceccom\Models\ForexcecPayment;
use Forexceccom\Models\ForexcecUser;
use Forexceccom\Repositories\Payment;
use Forexceccom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class PaymentController extends ControllerBase
{
    public function indexAction()
    {

        $data = $this->getParameter();
        $btn_export = $this->request->getPost("btnExportCsv");
        $keyword = $this->dispatcher->getParam("txtSearch");
        $from_payment = $this->dispatcher->getParam('txtFromPayment');
        $to_payment = $this->dispatcher->getParam('txtToPayment');
        $from = $this->dispatcher->getParam('txtFrom');
        $to = $this->dispatcher->getParam('txtTo');
        $status = $this->dispatcher->getParam('slcStatus');
        $method_payment = trim($this->request->get("slcMethodPayment"));
        $list_payments = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->getQuery('page');
        $paginator = new PaginatorModel(
            [
                'data' => $list_payments,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $str_status = Payment::getStatusCombobox($status);
        $str_method_payment = Payment::getMethodCombobox($method_payment);
        $this->view->setVars(array(
            'list_payments' => $paginator->getPaginate(),
            'from_payment' => $from_payment,
            'to_payment' => $to_payment,
            'from' => $from,
            'to' => $to,
            'keyword' => $keyword,
            'str_status' => $str_status,
            'str_method_payment' => $str_method_payment,
        ));
        if (isset($btn_export)) {
            $this->view->disable();
            $results[] = array("Id", "Full Name", "Email", "Method", "Order Date", "Payment Date", "Status", "Amount");
            foreach ($list_payments as $item) {
                $selectCurrency = $item->payment_order_currency;
                $currencyData['code'] = $selectCurrency;
                $currencyDataFromDatabase = \Forexceccom\Models\ForexcecCurrency::findFirst("currency_code = '$selectCurrency'");
                if ($currencyDataFromDatabase) {
                    $currencyData['symbol'] = $currencyDataFromDatabase->getCurrencySymbolLeft();
                    $amount = $currencyData['code'] . ' ' . $this->my->formatUSD($item->payment_order_amount);
                } else {
                    $currencyData['symbol'] = $this->globalVariable->mainCurrency;
                    $amount = $currencyData['code'] . ' ' . $this->my->formatUSD($item->payment_order_amount);
                }

                $dataExport = array(
                    $this->my->formatPaymentID($item->payment_insertdate, $item->payment_id),
                    $item->user_name,
                    $item->user_email,
                    $item->payment_method,
                    $this->my->formatDateTime($item->payment_insertdate, false),
                    $item->payment_date > 0 ? $this->my->formatDateTime($item->payment_date, false) : '',
                    $item->payment_status,
                    $amount,
                );
                $results[] = $dataExport;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=payment_' . time() . '.csv');
            $out = fopen('php://output', 'w');
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
        }
    }

    public function detailAction()
    {
        $payment_id = $this->request->getQuery("id");
        $checkID = new Validator();
        if (!$checkID->validInt($payment_id)) {
            return $this->response->redirect('notfound');
        }
        $payment = ForexcecPayment::getById($payment_id);
        if (!$payment) {
            return $this->response->redirect('notfound');
        }
        $currencyInfo = ForexcecCurrency::findFirstActiveByCode($payment->getPaymentCurrency());
        $this->view->setVars([
            'payment' => $payment,
            'currencyInfo' => $currencyInfo,
        ]);
    }

    private function getParameter()
    {
        $sql = "SELECT u.user_name, u.user_email, p.payment_id, p.payment_insertdate, p.payment_method
        , p.payment_cardtype, p.payment_isenrolled3d, p.payment_ispassed3d, p.payment_status, p.payment_currency, p.payment_order_currency
        ,  p.payment_order_amount, p.payment_date
        FROM Forexceccom\Models\ForexcecPayment AS p
        LEFT JOIN Forexceccom\Models\ForexcecUser AS u 
        ON p.payment_user_id = u.user_id
         WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from_payment = trim($this->request->get("txtFromPayment")); //string
        $to_payment = trim($this->request->get("txtToPayment"));  //string
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string
        $status = trim($this->request->get("slcStatus"));
        $method_payment = trim($this->request->get("slcMethodPayment"));
        $site = trim($this->request->get("slcSite"));
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (payment_id = :number:)";
                $arrParameter['number'] = $this->my->getIdFromFormatID($keyword, true);
            } else {
                $sql .= " AND (user_name like CONCAT('%',:keyword:,'%') OR user_email like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if ($from_payment) {
            $intFromPayment = $this->my->UTCTime(strtotime($from_payment)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND payment_date >= :from_payment:";
            $arrParameter['from_payment'] = $intFromPayment;
            $this->dispatcher->setParam("txtFromPayment", $from_payment);
        }
        if ($to_payment) {
            $intToPayment = $this->my->UTCTime(strtotime($to_payment)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND payment_date <= :to_payment:";
            $arrParameter['to_payment'] = $intToPayment;
            $this->dispatcher->setParam("txtToPayment", $to_payment);
        }
        if ($from) {
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND payment_insertdate >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if ($to) {
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND payment_insertdate <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        if (!empty($status)) {
            $sql .= " AND payment_status =:status:";
            $arrParameter['status'] = $status;
            $this->dispatcher->setParam("slcStatus", $status);
        }
        if (!empty($method_payment)) {
            $sql .= " AND payment_method =:pay_method:";
            $arrParameter['pay_method'] = $method_payment;
            $this->dispatcher->setParam("slcMethodPayment", $method_payment);
        }
        if (!empty($site)) {
            $sql .= " AND (payment_website = :site:) ";
            $arrParameter['site'] = $site;
            $this->dispatcher->setParam("slcSite", $site);
        }
        $sql .= " ORDER BY payment_insertdate DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }


}