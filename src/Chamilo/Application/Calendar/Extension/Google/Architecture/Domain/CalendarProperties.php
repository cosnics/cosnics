<?php
namespace Chamilo\Application\Calendar\Extension\Google\Architecture\Domain;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarProperties
{

    private string $description;

    private string $summary;

    private string $timeZone;

    public function __construct(string $summary, string $description, string $timeZone)
    {
        $this->summary = $summary;
        $this->description = $description;
        $this->timeZone = $timeZone;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    public function setTimeZone(string $timeZone): static
    {
        $this->timeZone = $timeZone;

        return $this;
    }
}