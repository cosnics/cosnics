<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Presence;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathPresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\Presence\Service\PublicationService;


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
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var Course
     */
    protected $course;

    /**
     * @var bool
     */
    protected $canEditPresence;

    /**
     * @param LearningPathStepContextService $learningPathStepContextService
     * @param PublicationService $publicationService
     */
    public function __construct(LearningPathStepContextService $learningPathStepContextService, PublicationService $publicationService)
    {
        $this->learningPathStepContextService = $learningPathStepContextService;
        $this->publicationService = $publicationService;
    }

    /**
     * @param ContentObjectPublication $publication
     */
    public function setContentObjectPublication(ContentObjectPublication $publication)
    {
        $this->contentObjectPublication = $publication;
    }

    /**
     * @param Course $course
     *
     * @return LearningPathPresenceServiceBridge
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;

        return $this;
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
        return $this->publicationService->getTargetUserIds($this->contentObjectPublication, $filterParameters);
    }

    public function getContextTitle(): string
    {
        return $this->course instanceof Course ? $this->course->get_title() : '';
    }
}
