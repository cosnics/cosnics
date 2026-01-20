<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Architecture\Domain;

/**
 * @package Chamilo\Application\Calendar\Extension\Office365\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event
{
    private \Microsoft\Graph\Generated\Models\Event $sourceEvent;

    public function getSourceEvent(): \Microsoft\Graph\Generated\Models\Event
    {
        return $this->sourceEvent;
    }

    public function setSourceEvent(\Microsoft\Graph\Generated\Models\Event $sourceEvent): static
    {
        $this->sourceEvent = $sourceEvent;

        return $this;
    }
}
