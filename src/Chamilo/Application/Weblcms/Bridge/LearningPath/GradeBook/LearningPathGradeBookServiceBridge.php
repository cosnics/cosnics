<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\GradeBook;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\GradeBook\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathGradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service\GradeBookPublicationService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\GradeBookItemService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\GradeBookItemScoreService;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\GradeBook
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathGradeBookServiceBridge implements LearningPathGradeBookServiceBridgeInterface
{
    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @var GradeBookPublicationService
     */
    protected $publicationService;

    /**
     * @var GradeBookItemService
     */
    protected $gradeBookItemService;

    /**
     * @var GradeBookItemScoreService
     */
    protected $gradeBookItemScoreService;

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
    protected $canEditGradeBook;

    /**
     * @param LearningPathStepContextService $learningPathStepContextService
     * @param GradeBookPublicationService $publicationService
     * @param GradeBookItemService $gradeBookItemService
     * @param GradeBookItemScoreService $gradeBookItemScoreService
     */
    public function __construct(LearningPathStepContextService $learningPathStepContextService, GradeBookPublicationService $publicationService, GradeBookItemService $gradeBookItemService, GradeBookItemScoreService $gradeBookItemScoreService)
    {
        $this->learningPathStepContextService = $learningPathStepContextService;
        $this->publicationService = $publicationService;
        $this->gradeBookItemService = $gradeBookItemService;
        $this->gradeBookItemScoreService = $gradeBookItemScoreService;
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
     * @return LearningPathGradeBookServiceBridge
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
    public function canEditGradeBook(): bool
    {
        return $this->canEditGradeBook;
    }

    /**
     * @param bool $canEditGradeBook
     */
    public function setCanEditGradeBook($canEditGradeBook = true)
    {
        $this->canEditGradeBook = $canEditGradeBook;
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

    /**
     * @return GradeBookItem[]
     * @throws \Exception
     */
    public function findPublicationGradeBookItems()
    {
        return $this->gradeBookItemService->getGradeBookItemsForLearningPath($this->contentObjectPublication);
    }

    /**
     * @param GradeBookItem $gradeBookItem
     *
     * @return array
     */
    public function findScores(GradeBookItem $gradeBookItem)
    {
        return $this->gradeBookItemScoreService->getScores($gradeBookItem, $this->getTargetUserIds());
    }
}
