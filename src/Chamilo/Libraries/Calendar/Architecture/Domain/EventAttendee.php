<?php
namespace Chamilo\Libraries\Calendar\Architecture\Domain;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventAttendee
{
    public const RESPONSE_STATUS_ACCEPTED = 1;
    public const RESPONSE_STATUS_DECLINED = 2;
    public const RESPONSE_STATUS_NONE = 5;
    public const RESPONSE_STATUS_ORGANIZER = 4;
    public const RESPONSE_STATUS_TENTATIVE = 3;
    public const TYPE_OPTIONAL = 2;
    public const TYPE_ORGANIZER = 4;
    public const TYPE_REQUIRED = 1;
    public const TYPE_RESOURCE = 3;

    private string $email;

    private string $name;

    private ?int $responseDate;

    private ?int $responseStatus;

    private ?int $type;

    public function __construct(string $email, string $name, ?int $type, ?int $responseStatus, ?int $responseDate)
    {
        $this->email = $email;
        $this->name = $name;
        $this->responseStatus = $responseStatus;
        $this->responseDate = $responseDate;
        $this->type = $type;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): EventAttendee
    {
        $this->email = $email;

        return $this;
    }

    public function getICalResponseStatus(): ?string
    {
        return match ($this->getResponseStatus()) {
            self::RESPONSE_STATUS_ACCEPTED => 'ACCEPTED',
            self::RESPONSE_STATUS_DECLINED => 'DECLINED',
            self::RESPONSE_STATUS_TENTATIVE => 'TENTATIVE',
            default => null
        };
    }

    public function getICalType(): ?string
    {
        return match ($this->getType()) {
            self::TYPE_OPTIONAL => 'OPT-PARTICIPANT',
            self::TYPE_ORGANIZER => 'CHAIR',
            self::TYPE_REQUIRED => 'REQ-PARTICIPANT',
            default => null
        };
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): EventAttendee
    {
        $this->name = $name;

        return $this;
    }

    public function getResponseDate(): ?int
    {
        return $this->responseDate;
    }

    public function setResponseDate(?int $responseDate): EventAttendee
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    public function getResponseStatus(): ?int
    {
        return $this->responseStatus;
    }

    public function setResponseStatus(?int $responseStatus): EventAttendee
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): EventAttendee
    {
        $this->type = $type;

        return $this;
    }
}