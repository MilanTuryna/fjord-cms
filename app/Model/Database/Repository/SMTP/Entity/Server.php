<?php


namespace App\Model\Database\Repository\SMTP\Entity;


use App\Model\Database\Entity;

/**
 * Class Server
 * @package App\Model\Database\Repository\SMTP\Entity
 */
class Server extends Entity
{
    const name = "name";
    const server_email = "server_email", server_password = "server_password", server_host = "server_host", receiver_email = "receiver_server_email", active = "active";

    public string $name;

    public string $server_email;
    public string $server_password;
    public string $server_host;

    public string $receiver_email;

    public bool $active;
    public int $id;
}