<?php
namespace Chamilo\Core\Repository\Publication\Domain;

/**
 * @package Chamilo\Core\Repository\Publication\Domain
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationResult
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 2;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $url;

    /**
     * @param integer $status
     * @param string $message
     * @param string $url
     */
    public function __construct(int $status, string $message, string $url = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * @return integer
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param integer $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

}