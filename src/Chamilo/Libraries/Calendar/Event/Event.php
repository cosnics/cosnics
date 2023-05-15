<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Architecture\Traits\ClassContext;

/**
 * An event in the personal calendar as a shell around concepts which exist in the integrating contexts
 *
 * @package Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event
{
    use ClassContext;

    private ?string $content;

    private ?string $context;

    private ?int $endDate;

    private ?string $id;

    private ?string $location;

    private ?RecurrenceRules $recurrenceRules;

    private ?string $source;

    private ?int $startDate;

    private ?string $title;

    private ?string $url;

    public function __construct(
        ?string $id = null, ?int $startDate = null, ?int $endDate = null, ?RecurrenceRules $recurrenceRules = null,
        ?string $url = null, ?string $title = null, ?string $content = null, ?string $location = null,
        ?string $source = null, ?string $context = null
    )
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->recurrenceRules = $recurrenceRules ?: new RecurrenceRules();
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
        $this->location = $location;
        $this->source = $source;
        $this->context = $context;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content)
    {
        $this->content = $content;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context)
    {
        $this->context = $context;
    }

    public function getEndDate(): ?int
    {
        return $this->endDate;
    }

    public function setEndDate(?int $endDate)
    {
        $this->endDate = $endDate;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id)
    {
        $this->id = $id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location)
    {
        $this->location = $location;
    }

    public function getRecurrenceRules(): ?RecurrenceRules
    {
        return $this->recurrenceRules;
    }

    public function setRecurrenceRules(?RecurrenceRules $recurrenceRules)
    {
        $this->recurrenceRules = $recurrenceRules;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source)
    {
        $this->source = $source;
    }

    public function getStartDate(): ?int
    {
        return $this->startDate;
    }

    public function setStartDate(?int $startDate)
    {
        $this->startDate = $startDate;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url)
    {
        $this->url = $url;
    }
}
