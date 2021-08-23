<?php

namespace Chamilo\Application\Weblcms\Bridge\Presence;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\DataClass\Publication as PresencePublication;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
//use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
//use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
//use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceInterface;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Presence
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceServiceBridge implements PresenceServiceBridgeInterface
{
    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

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
     * @param PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
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
     * @return int[]
     */
    public function getTargetUserIds(): array
    {
        return DataManager::getPublicationTargetUserIds($this->contentObjectPublication->getId(), $this->contentObjectPublication->get_course_id());
    }
}
