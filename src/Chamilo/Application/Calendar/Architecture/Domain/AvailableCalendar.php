<?php
namespace Chamilo\Application\Calendar\Architecture\Domain;

/**
 * @package Chamilo\Application\Calendar\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AvailableCalendar
{
    public function __construct(
        public string $type, public string $identifier, public string $name, public ?string $description = null
    )
    {
    }

    public function getUniqueIdentifier(): string
    {
        return md5(serialize([$this->type, $this->identifier]));
    }
}