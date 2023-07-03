<?php


namespace App\Forms\Front\Data;


use App\Model\Database\Entity;

class ContactFormData extends Entity
{
    public string $name;
    public string $email;
    public string $content;
    public bool $accept;
}