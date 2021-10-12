<?php

namespace Chamilo\Application\Weblcms\Bridge\Presence;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication as PresencePublication;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Service\PublicationService;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;


/**
 * @package Chamilo\Application\Weblcms\Bridge\Presence
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceServiceBridge implements PresenceServiceBridgeInterface
{
    /**
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var PresencePublication
     */
    protected $presencePublication;

    /**
     * @var bool
     */
    protected $canEditPresence;

    /**
     * @param PublicationService $publicationService
     */
    public function __construct(PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param PresencePublication $presencePublication
     */
    public function setPresencePublication(PresencePublication $presencePublication)
    {
        $this->presencePublication = $presencePublication;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        return new ContextIdentifier(get_class($this->presencePublication), $this->contentObjectPublication->getId());
    }

    /**
     * @return bool
     */
    public function canEditPresence(): bool
    {
        return $this->canEditPresence;
    }

    /**
     * @param bool $canEditPresence
     */
    public function setCanEditPresence(bool $canEditPresence = true)
    {
        $this->canEditPresence = $canEditPresence;
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return array
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array
    {
        return $this->publicationService->getTargetUserIds($this->contentObjectPublication, $filterParameters);
    }
}
