<?php

namespace Chamilo\Application\Weblcms\Bridge\GradeBook;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\DataClass\Publication as GradeBookPublication;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service\GradeBookPublicationService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\GradeBookItemService;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\GradeBookItemScoreService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;


/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookServiceBridge implements GradeBookServiceBridgeInterface
{
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
     * @var GradeBookPublication
     */
    protected $gradebookPublication;

    /**
     * @var bool
     */
    protected $canEditGradebook;

    /**
     * @param GradeBookPublicationService $publicationService
     * @param GradeBookItemService $gradeBookItemService
     * @param GradeBookItemScoreService $gradeBookItemScoreService
     */
    public function __construct(GradeBookPublicationService $publicationService, GradeBookItemService $gradeBookItemService, GradeBookItemScoreService $gradeBookItemScoreService)
    {
        $this->publicationService = $publicationService;
        $this->gradeBookItemService = $gradeBookItemService;
        $this->gradeBookItemScoreService = $gradeBookItemScoreService;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param Course $course
     *
     * @return GradeBookServiceBridge
     */
    public function setCourse(Course $course): GradeBookServiceBridge
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @param GradeBookPublication $gradebookPublication
     */
    public function setGradeBookPublication(GradeBookPublication $gradebookPublication)
    {
        $this->gradebookPublication = $gradebookPublication;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        return new ContextIdentifier(get_class($this->gradebookPublication), $this->contentObjectPublication->getId());
    }

    /**
     * @return bool
     */
    public function canEditGradeBook(): bool
    {
        return $this->canEditGradebook;
    }

    /**
     * @param bool $canEditGradebook
     */
    public function setCanEditGradebook(bool $canEditGradebook = true)
    {
        $this->canEditGradebook = $canEditGradebook;
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return array
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array
    {
        return $this->publicationService->getTargetUserIds($this->contentObjectPublication, $filterParameters);
    }

    /**
     * @return string
     */
    public function getContextTitle(): string
    {
        return $this->course instanceof Course ? $this->course->get_title() : '';
    }

    /**
     * @return GradeBookItem[]
     */
    public function findPublicationGradeBookItems()
    {
        return $this->gradeBookItemService->getGradeBookItemsForCourse($this->course);
    }

    /**
     * @param GradeBookItem $gradeBookItem
     *
     * @return array
     */
    public function findScores(GradeBookItem $gradeBookItem): array
    {
        return $this->gradeBookItemScoreService->getScores($gradeBookItem, $this->getTargetUserIds());
    }
}
