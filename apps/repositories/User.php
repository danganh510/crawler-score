<?php

namespace Score\Repositories;
use Score\Models\ForexcecUser;
use Score\Models\ForexcecRole;
use Phalcon\Mvc\User\Component;

class User extends Component {
    /**
     * @var ForexcecUser $user
     * @var ForexcecRole $role
     */
    public function initSession($user,$role){
        if ($user) {            
            $role_name = ($role)?$role->getRoleName():"user";
            $this->session->set('auth', array(
                'id' => $user->getUserId(),
                'name' => $user->getUserName(),
                'email' => $user->getUserEmail(),
                'role' => $role_name,
                'insertTime' => $user->getUserInsertTime(),
            ));
        }
        return false;
    }
    public function redirectLogged($pre = "") {
        if ($this->session->has('preURL')){
            $preURL = $this->session->get('preURL');
            $this->session->remove('preURL');
            if (strlen($preURL)>1 && $preURL != "/"){
                $this->response->redirect($preURL);
                return;
            }
        }
        if($pre == "")
            $this->response->redirect("my-account");
        else
            $this->response->redirect($pre);
    }

    public static function getByLimit($limit){
        return ForexcecUser::find(array(
            "order"      => "user_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
