<?php


namespace Dochne\Shopping\Entity;


class Category
{
    public $id;
    public $name;
    public $position;
    public $createdAt;
    public $shopping;

    public function getHash() : string
    {
        return md5($this->shopping);
    }
}