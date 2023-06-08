<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Symfony\Component\Translation\Translator;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 *
 * @package Chamilo\Application\Weblcms\Service\Home
 */
class AssignmentSubmissionsBlockRenderer extends BlockRenderer
{
    protected AssignmentService $assignmentService;

    protected PublicationRepository $publicationRepository;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, AssignmentService $assignmentService,
        PublicationRepository $publicationRepository, ElementRightsService $elementRightsService
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService);

        $this->assignmentService = $assignmentService;
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $translator = $this->getTranslator();
        $user_id = $user->getId();

        // Retrieve the assignments of the user
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLISHER_ID
            ), new StaticConditionVariable($user_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable('Assignment')
        );

        $condition = new AndCondition($conditions);

        $assignment_publications_resultset = WeblcmsDataManager::retrieves(
            ContentObjectPublication::class, new DataClassRetrievesParameters($condition)
        );

        if ($assignment_publications_resultset->count() == 0)
        {
            return $translator->trans('YouDoNotOwnAnyAssignments', [], Manager::CONTEXT);
        }

        $items = [];

        /** @var ContentObjectPublication[] $publicationsById */
        $publicationsById = [];
        $assignmentPublicationsById = [];

        foreach ($assignment_publications_resultset as $publication)
        {
            $publicationsById[$publication->getId()] = $publication;
        }

        /** @var Publication[] $assignmentPublications */
        $assignmentPublications =
            $this->getPublicationRepository()->findPublicationsByContentObjectPublicationIdentifiers(
                array_keys($publicationsById)
            );

        foreach ($assignmentPublications as $assignmentPublication)
        {
            $assignmentPublicationsById[$assignmentPublication->getPublicationId()] = $assignmentPublication;
        }

        foreach ($publicationsById as $publication)
        {
            // Retrieve last time the publication was accessed
            $course_tool = WeblcmsDataManager::retrieve_course_tool_by_name($publication->get_tool());
            $last_access_time = $this->getLastVisit(
                $user_id, $publication->get_course_id(), $course_tool->getId(), $publication->get_category_id(),
                $publication->getId()
            );

            $item = [];

            $assignmentPublication = $assignmentPublicationsById[$publication->getId()];
            $entityType = ($assignmentPublication instanceof Publication) ? $assignmentPublication->getEntityType() :
                Entry::ENTITY_TYPE_USER;

            $count = $this->getAssignmentService()->countEntriesByContentObjectPublicationWithCreatedDateLargerThan(
                $publication, $entityType, $last_access_time
            );

            $object = $publication->getContentObject();
            $item['title'] = $object->get_title();

            $parameters = [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_COURSE => $publication->get_course_id(),
                Application::PARAM_ACTION => Manager::ACTION_VIEW_COURSE,
                Manager::PARAM_TOOL => 'Assignment',
                Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->getId(),
                Manager::PARAM_CATEGORY => $publication->get_category_id()
            ];

            $item['link'] = $this->getUrlGenerator()->fromParameters($parameters);

            if ($count)
            {
                $item['count'] = $count;
                $items[] = $item;
            }
        }

        $html = $this->displayNewItems($items);

        if (count($html) == 0)
        {
            return $translator->trans('NoNewSubmissionsSinceLastVisit', [], Manager::CONTEXT);
        }

        return implode('', $html);
    }

    public function displayNewItems($items): array
    {
        $html = [];

        foreach ($items as $item)
        {
            $html[] = '<a href="' . $item['link'] . '">' . $item['title'] . '</a>';
            $html[] = ' <span class="badge">' . $item['count'] . '</span><br />';
        }

        return $html;
    }

    protected function getAssignmentService(): AssignmentService
    {
        return $this->assignmentService;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    private function getLastVisit($user_id, $course_id, $tool_id, $category_id, $publication_id): int
    {
        $course_visit = new CourseVisit();
        $course_visit->set_user_id($user_id);
        $course_visit->set_course_id($course_id);
        $course_visit->set_tool_id($tool_id);
        $course_visit->set_category_id($category_id);
        $course_visit->set_publication_id($publication_id);
        $course_visit = $course_visit->retrieve_course_visit_with_current_data(false);

        if (!$course_visit)
        {
            return 0;
        }

        return $course_visit->get_last_access_date();
    }

    protected function getPublicationRepository(): PublicationRepository
    {
        return $this->publicationRepository;
    }
}
