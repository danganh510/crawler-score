<?php

namespace Forexceccom\Backend\Controllers;

class DeletecachetoolController extends ControllerBase
{
    const URL_DELETE_ALL_CACHE = 'allCache';
    const URL_DELETE_FOOTER_CACHE = 'footerCache';
    const URL_DELETE_REGISTER_CACHE = 'configCache';
    const URL_DELETE_PROMOTION_CACHE = 'configCache';

    public function indexAction()
    {
        $msg_result = array();
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
        }
        $this->view->msg_result = $msg_result;
    }
    public function deleteallcacheAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) {
            $URL_DELETE_CACHE_TOOL = defined('URL_DELETE_CACHE_TOOL') ? URL_DELETE_CACHE_TOOL : 'https://www.forexcec.com/deletecachetool';
            $URL_DELETE_CACHE_TOOL .= '?type='.self::URL_DELETE_ALL_CACHE;
            $result = self::curl_get_contents($URL_DELETE_CACHE_TOOL);
            $this->session->set('msg_result', array('status' => 'error', 'msg' => $result['message']));
            $this->response->redirect("/delete-cache-tool");
        }
    }
    public function deletefootercacheAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) {
            $URL_DELETE_CACHE_TOOL = defined('URL_DELETE_CACHE_TOOL') ? URL_DELETE_CACHE_TOOL : 'https://www.forexcec.com/deletecachetool';
            $URL_DELETE_CACHE_TOOL .= '?type='.self::URL_DELETE_FOOTER_CACHE;
            $result = self::curl_get_contents($URL_DELETE_CACHE_TOOL);
            $this->session->set('msg_result', array('status' => 'error', 'msg' => $result['message']));
            $this->response->redirect("/delete-cache-tool");
        }
    }
    public function deleteregistercacheAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) {
            $URL_DELETE_CACHE_TOOL = 'https://register.forexcec.com/delete-cache';
            $URL_DELETE_CACHE_TOOL .= '?type='.self::URL_DELETE_REGISTER_CACHE;
            $result = self::curl_get_contents($URL_DELETE_CACHE_TOOL);
            $this->session->set('msg_result', array('status' => 'error', 'msg' => $result['message']));
            $this->response->redirect("/delete-cache-tool");
        }
    }
    public function deletepromotioncacheAction()
    {
        $this->view->disable();
        if ($this->request->isPost()) {
            $URL_DELETE_CACHE_TOOL = 'https://promotion.forexcec.com/delete-cache';
            $URL_DELETE_CACHE_TOOL .= '?type='.self::URL_DELETE_PROMOTION_CACHE;
            $result = self::curl_get_contents($URL_DELETE_CACHE_TOOL);
            $this->session->set('msg_result', array('status' => 'error', 'msg' => $result['message']));
            $this->response->redirect("/delete-cache-tool");
        }
    }

    function curl_get_contents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "ctoken=k3FRQ1U0bYHUVSu6");
        $data = curl_exec($ch);
        curl_close($ch);
        $data_de = json_decode($data,true);
        if($data_de === NULL){
            $data_de= [
                'status'=>'error',
                'message' =>$data,
            ];
        }
        return $data_de;
    }
}