<?php

namespace Forexceccom\Repositories;

use Forexceccom\Models\ForexcecGlossary;
use Phalcon\Mvc\User\Component;

class Glossary extends Component
{
    public static function checkKeyword($key, $id)
    {
        return ForexcecGlossary::findFirst(
            array(
                'glossary_keyword = :keyword: AND glossary_id != :id: ',
                'bind' => array('keyword' => $key, 'id' => $id),
            )
        );
    }
}

