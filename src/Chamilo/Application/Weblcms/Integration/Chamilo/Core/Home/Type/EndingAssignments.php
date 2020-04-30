<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 */
class EndingAssignments extends Block
{

    public function displayContent()
    {
        // deadline min 1 week (60 * 60 * 24 * 7)
        $deadline = time() + 604800;

        $courses = CourseDataManager::retrieve_all_courses_from_user($this->getUser());

        while ($course = $courses->next_result())
        {
            $course_ids[$course->get_id()] = $course->get_id();
        }

        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_COURSE_ID
            ),
            $course_ids
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_TOOL
            ),
            new StaticConditionVariable('assignment')
        );

        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(Assignment::class)
        );

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ),
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
            null,
            $subselect_condition
        );
        $condition = new AndCondition($conditions);

        $publications = DataManager::retrieves(
            ContentObjectPublication::class,
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(
                            ContentObjectPublication::class,
                            ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
                        )
                    )
                )
            )
        )->as_array();

        $ending_assignments = array();
        foreach ($publications as $publication)
        {
            $assignment = $publication->get_content_object();
            if ($assignment->get_end_time() > time() && $assignment->get_end_time() < $deadline)
            {
                $parameters = array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $publication->get_course_id(),
                    Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(),
                    Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => NewBlock::TOOL_ASSIGNMENT,
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
                    Manager::PARAM_PUBLICATION_ID => $publication->get_id()
                );

                $redirect = new Redirect($parameters);
                $link = $redirect->getUrl();

                $ending_assignments[$assignment->get_end_time() . ' ' . $publication->get_id()] = array(
                    'title' => $assignment->get_title(),
                    'link' => $link,
                    'end_time' => $assignment->get_end_time()
                );
            }
        }

        ksort($ending_assignments);
        $html = $this->displayNewItems($ending_assignments);

        if (count($html) == 0)
        {
            return Translation::get('NoAssignmentsEndComingWeek');
        }

        return implode(PHP_EOL, $html);
    }

    public function displayNewItems($items)
    {
        $html = array();
        foreach ($items as $item)
        {
            $end_date = DatetimeUtilities::format_locale_date(
                Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' . Translation::get(
                    'TimeNoSecFormat',
                    null,
                    Utilities::COMMON_LIBRARIES
                ),
                $item['end_time']
            );

            $html[] = '<a href="' . $item['link'] . '">' . $item['title'] . '</a>: ' . Translation::get('Until') . ' ' .
                $end_date . '<br />';
        }

        return $html;
    }
}
