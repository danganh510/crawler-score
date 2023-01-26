<?php


namespace General\Models;


use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    public function initialize()
    {
        // write operation using db connection
        $this->setWriteConnectionService('db_general');
        // read operation using the connection dbSlave
        $this->setReadConnectionService('db_general_slave');
    }
}