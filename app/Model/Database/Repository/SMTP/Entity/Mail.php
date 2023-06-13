<?php


namespace App\Model\Database\Repository\SMTP\Entity;

use App\Model\Database\Entity;

/**
 * Class Mail
 * @package App\Model\Database\Repository\SMTP\Entity
 */
class Mail extends Entity
{
    const title = "title", original_sender = "original_sender", server_id = "server_id", content = "content", date ="date", smtp_id = "smtp_id", id = "id";

    public string $title;
    public string $original_sender;
    public int $server_id;
    public string $content;
    public string $date;
    public int $id;
}