<?php
namespace App;

use core\database\model\Model;

class User extends Model
{
    public function php()
    {
        echo "Hello PHP";
    }

    public function SayHello()
    {
        return "id={$this->id}";
    }
}
