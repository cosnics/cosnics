<?php
namespace Chamilo\Libraries\Protocol\Ajax\Architecture\Domain;

use Symfony\Component\HttpFoundation\Response;

/**
 * This class represents a default Json response as provided and used by the various AJAX calls throughout Chamilo
 *
 * @package Chamilo\Libraries\Protocol\Ajax\Architecture\Domain
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

    public function __construct(int $resultCode = 200, array $properties = [])
    {
        $this->setResultCode($resultCode);
        $this->setProperties($properties);
    }

    public static function badRequest(?string $resultMessage = null): Response
    {
        return self::error(400, $resultMessage);
    }

    public static function error(int $resultCode = 404, ?string $resultMessage = null): Response
    {
        $jsonAjaxResult = new self($resultCode);

        if ($resultMessage) {
            $jsonAjaxResult->setResultMessage($resultMessage);
        }

        return $jsonAjaxResult->getResponse();
    }

    public static function generalError(?string $resultMessage = null): Response
    {
        return self::error(500, $resultMessage);
    }

    /**
     * @return string[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param mixed $properties
     */
    public function setProperties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function getProperty(string $property): string
    {
        return $this->properties[$property];
    }

    public function getResponse(): Response
    {
        return new Response(
            json_encode($this), $this->getResultCode() ?: 200, ['Content-Type' => 'application/json']
        );
    }

    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    public function setResultCode(int $resultCode): static
    {
        $this->resultCode = $resultCode;
        $this->resultMessage = Response::$statusTexts[$resultCode];

        return $this;
    }

    public function getResultMessage(): string
    {
        return $this->resultMessage;
    }

    public function setResultMessage(string $resultMessage): static
    {
        $this->resultMessage = $resultMessage;

        return $this;
    }

    public static function notAllowed(?string $resultMessage = null): Response
    {
        return self::error(403, $resultMessage);
    }

    public static function notFound(?string $resultMessage = null): Response
    {
        return self::error(404, $resultMessage);
    }

    public function resetResultMessage(): static
    {
        $this->resultMessage = Response::$statusTexts[$this->getResultCode()];

        return $this;
    }

    public function setProperty(string $property, mixed $value): static
    {
        $this->properties[$property] = $value;

        return $this;
    }

    public static function success(?string $resultMessage = null): Response
    {
        return self::error(200, $resultMessage);
    }
}
