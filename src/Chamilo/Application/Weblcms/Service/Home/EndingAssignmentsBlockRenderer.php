<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 *
 * @package Chamilo\Application\Weblcms\Service\Home
 */
class EndingAssignmentsBlockRenderer extends BlockRenderer
{

    protected DatetimeUtilities $datetimeUtilities;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, DatetimeUtilities $datetimeUtilities
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter);

        $this->datetimeUtilities = $datetimeUtilities;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function displayContent(Block $block, ?User $user = null): string
    {
        // deadline min 1 week (60 * 60 * 24 * 7)
        $deadline = time() + 604800;

        $courses = CourseDataManager::retrieve_all_courses_from_user($user);
        $course_ids = [];

        foreach ($courses as $course)
        {
            $course_ids[$course->getId()] = $course->getId();
        }

        $conditions = [];
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), $course_ids
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable('assignment')
        );

        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(Assignment::class)
        );

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, Publication::PROPERTY_CONTENT_OBJECT_ID
            ), new PropertyConditionVariable(ContentObject::class, DataClass::PROPERTY_ID), $subselect_condition
        );
        $condition = new AndCondition($conditions);

        $publications = DataManager::retrieves(
            ContentObjectPublication::class, new DataClassRetrievesParameters(
                $condition, null, null, new OrderBy([
                    new OrderProperty(
                        new PropertyConditionVariable(
                            ContentObjectPublication::class, ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
                        )
                    )
                ])
            )
        );

        $ending_assignments = [];
        foreach ($publications as $publication)
        {
            /**
             * @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
             */
            $assignment = $publication->get_content_object();

            if ($assignment->get_end_time() > time() && $assignment->get_end_time() < $deadline)
            {
                $parameters = [
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $publication->get_course_id(),
                    Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => NewBlockRenderer::TOOL_ASSIGNMENT,
                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_DISPLAY,
                    Manager::PARAM_PUBLICATION_ID => $publication->getId()
                ];

                $link = $this->getUrlGenerator()->fromParameters($parameters);

                $ending_assignments[$assignment->get_end_time() . ' ' . $publication->getId()] = [
                    'title' => $assignment->get_title(),
                    'link' => $link,
                    'end_time' => $assignment->get_end_time()
                ];
            }
        }

        ksort($ending_assignments);
        $html = $this->displayNewItems($ending_assignments);

        if (count($html) == 0)
        {
            return $this->getTranslator()->trans('NoAssignmentsEndComingWeek', [],
                \Chamilo\Application\Weblcms\Manager::CONTEXT);
        }

        return implode(PHP_EOL, $html);
    }

    public function displayNewItems($items): array
    {
        $translator = $this->getTranslator();

        $html = [];

        foreach ($items as $item)
        {
            $end_date = $this->getDatetimeUtilities()->formatLocaleDate(
                $translator->trans('DateFormatShort', [], StringUtilities::LIBRARIES) . ', ' . $translator->trans(
                    'TimeNoSecFormat', [], StringUtilities::LIBRARIES
                ), $item['end_time']
            );

            $html[] = '<a href="' . $item['link'] . '">' . $item['title'] . '</a>: ' .
                $translator->trans('Until', [], StringUtilities::LIBRARIES) . ' ' . $end_date . '<br />';
        }

        return $html;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }
}
