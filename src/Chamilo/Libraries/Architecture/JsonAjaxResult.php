<?php
namespace Chamilo\Libraries\Architecture;

use Symfony\Component\HttpFoundation\Response;

/**
 * This class represents a default Json response as provided and used by the various AJAX calls throughout Chamilo
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JsonAjaxResult
{

    /**
     * @var string[]
     */
    public array $properties = [];

    public int $resultCode;

    public string $resultMessage;

    protected bool $returnActualStatusCode = false;

    public function __construct(int $resultCode = 200, array $properties = [])
    {
        $this->setResultCode($resultCode);
        $this->setProperties($properties);
    }

    public static function badRequest(?string $resultMessage = null): void
    {
        self::error(400, $resultMessage);
    }

    public function display(): void
    {
        if ($this->returnActualStatusCode)
        {
            http_response_code($this->getResultCode());
        }
        header('Content-type: application/json');

        echo json_encode($this);
        exit();
    }

    public static function error(int $resultCode = 404, ?string $resultMessage = null): void
    {
        $json_ajax_result = new self($resultCode);

        if ($resultMessage)
        {
            $json_ajax_result->setResultMessage($resultMessage);
        }

        $json_ajax_result->display();
    }

    public static function generalError(?string $resultMessage = null): void
    {
        self::error(500, $resultMessage);
    }

    /**
     * @return string[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param string[] $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getProperty(string $property): string
    {
        return $this->properties[$property];
    }

    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    public function setResultCode(int $resultCode): void
    {
        $this->resultCode = $resultCode;
        $this->resultMessage = Response::$statusTexts[$resultCode];
    }

    public function getResultMessage(): string
    {
        return $this->resultMessage;
    }

    public function setResultMessage(string $resultMessage): void
    {
        $this->resultMessage = $resultMessage;
    }

    public static function notAllowed(?string $resultMessage = null): void
    {
        self::error(403, $resultMessage);
    }

    public static function notFound(?string $resultMessage = null): void
    {
        self::error(404, $resultMessage);
    }

    public function resetResultMessage(): void
    {
        $this->resultMessage = Response::$statusTexts[$this->getResultCode()];
    }

    /**
     *  For backwards compatibility. Every response returns a 200 status code unless this function is called
     */
    public function returnActualStatusCode(): void
    {
        $this->returnActualStatusCode = true;
    }

    public function setProperty(string $property, mixed $value): void
    {
        $this->properties[$property] = $value;
    }

    public static function success(?string $resultMessage = null): void
    {
        self::error(200, $resultMessage);
    }
}
