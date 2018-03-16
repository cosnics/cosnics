<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Hogent\Application\Assignment\DataTransferObject\AssignmentPublication;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of scores of each assignment per
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssignmentUserScoresBlock extends AssignmentReportingManager
{

    /**
     * @var ReportingData
     */
    private $reporting_data;

    public function count_data()
    {
        if (!isset($this->reporting_data))
        {
            $this->reporting_data = new ReportingData();

            $course_id = $this->get_course_id();

            $publications = $this->retrieveAssignmentPublicationsForCourseAndEntityType(
                $course_id, $this->getEntityType()
            );

            $publicationTitlesById = $this->determineReportingHeaders($publications);
            $entities = $this->retrieveEntitiesForCourse($course_id);

            foreach ($entities as $key => $entity)
            {
                $this->reporting_data->add_category($key);

                $this->reporting_data->add_data_category_row(
                    $key,
                    Translation::get('Name'),
                    $this->renderEntityName($entity)
                );

                $entityId = $entity[DataClass::PROPERTY_ID];

                foreach ($publications as $publication)
                {
                    $publicationId = $publication[ContentObjectPublication::PROPERTY_ID];

                    $publicationObject = new ContentObjectPublication();
                    $publicationObject->setId($publicationId);

                    $title = $publicationTitlesById[$publicationId];

                    if (!$this->getAssignmentService()->countEntriesForContentObjectPublicationEntityTypeAndId(
                        $publicationObject, $this->getEntityType(), $entityId
                    ))
                    {
                        $this->reporting_data->add_data_category_row($key, $title, null);
                        continue;
                    }

                    $lastScore = $this->getAssignmentService()->getLastScoreForContentObjectPublicationEntityTypeAndId(
                        $publicationObject, $this->getEntityType(), $entityId
                    );

                    if ($lastScore)
                    {
                        $score = $this->format_score_html($lastScore);

                        $this->reporting_data->add_data_category_row($key, $title, $score);
                    }
                    else
                    {
                        $link = $this->getEntityUrl($course_id, $publicationId, $this->getEntityType(), $entityId);

                        $this->reporting_data->add_data_category_row(
                            $key,
                            $title,
                            '<span style="text-decoration: blink;"><b><a href="' . $link .
                            '" target="_blank">?</a></b></span>'
                        );
                    }
                }
            }

            $this->reporting_data->hide_categories();
        }

        return $this->reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    /**
     * @param int $course_id
     * @param int $entityType
     *
     * @return array
     */
    protected function retrieveAssignmentPublicationsForCourseAndEntityType($course_id, $entityType)
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

        $publications = $this->filterPublicationsForEntityType($publication_resultset, $entityType);

        return $publications;
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

        $params[Manager::PARAM_ACTION] = Manager::ACTION_ENTRY;
        $params[Manager::PARAM_ENTITY_TYPE] = $entityType;
        $params[Manager::PARAM_ENTITY_ID] = $entityId;

        $redirect = new Redirect($params);
        $link = $redirect->getUrl();

        return $link;
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
     * @return int
     */
    protected function getEntityType()
    {
        return Entry::ENTITY_TYPE_USER;
    }

    /**
     * @param mixed $entity
     *
     * @return string
     */
    protected function renderEntityName($entity)
    {
        return \Chamilo\Core\User\Storage\DataClass\User::fullname(
            $entity[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_FIRSTNAME],
            $entity[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_LASTNAME]
        );
    }

    /**
     * @param int $course_id
     *
     * @return string[][]
     */
    protected function retrieveEntitiesForCourse($course_id)
    {
        return CourseDataManager::retrieve_all_course_users($course_id)->as_array();
    }

    /**
     * @param $publications
     *
     * @return string[]
     */
    protected function determineReportingHeaders($publications)
    {
        $publicationTitlesById = [];

        // set the table headers
        $headings = array();
        $headings[] = Translation::get('Name');

        foreach ($publications as $publication)
        {
            $publicationId = $publication[ContentObjectPublication::PROPERTY_ID];

            if (count($publications) > 5)
            {
                $publicationTitlesById[$publicationId] = '<div id="' . $publicationId . '">' .
                    substr($publication[ContentObject::PROPERTY_TITLE], 0, 14) . '</div>';
            }
            else
            {
                $publicationTitlesById[$publicationId] = '<div id="' . $publicationId . '">' .
                    $publication[ContentObject::PROPERTY_TITLE] . '</div>';
            }

            $headings[] = $publicationTitlesById[$publicationId];
        }

        $this->reporting_data->set_rows($headings);

        return $publicationTitlesById;
    }
}
