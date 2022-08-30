<?php

use Phalcon\Mvc\Model;

class Transactions extends Model
{
    public $id;
    public $id_company;
    public $id_client;
    public $id_provider;
    public $id_product;
    public $quantity_product;
    public $id_employee;
}
