<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecUserAgent;
use Phalcon\Mvc\User\Component;

class UserAgent extends Component
{
    public static function getFirstUserAgentById($agent_id) {
        return ForexcecUserAgent::findFirst(array(
            'agent_id = :agent_id:',
            'bind' => array('agent_id' => $agent_id)
        ));
    }
}


