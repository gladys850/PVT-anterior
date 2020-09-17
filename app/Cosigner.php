<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cosigner
{

    private $name;
    private $id_card;
    private $calculates = array();

    public function getName()
    {
        return $this->name;
    }

    public function getId_card()
    {
        return $this->id_card;
    }
    

    public function setName($name)
    {
        $this->name=$name;
    }

    public function setId_card($id_card)
    {
        $this->id_card=$id_card;
    }
}
