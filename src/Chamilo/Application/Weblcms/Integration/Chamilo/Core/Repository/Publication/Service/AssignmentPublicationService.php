<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;


use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\File\Redirect;

/**
 * Class with common functionality for URL and location building for publications of content objects in assignments (both in learning paths and regular)
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentPublicationService implements AssignmentPublicationServiceInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\PublicationService
     */
    protected $publicationService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var string
     */
    protected $publicationContext;

    /**
     * AssignmentPublicationService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService $treeNodeDataService
     * @param string $publicationContext
     */
    public function __construct(
        AssignmentService $assignmentService,
        \Chamilo\Application\Weblcms\Service\PublicationService $publicationService,
        \Chamilo\Application\Weblcms\Service\CourseService $courseService,
        ContentObjectRepository $contentObjectRepository, TreeNodeDataService $treeNodeDataService,
        string $publicationContext
    )
    {
        $this->publicationService = $publicationService;
        $this->courseService = $courseService;
        $this->publicationContext = $publicationContext;
        $this->assignmentService = $assignmentService;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * @return string
     */
    public function getPublicationContext()
    {
        return $this->publicationContext;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $attributes
     * @param string $prefix
     *
     * @throws \Chamilo\Core\Repository\ContentObject\LearningPath\Exception\TreeNodeNotFoundException
     */
    protected function addLocationForEntry(Entry $entry, Attributes $attributes, string $prefix)
    {
        $location = $prefix . $entry->getContentObject()->get_title();
        $url = null;

        if($entry instanceof \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry)
        {
            $publication = $this->publicationService->getPublication($entry->getContentObjectPublicationId());
            if(!$publication instanceof ContentObjectPublication)
            {
                return;
            }

            $course = $this->courseService->getCourseById($publication->get_course_id());

            $location = $prefix . $course->get_title() . ' > ' . $publication->get_content_object()->get_title();

            $redirect = new Redirect(
                [
                    \Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                    \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course->getId(),
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Assignment',
                    \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $publication->getId(),
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY,
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE => $entry->getEntityType(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID => $entry->getEntityId(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry->getId()
                ]
            );

            $url = $redirect->getUrl();
        }
        elseif($entry instanceof \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry)
        {
            $publication = $this->publicationService->getPublication($entry->getContentObjectPublicationId());
            if(!$publication instanceof ContentObjectPublication)
            {
                return;
            }

            $course = $this->courseService->getCourseById($publication->get_course_id());
            $treeNode = $this->treeNodeDataService->getTreeNodeDataById($entry->getTreeNodeDataId());
            $treeNodeContentObject = $this->contentObjectRepository->findById($treeNode->getContentObjectId());

            $location = $prefix . $course->get_title() . ' > ' . $publication->get_content_object()->get_title() . ' > '
                . $treeNodeContentObject->get_title();

            $redirect = new Redirect(
                [
                    \Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                    \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course->getId(),
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'LearningPath',
                    \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION => $publication->getId(),
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID => $entry->getTreeNodeDataId(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY,
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE => $entry->getEntityType(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID => $entry->getEntityId(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry->getId()
                ]
            );

            $url = $redirect->getUrl();
        }

        $attributes->set_location($location);
        $attributes->set_url($url);
    }

}
