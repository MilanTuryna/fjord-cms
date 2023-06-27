<?php


namespace App\Model\Http\Responses;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\SmartObject;
use Nette\Application\Response as AppResponse;

/**
 * Class Json
 *
 * Alternative for JsonResponse.php in Nette SRC for adding pretty-print to generated JSON in PrettyJsonResponse::send
 */
class PrettyJsonResponse implements AppResponse
{
    use SmartObject;

    /** @var mixed */
    private $payload;

    /** @var string */
    private string $contentType;


    public function __construct($payload, string $contentType = null, private bool $stripSlashes = false, private int $flags = 0)
    {
        $this->payload = $payload;
        $this->contentType = $contentType ?: 'application/json';
    }

    /**
     * Returns the MIME content type of a downloaded file.
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return mixed
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }

    /**
     * @param IRequest $httpRequest
     * @param IResponse $httpResponse
     * @param bool $stripSlashes
     * @param int $flags
     */
    function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->contentType, 'utf-8');
        $encoded = json_encode($this->payload, $this->flags);
        echo $this->stripSlashes ? stripslashes($encoded) : $encoded;
    }
}