<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This manager creates a new level between the reporting responsible for the Assignment Tool and the Weblcms reporting
 * system.
 * It groups common code between the reporting blocks in one place so that each block is no longer responsible
 * for code that should be shared.
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
abstract class AssignmentReportingManager extends ToolBlock
{

    /**
     * Formats the colour of the score with reference to the platform setting passing percentage.
     *
     * @param $score int The score to be formatted.
     *
     * @return string The score in coloured HTML format.
     */
    protected function format_score_html($score)
    {
        if ($score !== null)
        {
            $colour = null;

            $passingPercentage = Configuration::getInstance()->get_setting(
                array('Chamilo\Core\Admin', 'passing_percentage')
            );

            if ($score < $passingPercentage)
            {
                $colour = 'red';
            }
            else
            {
                $colour = 'green';
            }

            return '<span style="color:' . $colour . '">' . round($score, 2) . '%</span>';
        }
        else
        {
            return '-';
        }
    }

    /**
     * Formats a date and colours it red when it is later than the critical date.
     *
     * @param $date type The date to be formatted.
     * @param $critical_date type The date that is used to decide whether $date is later.
     *
     * @return string The date in coloured HTML format.
     */
    protected function format_date_html($date, $critical_date)
    {
        $formatted_date = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $date
        );

        if ($date > $critical_date)
        {
            return '<span style="color:red">' . $formatted_date . '</span>';
        }

        return $formatted_date;
    }

    /**
     * Retrieves the course id from the url.
     *
     * @return int the course id.
     */
    public function get_course_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
    }

    /**
     * Retrieves the publication id from the url.
     *
     * @return int the publication id.
     */
    public function get_publication_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     * Retrieves the submitter type from the url.
     *
     * @return int the submitter type.
     */
    public function get_submitter_type()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE);
    }

    /**
     * Retrieves the target id from the url.
     *
     * @return int the target id.
     */
    public function get_target_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID);
    }

    /**
     * Returns the average score from the given score trackers.
     *
     * @param $score_trackers \application\weblcms\integration\core\tracking\tracker\SubmissionScore The score trackers
     *        used to
     *        the average score
     *
     * @return int The average score
     */
    protected function get_avg_score($score_trackers)
    {
        if (!$score_trackers || count($score_trackers) < 1)
        {
            return null;
        }
        $score = null;

        foreach ($score_trackers as $score_tracker)
        {
            $score += $score_tracker->get_score();
        }

        return round($score / count($score_trackers), 2);
    }

    /**
     * Returns the minimum score from the given score trackers.
     *
     * @param $score_trackers \application\weblcms\integration\core\tracking\tracker\SubmissionScore The score trackers
     *        used to
     *        determine the minimum score
     *
     * @return int The minimum score
     */
    protected function get_min_score($score_trackers)
    {
        if (!$score_trackers || count($score_trackers) < 1)
        {
            return null;
        }
        $score = null;

        foreach ($score_trackers as $score_tracker)
        {
            if (is_null($score) || $score_tracker->get_score() < $score)
            {
                $score = $score_tracker->get_score();
            }
        }

        return round($score, 2);
    }

    /**
     * Returns the maximum score from the given score trackers.
     *
     * @param $score_trackers \application\weblcms\integration\core\tracking\tracker\SubmissionScore The score trackers
     *        used to
     *        determine the maximum score
     *
     * @return int The maximum score
     */
    protected function get_max_score($score_trackers)
    {
        if (!$score_trackers || count($score_trackers) < 1)
        {
            return null;
        }
        $score = null;

        foreach ($score_trackers as $score_tracker)
        {
            if (is_null($score) || $score_tracker->get_score() > $score)
            {
                $score = $score_tracker->get_score();
            }
        }

        return round($score, 2);
    }

    /**
     * Generates an HTML link to the assignment tool using the name of the submitter passed.
     *
     * @param $submitter_type int The type of the submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $submitter_id int The id of the submitter.
     *
     * @return string The HTML representation of the link.
     */
    protected function generate_submitter_name_link($submitter_type, $submitter_id)
    {
        $submitter_name = $this->get_submitter_name_by_id($submitter_type, $submitter_id);

        $params = array();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->get_course_id();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                Assignment::class_name(),
                true
            );
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->get_publication_id();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_BROWSE_SUBMISSIONS;
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE] = ContentObjectRenderer::TYPE_TABLE;
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $this->get_publication_id();
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID] = $submitter_id;
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE] =
            $submitter_type;

        $redirect = new Redirect($params);
        $link = $redirect->getUrl();

        return ' <a href="' . $link . '">' . $submitter_name . '</a>';
    }

    /**
     * Generates an HTML link to the assignment tool using the title of the submission in the submissions tracker
     * passed.
     *
     * @param $submission_tracker \application\weblcms\integration\core\tracking\tracker\AssignmentSubmission The
     *        submission
     *        tracker.
     *
     * @return string The HTML representation of the link.
     */
    protected function generate_submission_title_link($submission_tracker)
    {
        $submission_title = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $submission_tracker->get_content_object_id()
        )->get_title();

        $params = array();
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->get_course_id();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                Assignment::class_name(),
                true
            );
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_VIEW_SUBMISSION;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->get_publication_id();
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID] =
            $submission_tracker->get_submitter_id();
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE] =
            $submission_tracker->get_submitter_type();
        $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMISSION] =
            $submission_tracker->get_id();

        $redirect = new Redirect($params);
        $link = $redirect->getUrl();

        return '<a href="' . $link . '">' . $submission_title . '</a>';
    }

    /**
     * Generates an HTML link to the user tool using tha name of the user passed.
     *
     * @param $user_id int The id of the user.
     *
     * @return string The HTML representation of the link.
     */
    protected function generate_user_name_link($user_id)
    {
        $user_name = $this->get_submitter_name_by_id(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER,
            $user_id
        );

        $params = array();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->get_course_id();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = \Chamilo\Core\User\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Implementation\User\Manager::ACTION_USER_DETAILS;
        $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE] =
            ContentObjectPublicationListRenderer::TYPE_LIST;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;

        $redirect = new Redirect($params);
        $url_user_name = $redirect->getUrl();

        return '<a href="' . $url_user_name . '">' . $user_name . '</a>';
    }

    /**
     * Obtains the name of the submitter passed.
     *
     * @param $submitter_type int The type of the submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $submitter_id int The id of the submitter.
     *
     * @return string The name of the submitter.
     */
    protected function get_submitter_name_by_id($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                return CourseGroupDataManager::retrieve_by_id(CourseGroup::class_name(), $submitter_id)->get_name();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $submitter_id)
                    ->get_name();
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    (int) $submitter_id
                );

                if ($user instanceof User)
                {
                    return $user->get_fullname();
                }

                return null;
        }
    }

    /**
     * @param int $courseId
     * @param int $publicationId
     * @param int $entityType
     * @param int $entityId
     *
     * @return string
     */
    protected function getEntityUrl($courseId, $publicationId, $entityType, $entityId)
    {
        $params = array();

        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $courseId;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(
                Assignment::class_name(),
                true
            );
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $publicationId;

        $params[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION] =
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY;

        $params[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE] = $entityType;
        $params[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID] = $entityId;

        $redirect = new Redirect($params);
        $link = $redirect->getUrl();

        return $link;
    }

    /**
     * @param int $course_id
     * @param int $publicationId
     *
     * @return string
     */
    protected function getAssignmentUrl($course_id, $publicationId)
    {
        $params = array();

        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;

        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assignment::class_name(), true);

        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $publicationId;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY;

        $redirect = new Redirect($params);

        return $redirect->getUrl();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return string
     */
    protected function getAssignmentUrlForContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        return $this->getAssignmentUrl($contentObjectPublication->get_course_id(), $contentObjectPublication->getId());
    }

    /**
     * @param int $course_id
     * @param int $entityType
     *
     * @return array
     */
    protected function retrieveAssignmentPublicationsForCourse($course_id, $entityType = null)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_TOOL
            ),
            new StaticConditionVariable(
                ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assignment::class_name())
            )
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(Assignment::class)
        );

        $condition = new AndCondition($conditions);
        $order_by = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_MODIFIED_DATE
            )
        );

        $publication_resultset =
            \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
                $condition,
                $order_by
            );

        $publications = !empty($entityType) ?
            $this->filterPublicationsForEntityType($publication_resultset, $entityType) :
            $publication_resultset->as_array();

        return $publications;
    }

    /**
     * @param ResultSet $publication_resultset
     * @param int $entityType
     *
     * @return array
     */
    protected function filterPublicationsForEntityType($publication_resultset, $entityType)
    {
        $publicationsById = [];
        $assignmentPublicationsById = [];

        while ($publication = $publication_resultset->next_result())
        {
            $publicationsById[$publication[DataClass::PROPERTY_ID]] = $publication;
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

        $publications = [];

        foreach ($publicationsById as $publicationId => $publication)
        {
            $assignmentPublication = $assignmentPublicationsById[$publicationId];
            if ($assignmentPublication instanceof Publication &&
                $assignmentPublication->getEntityType() == $entityType)
            {
                $publications[] = $publication;
            }
        }

        return $publications;
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\EntityServiceInterface
     */
    protected function getEntityServiceForEntityType($entityType)
    {
        switch($entityType)
        {
            case Entry::ENTITY_TYPE_COURSE_GROUP:
                return $this->getCourseGroupEntityService();
            case Entry::ENTITY_TYPE_PLATFORM_GROUP:
                return $this->getPlatformGroupEntityService();
            case Entry::ENTITY_TYPE_USER:
            default:
                return $this->getUserEntityService();

        }
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
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\UserEntityService
     */
    protected function getUserEntityService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entity.user_entity_service'
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\PlatformGroupEntityService
     */
    protected function getPlatformGroupEntityService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entity.platform_group_entity_service'
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\Entity\CourseGroupEntityService
     */
    protected function getCourseGroupEntityService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entity.course_group_entity_service'
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
