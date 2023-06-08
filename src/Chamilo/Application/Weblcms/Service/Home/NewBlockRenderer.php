<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class NewBlockRenderer extends BlockRenderer
{
    public const TOOL_ANNOUNCEMENT = 'Announcement';
    public const TOOL_ASSIGNMENT = 'Assignment';
    public const TOOL_DOCUMENT = 'Document';

    public function displayContent(Element $block, ?User $user = null): string
    {
        $publications = $this->getContent($this->getToolName(), $user);

        if (count($publications) == 0)
        {
            $html = [];

            $html[] = '<div class="panel-body portal-block-content' . ($block->isVisible() ? '' : ' hidden') . '">';
            $html[] = $this->getTranslator()->trans('NoNewPublicationsSinceLastVisit', [], Manager::CONTEXT);
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return $this->displayNewItems($block, $publications);
    }

    /**
     * @param string[] $publication
     */
    public function displayNewItem(array $publication): string
    {
        $html = [];

        $html[] = '<a href="' . $this->getCourseViewerLink($publication) . '" class="list-group-item">';
        $html[] = $this->getBadgeContent($publication);
        $html[] = '<p class="list-group-item-text">' . $publication[ContentObject::PROPERTY_TITLE] . '</p>';
        $html[] = '<h5 class="list-group-item-heading">' . $publication[Course::PROPERTY_TITLE] . '</h5>';
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[][] $publications
     */
    public function displayNewItems(Element $block, array $publications): string
    {
        $html = [];

        $html[] = '<div class="list-group portal-block-content portal-block-new-list' .
            ($block->isVisible() ? '' : ' hidden') . '">';

        foreach ($publications as $publication)
        {
            $html[] = $this->displayNewItem($publication);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getBadgeContent(array $publication): string
    {
        return '<span class="badge badge-date">' .
            date('j M', (int) $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]) . '</span>';
    }

    /**
     * @return string[][]
     */
    public function getContent(string $tool, ?User $user = null): array
    {
        // All user courses for active course types
        $excludedCourseTypes = explode(
            ',', $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'excluded_course_types'])
        );
        $archiveCondition = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Course::class, Course::PROPERTY_COURSE_TYPE_ID), $excludedCourseTypes
            )
        );

        // All user courses
        $courses = CourseDataManager::retrieve_all_courses_from_user($user, $archiveCondition);

        $courseSettingsController = CourseSettingsController::getInstance();
        $uniquePublications = [];

        foreach ($courses as $course)
        {
            if ($courseSettingsController->get_course_setting(
                    $course, CourseSettingsConnector::VISIBILITY
                ) == 1)
            {
                $condition = $this->getPublicationConditions($course, $tool, $user);
                $course_module_id = WeblcmsDataManager::retrieve_course_tool_by_name($tool)->getId();
                $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
                    WeblcmsRights::TYPE_COURSE_MODULE, $course_module_id, $course->getId()
                );

                $entities = [];
                $entities[CourseGroupEntity::ENTITY_TYPE] = CourseGroupEntity::getInstance(
                    $course->getId()
                );
                $entities[UserEntityProvider::ENTITY_TYPE] = CourseUserEntity::getInstance();
                $entities[GroupEntityProvider::ENTITY_TYPE] = CoursePlatformGroupEntity::getInstance();

                $publications =
                    WeblcmsDataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                        $location, $entities, $condition, new OrderBy([
                            new OrderProperty(
                                new PropertyConditionVariable(
                                    ContentObjectPublication::class,
                                    ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
                                )
                            )
                        ])
                    );

                if ($publications == 0)
                {
                    continue;
                }

                foreach ($publications as $publication)
                {
                    $publication[Course::PROPERTY_TITLE] = $course->get_title();

                    $uniquePublications[$course->getId() . '.' . $publication[DataClass::PROPERTY_ID]] = $publication;
                }
            }
        }

        usort($uniquePublications, [$this, 'sortPublications']);

        return $uniquePublications;
    }

    /**
     * @return string[]
     */
    abstract public function getContentObjectTypes(): array;

    /**
     * @param string[] $publication
     */
    abstract public function getCourseViewerLink(array $publication): string;

    private function getPublicationConditions(Course $course, string $tool, ?User $user = null): AndCondition
    {
        $last_visit_date = WeblcmsDataManager::get_last_visit_date(
            $course->getId(), $user->getId(), $tool
        );

        $conditions = [];
        $conditions[] = WeblcmsDataManager::get_publications_condition(
            $course, $user, $tool, $this->getContentObjectTypes()
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLICATION_DATE
            ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($last_visit_date)
        );

        return new AndCondition($conditions);
    }

    abstract public function getToolName(): string;

    public function renderContentFooter(Element $block): string
    {
        return '';
    }

    public function renderContentHeader(Element $block): string
    {
        return '';
    }

    /**
     * @param string[] $publicationLeft
     * @param string[] $publicationRight
     *
     * @return int
     */
    public function sortPublications(array $publicationLeft, array $publicationRight): int
    {
        if ($publicationLeft[ContentObjectPublication::PROPERTY_MODIFIED_DATE] ==
            $publicationRight[ContentObjectPublication::PROPERTY_MODIFIED_DATE])
        {
            return 0;
        }
        elseif ($publicationLeft[ContentObjectPublication::PROPERTY_MODIFIED_DATE] >
            $publicationRight[ContentObjectPublication::PROPERTY_MODIFIED_DATE])
        {
            return - 1;
        }
        else
        {
            return 1;
        }
    }
}
