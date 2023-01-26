<?php

namespace Forexceccom\Backend\Controllers;

use Forexceccom\Models\ForexcecRole;
use Forexceccom\Repositories\Activity;
use Phalcon\Mvc\View;
use Forexceccom\Utils\PasswordGenerator;
use Forexceccom\Models\ForexcecUser;
use Forexceccom\Repositories\User;
use Forexceccom\Utils\Validator;

class LoginController extends ControllerBase
{
    public function indexAction()
    {
        if ($this->session->has('auth')) {
            $this->response->redirect('/');
            return;
        }
        if ($this->session->has('msg_login')) {
            $this->view->msg_login = $this->session->get('msg_login');
            $this->session->remove('msg_login');
        }
        if ($this->request->isPost()) {

            $validate = new Validator();
            $email = trim($this->request->getPost('email'));
            $password = trim($this->request->getPost('password'));
            $this->view->email = $email;
            $this->view->password = $password;

            $validLogin = true;
            if (strlen($email) < 1) {
                $this->view->msgErrorEmail = "This field cannot be empty.";
                $validLogin = false;
            } else if (strlen($email) > 255 || !$validate->validEmail($email)) {
                $this->view->msgErrorEmail = "Enter a valid email";
                $validLogin = false;
            } else {
                $this->view->msgErrorEmail = "";
            }
            if (strlen($password) < 1 || strlen($password) > 255) {
                $this->view->msgErrorPass = "This field cannot be empty.";
                $validLogin = false;
            } else {
                $this->view->msgErrorPass = "";
            }
            if ($validLogin) {
                $user = ForexcecUser::findFirstByEmail($email);
                if ($user) {
                    $role = ForexcecRole::getFirstLoginById($user->getUserRoleId());
                    $controllerClass = $this->dispatcher->getControllerClass();
                    if (($role) || (strpos($controllerClass, 'Frontend') !== false)) {
                        if ($this->security->checkHash($password, $user->getUserPassword())) {
                            $user_repo = new User();
                            $ativityRepo = new Activity();
                            $logChangeData = array();
                            $user_id = $user->getUserId();
                            $message = '';
                            $user_repo->initSession($user, $role);
                            $user_repo->redirectLogged("/");
                            $data_log = json_encode($logChangeData);
                            $ativityRepo->logActivity($this->controllerName, $this->actionName, $user_id, $message, $data_log, $user_id);
                            return;
                        } else {
                            $this->view->msgErrorLogin = "Email or password not correct";
                        }
                    } else {
                        $this->view->msgErrorLogin = "User not granted permissions";
                    }
                } else {
                    $this->view->msgErrorLogin = "Email or password not correct";
                }
            }
        }
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => false,
            View::LEVEL_MAIN_LAYOUT => false
        ));
        $this->tag->setTitle('Login');
        $this->view->pick('login/index');
    }

    public function logoutAction()
    {
        $ativityRepo = new Activity();
        $logChangeData = array();
        $user_id = $this->auth['id'];
        $message = '';
        $data_log = json_encode($logChangeData);
        $ativityRepo->logActivity($this->controllerName, $this->actionName, $user_id, $message, $data_log, $user_id);
        $this->session->destroy();
        $this->response->redirect('/login');
        return;
    }
}