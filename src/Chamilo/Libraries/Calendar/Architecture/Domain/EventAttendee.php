<?php
namespace Chamilo\Libraries\Calendar\Architecture\Domain;

use Chamilo\Libraries\Calendar\Architecture\Enum\AttendeeTypeEnum;
use Chamilo\Libraries\Calendar\Architecture\Enum\ResponseStatusEnum;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventAttendee
{
    private string $email;

    private string $name;

    private ?int $responseDate;

    private ?ResponseStatusEnum $responseStatus;

    private ?AttendeeTypeEnum $type;

    public function __construct(
        string $email, string $name, ?AttendeeTypeEnum $type, ?ResponseStatusEnum $responseStatus, ?int $responseDate
    )
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

    public function getResponseStatus(): ?ResponseStatusEnum
    {
        return $this->responseStatus;
    }

    public function setResponseStatus(?int $responseStatus): EventAttendee
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    public function getType(): AttendeeTypeEnum
    {
        return $this->type;
    }

    public function setType(AttendeeTypeEnum $type): EventAttendee
    {
        $this->type = $type;

        return $this;
    }
}