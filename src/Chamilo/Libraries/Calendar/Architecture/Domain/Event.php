<?php
namespace Chamilo\Libraries\Calendar\Architecture\Domain;

/**
 * An event in the personal calendar as a shell around concepts which exist in the integrating contexts
 *
 * @package Chamilo\Libraries\Calendar\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event
{
    /**
     * @var \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button[]
     */
    protected array $actions = [];

    /**
     * @var \Chamilo\Libraries\Calendar\Architecture\Domain\EventAttendee[]
     */
    private array $attendees;

    private ?string $content;

    private ?string $context;

    private ?int $endDate;

    private ?string $identifier;

    private ?string $location;

    private ?EventAttendee $organizer;

    private ?string $source;

    private ?int $startDate;

    private ?string $title;

    private ?string $url;

    /**
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\EventAttendee[] $attendees
     * @param \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button[] $actions
     */
    public function __construct(
        ?string $identifier = null, ?int $startDate = null, ?int $endDate = null, ?string $url = null, ?string $title = null,
        ?string $content = null, ?string $location = null, ?string $source = null, ?string $context = null,
        ?EventAttendee $organizer = null, array $attendees = [], array $actions = []
    )
    {
        $this->identifier = $identifier;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
        $this->location = $location;
        $this->source = $source;
        $this->context = $context;
        $this->organizer = $organizer;
        $this->attendees = $attendees;
        $this->actions = $actions;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function setActions(array $actions): Event
    {
        $this->actions = $actions;

        return $this;
    }

    public function getAttendees(): array
    {
        return $this->attendees;
    }

    public function setAttendees(array $attendees = []): Event
    {
        $this->attendees = $attendees;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): Event
    {
        $this->content = $content;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): Event
    {
        $this->context = $context;

        return $this;
    }

    public function getEndDate(): ?int
    {
        return $this->endDate;
    }

    public function setEndDate(?int $endDate): Event
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): Event
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): Event
    {
        $this->location = $location;

        return $this;
    }

    public function getOrganizer(): ?EventAttendee
    {
        return $this->organizer;
    }

    public function setOrganizer(?EventAttendee $organizer): Event
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): Event
    {
        $this->source = $source;

        return $this;
    }

    public function getStartDate(): ?int
    {
        return $this->startDate;
    }

    public function setStartDate(?int $startDate): Event
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): Event
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Event
    {
        $this->url = $url;

        return $this;
    }
}
