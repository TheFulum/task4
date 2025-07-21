<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/autoload.php';

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
}