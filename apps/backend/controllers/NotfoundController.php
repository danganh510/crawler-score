<?php
namespace Forexceccom\Backend\Controllers;

use Forexceccom\Repositories\Page;
use Forexceccom\Repositories\SendError;

class NotfoundController extends ControllerBase
{
    public function indexAction()
    {
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('notfound','404 - Not Found','not-found', $this->location_code, $this->lang_code);
        $repoPage->generateStylePage('notfound');
        /**
         * Send Error Email
         */
        $message = "";
        if (!isset($_GET['_url']) || strpos($_GET['_url'], '/notfound') === FALSE) {
            $sent_error = new SendError();
            $sent_error->sendErrorNotfound($message);
            $this->response->redirect('/notfound');
        }
    }

    public function notfoundAction()
    {
        $this->my->sendErrorEmailAndRedirectToNotFoundPage();
    }
}