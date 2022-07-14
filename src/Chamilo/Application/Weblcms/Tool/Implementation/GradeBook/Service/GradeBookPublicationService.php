<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Service
 *
 * @author Stefan Gabriels - Hogeschool Gent
 */
class GradeBookPublicationService
{
    const TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\GradeBook';

    /**
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @param PublicationRepository $publicationRepository
     * @param PublicationService $publicationService
     * @param LearningPathService $learningPathService
     * @param LearningPathStepContextService $learningPathStepContextService
     * @param Translator $translator
     */
    public function __construct(PublicationRepository $publicationRepository, PublicationService $publicationService, LearningPathService $learningPathService, LearningPathStepContextService $learningPathStepContextService, Translator $translator)
    {
        $this->publicationRepository = $publicationRepository;
        $this->publicationService = $publicationService;
        $this->learningPathService = $learningPathService;
        $this->learningPathStepContextService = $learningPathStepContextService;
        $this->translator = $translator;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPublicationByContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->publicationRepository->findPublicationByContentObjectPublication($contentObjectPublication);
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param FilterParameters|null $filterParameters
     *
     * @return array
     */
    public function getTargetUserIds(ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters = null): array
    {
        return $this->publicationRepository->getTargetUserIds($contentObjectPublication, $filterParameters);
    }

    /**
     * @param User $user
     * @param ContentObjectPublication $contentObjectPublication
     * @param array|null $targetUserIds
     *
     * @return bool
     */
    public function isUserSubscribedToPublication(User $user, ContentObjectPublication $contentObjectPublication, array $targetUserIds = null): bool
    {
        if (is_null($targetUserIds))
        {
            $targetUserIds = $this->getTargetUserIds($contentObjectPublication);
        }
        return in_array($user->getId(), $targetUserIds);
    }

    /**
     * @param Course $course
     *
     * @return GradeBookItem[]
     */
    public function getGradeBookItemsForCourse(Course $course): array
    {
        $courseBreadcrumbs = $this->getCourseBreadcrumbs($course);

        $publicationsByCourse = $this->publicationService->getPublicationsByCourse($course);
        $publicationGradebookItems = array();

        foreach ($publicationsByCourse as $publication)
        {
            $contentObject = $publication->get_content_object();

            if ($contentObject instanceof LearningPath)
            {
                $publicationGradebookItems = $this->addLearningPathGradeBookItems($publication, $courseBreadcrumbs, $contentObject, $publicationGradebookItems);
            }

            if (!$this->isScorableObject($contentObject))
            {
                continue;
            }

            $publicationGradebookItems[] = $this->getPublicationGradeBookItem($publication, $courseBreadcrumbs);
        }

        return $publicationGradebookItems;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param array $courseBreadcrumbs
     *
     * @return GradeBookItem
     */
    protected function getPublicationGradeBookItem(ContentObjectPublication $publication, array $courseBreadcrumbs): GradeBookItem
    {
        return (new GradeBookItem())
            ->setContextClass(ContentObjectPublication::class)
            ->setContextId($publication->getId())
            ->setType($publication->get_tool())
            ->setTitle($publication->get_content_object()->get_title())
            ->setBreadcrumb(array_merge([
                $this->translator->trans($publication->get_tool() . 'ToolTypeName', [], self::TRANSLATION_CONTEXT)],
                $courseBreadcrumbs[$publication->get_category_id()]));
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param array $courseBreadcrumbs
     * @param LearningPath $contentObject
     * @param GradeBookItem[] $publicationGradebookItems
     *
     * @return GradeBookItem[]
     */
    public function addLearningPathGradeBookItems(ContentObjectPublication $contentObjectPublication, array $courseBreadcrumbs, LearningPath $contentObject, array $publicationGradebookItems): array
    {
        $treeNodes = $this->learningPathService->getTree($contentObject)->getTreeNodes();
        foreach ($treeNodes as $treeNode)
        {
            $gradebookItem = $this->getLearningPathTreeNodeGradeBookItem($contentObjectPublication, $treeNode);
            if (!empty($gradebookItem))
            {
                $breadcrumb = $courseBreadcrumbs[$contentObjectPublication->get_category_id()];
                $gradebookItem->setBreadcrumb(array_merge(
                    [$this->translator->trans($contentObjectPublication->get_tool() . 'ToolTypeName', [], self::TRANSLATION_CONTEXT)],
                    $breadcrumb, $gradebookItem->getBreadcrumb()));
                $publicationGradebookItems[] = $gradebookItem;
            }
        }
        return $publicationGradebookItems;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param TreeNode $treeNode
     *
     * @return GradeBookItem|null
     */
    protected function getLearningPathTreeNodeGradeBookItem(ContentObjectPublication $contentObjectPublication, TreeNode $treeNode): ?GradeBookItem
    {
        $contentObject = $treeNode->getContentObject();

        if (!$this->isScorableObject($contentObject))
        {
            return null;
        }

        $stepContext = $this->learningPathStepContextService->getOrCreateLearningPathStepContext($treeNode->getId(), ContentObjectPublication::class_name(), $contentObjectPublication->getId());
        return (new GradeBookItem())
            ->setContextClass(LearningPathStepContext::class)
            ->setContextId($stepContext->getId())
            ->setType($contentObject->class_name(false))
            ->setTitle($contentObject->get_title())
            ->setBreadcrumb($this->createTreeNodeBreadcrumb($treeNode));
    }

    protected function isScorableObject(ContentObject $contentObject)
    {
        return $contentObject instanceof Assignment || $contentObject instanceof Assessment || $contentObject instanceof Evaluation;
    }

    /**
     * @param Course $course
     * @return array
     */
    public function getCourseBreadcrumbs(Course $course)
    {
        $categories = array();
        foreach ($this->publicationService->getPublicationCategoriesForCourse($course) as $category)
        {
            $categories[$category->getId()] = $category;
        }

        $breadcrumbs = array();
        foreach ($categories as $key => $category)
        {
            $breadcrumbs[$key] = $this->createCourseBreadcrumb($category, $categories);
        }
        $breadcrumbs[0] = [];
        return $breadcrumbs;
    }

    /**
     * @param $category
     * @param array $categories
     *
     * @return array
     */
    protected function createCourseBreadcrumb($category, array $categories): array
    {
        $breadcrumb = array();
        $breadcrumb[] = $category->get_name();
        while ($category->get_parent() != 0)
        {
            $category = $categories[$category->get_parent()];
            $breadcrumb[] = $category->get_name();
        }
        return array_reverse($breadcrumb);
    }

    /**
     * @param TreeNode $treeNode
     *
     * @return array
     */
    protected function createTreeNodeBreadcrumb(TreeNode $treeNode): array
    {
        $parentNodes = $treeNode->getParentNodes();
        $breadcrumb = array();
        foreach ($parentNodes as $node)
        {
            $breadcrumb[] = $node->getContentObject()->get_title();
        }
        return $breadcrumb;
    }
}