<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Manager;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager
{
    const FILTER_THIS_MONTH = 'month';

    const FILTER_THIS_WEEK = 'week';

    const FILTER_TODAY = 'today';

    const PARAM_FILTER = 'filter';


    public function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $calendar_event = ContentObject::factory(CalendarEvent::class);

        $calendar_event->set_title($publication[ContentObject::PROPERTY_TITLE]);
        $calendar_event->set_description($publication[ContentObject::PROPERTY_DESCRIPTION]);
        $calendar_event->set_start_date($publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]);
        $calendar_event->set_end_date($publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]);
        $calendar_event->set_frequency(CalendarEvent::FREQUENCY_NONE);

        return $calendar_event;
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * "Overrides" the default value of the generic browser component.
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy
     */
    public function getDefaultOrderBy()
    {
        return new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                ), SORT_DESC
            )
        ]);
    }

    protected function getFilter()
    {
        return $this->getRequest()->query->get(self::PARAM_FILTER);
    }

    public function getFilterActions()
    {
        $showActions = [];
        $filter = $this->getFilter();

        $showActions[] = new SubButtonHeader(Translation::get('ViewPeriodHeader'));

        $showActions[] = new SubButton(
            Translation::get('PeriodAll', null, StringUtilities::LIBRARIES), null,
            $this->get_url(array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null)),
            Button::DISPLAY_LABEL, null, [], null, $filter == ''
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodToday', null, StringUtilities::LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_TODAY
            )
        ), Button::DISPLAY_LABEL, null, [], null, $filter == self::FILTER_TODAY
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodWeek', null, StringUtilities::LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_THIS_WEEK
            )
        ), Button::DISPLAY_LABEL, null, [], null, $filter == self::FILTER_THIS_WEEK
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodMonth', null, StringUtilities::LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_THIS_MONTH
            )
        ), Button::DISPLAY_LABEL, null, [], null, $filter == self::FILTER_THIS_MONTH
        );

        $showActions[] = new SubButtonDivider();

        return $showActions;
    }

    public function get_tool_conditions()
    {
        $conditions = [];
        $filter = $this->getRequest()->query->get(self::PARAM_FILTER);

        switch ($filter)
        {
            case self::FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                    ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
            case self::FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                    ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
            case self::FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_MODIFIED_DATE
                    ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
        }

        return $conditions;
    }
}
