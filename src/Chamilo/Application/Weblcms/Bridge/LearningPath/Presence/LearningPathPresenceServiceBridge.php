<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Presence;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathPresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Storage\Repository\PublicationRepository;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Presence
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathPresenceServiceBridge implements LearningPathPresenceServiceBridgeInterface
{
    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $canEditPresence;

    public function __construct(LearningPathStepContextService $learningPathStepContextService, PublicationRepository $publicationRepository)
    {
        $this->learningPathStepContextService = $learningPathStepContextService;
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @param ContentObjectPublication $publication
     */
    public function setContentObjectPublication(ContentObjectPublication $publication)
    {
        $this->contentObjectPublication = $publication;
    }

    /**
     * @param int $stepId
     * @return ContextIdentifier
     */
    public function getContextIdentifier(int $stepId): ContextIdentifier
    {
        $publicationClass = ContentObjectPublication::class_name();
        $publicationId = $this->contentObjectPublication->getId();
        $learningPathStepContext = $this->learningPathStepContextService->getOrCreateLearningPathStepContext($stepId, $publicationClass, $publicationId);
        return new ContextIdentifier(get_class($learningPathStepContext), $learningPathStepContext->getId());
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
    public function setCanEditPresence($canEditPresence = true)
    {
        $this->canEditPresence = $canEditPresence;
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array
    {
        return $this->publicationRepository->getTargetUserIds($this->contentObjectPublication, $filterParameters);
    }
}