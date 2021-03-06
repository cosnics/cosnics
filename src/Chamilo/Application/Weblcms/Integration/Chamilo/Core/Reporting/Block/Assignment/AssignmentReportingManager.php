<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\FeedbackService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
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
     * @param $date int The date to be formatted.
     * @param $critical_date int The date that is used to decide whether $date is later.
     *
     * @return string The date in coloured HTML format.
     */
    protected function format_date_html($date, $critical_date = null)
    {
        if ($date <= 0)
        {
            return null;
        }

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
    public function getCourseId()
    {
        return $this->getRequest()->getFromUrl(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
    }

    /**
     * Retrieves the publication id from the url.
     *
     * @return int the publication id.
     */
    public function getPublicationId()
    {
        return $this->getRequest()->getFromUrl(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | ContentObjectPublication
     */
    public function getContentObjectPublication()
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->getPublicationId()
        );
    }

    /**
     * Retrieves the submitter type from the url.
     *
     * @return int the submitter type.
     */
    public function getEntityType()
    {
        return $this->getRequest()->getFromUrl(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE,
            Entry::ENTITY_TYPE_USER
        );
    }

    /**
     * Retrieves the target id from the url.
     *
     * @return int the target id.
     */
    public function getEntityId()
    {
        return $this->getRequest()->getFromUrl(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID
        );
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
     * @param int $courseId
     * @param int $publicationId
     * @param int $entityType
     * @param int $entityId
     * @param int $entryId
     *
     * @return string
     */
    protected function getEntryUrl($courseId, $publicationId, $entityType, $entityId, $entryId)
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
        $params[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID] = $entryId;

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

        $publications = !is_null($entityType) ?
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
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceInterface
     */
    protected function getEntityServiceForEntityType($entityType)
    {
        return $this->getEntityServiceManager()->getEntityServiceByType($entityType);
    }

    /**
     * @param string $url
     * @param string $title
     * @param string $target
     *
     * @return string
     */
    protected function createLink($url, $title, $target = null)
    {
        return '<a href="' . $url . '"' . (!empty($target) ? ' target="' . $target . '" ' : '') . '>' . $title . '</a>';
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService
     */
    protected function getAssignmentService()
    {
        return $this->getService(AssignmentService::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\FeedbackService
     */
    protected function getFeedbackService()
    {
        return $this->getService(FeedbackService::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager
     */
    protected function getEntityServiceManager()
    {
        return $this->getService(
            'Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager'
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
