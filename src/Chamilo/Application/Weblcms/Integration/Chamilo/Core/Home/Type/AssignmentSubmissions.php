<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 */
class AssignmentSubmissions extends Block
{
    public function displayContent()
    {
        $user_id = $this->getUserId();

        // Retrieve the assignments of the user
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(),
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_PUBLISHER_ID
            ),
            new StaticConditionVariable($user_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(),
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_TOOL
            ),
            new StaticConditionVariable('Assignment')
        );

        $condition = new AndCondition($conditions);

        $assignment_publications_resultset = WeblcmsDataManager::retrieves(
            ContentObjectPublication::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        if ($assignment_publications_resultset->size() == 0)
        {
            return Translation::get('YouDoNotOwnAnyAssignments');
        }

        $items = array();

        /** @var ContentObjectPublication[] $publicationsById */
        $publicationsById = [];
        $assignmentPublicationsById = [];

        while ($publication = $assignment_publications_resultset->next_result())
        {
            $publicationsById[$publication->getId()] = $publication;
        }

        /** @var Publication[] $assignmentPublications */
        $assignmentPublications =
            $this->getPublicationRepository()->findPublicationsByContentObjectPublicationIdentifiers(
                array_keys($publicationsById)
            );

        foreach($assignmentPublications as $assignmentPublication)
        {
            $assignmentPublicationsById[$assignmentPublication->getPublicationId()] = $assignmentPublication;
        }

        foreach($publicationsById as $identifier => $publication)
        {
            // Retrieve last time the publication was accessed
            $course_tool = WeblcmsDataManager::retrieve_course_tool_by_name($publication->get_tool());
            $last_access_time = $this->getLastVisit(
                $user_id,
                $publication->get_course_id(),
                $course_tool->get_id(),
                $publication->get_category_id(),
                $publication->get_id()
            );

            $item = array();

            $assignmentPublication = $assignmentPublicationsById[$publication->getId()];
            $entityType = ($assignmentPublication instanceof Publication) ? $assignmentPublication->getEntityType() : Entry::ENTITY_TYPE_USER;

            $count = $this->getAssignmentService()->countEntriesByContentObjectPublicationWithCreatedDateLargerThan(
                $publication, $entityType, $last_access_time
            );

            $object = $publication->getContentObject();
            $item['title'] = $object->get_title();

            $parameters = array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $publication->get_course_id(),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Assignment',
                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id(),
                \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => $publication->get_category_id()
            );

            $redirect = new Redirect($parameters);

            $item['link'] = $redirect->getUrl();

            if ($count)
            {
                $item['count'] = $count;
                $items[] = $item;
            }
        }

        $html = $this->displayNewItems($items);

        if (count($html) == 0)
        {
            return Translation::get('NoNewSubmissionsSinceLastVisit');
        }

        return implode('', $html);
    }

    public function displayNewItems($items)
    {
        $html = array();

        foreach ($items as $item)
        {
            $html[] = '<a href="' . $item['link'] . '">' . $item['title'] . '</a>';
            $html[] = ' <span class="badge">' . $item['count'] .'</span><br />';
        }

        return $html;
    }

    private function getLastVisit($user_id, $course_id, $tool_id, $category_id, $publication_id)
    {
        $course_visit = new CourseVisit();
        $course_visit->set_user_id($user_id);
        $course_visit->set_course_id($course_id);
        $course_visit->set_tool_id($tool_id);
        $course_visit->set_publication_id($publication_id);
        $course_visit = $course_visit->retrieve_course_visit_with_current_data(false);

        if (!$course_visit)
        {
            return 0;
        }

        return $course_visit->get_last_access_date();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected function getAssignmentService()
    {
        return $this->getService(
            'chamilo.application.weblcms.integration.chamilo.core.tracking.service.assignment_service'
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository
     */
    protected function getPublicationRepository()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.storage.repository.publication_repository'
        );
    }
}
