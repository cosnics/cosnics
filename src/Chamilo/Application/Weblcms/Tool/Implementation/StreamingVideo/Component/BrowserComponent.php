<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager
{
    const FILTER_THIS_MONTH = 'month';

    const FILTER_THIS_WEEK = 'week';

    const FILTER_TODAY = 'today';

    const PARAM_FILTER = 'filter';

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
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
            Translation::get('PeriodAll', null, Utilities::COMMON_LIBRARIES), null,
            $this->get_url(array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null)),
            Button::DISPLAY_LABEL, false, [], null, $filter == ''
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodToday', null, Utilities::COMMON_LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_TODAY
            )
        ), Button::DISPLAY_LABEL, false, [], null, $filter == self::FILTER_TODAY
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodWeek', null, Utilities::COMMON_LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_THIS_WEEK
            )
        ), Button::DISPLAY_LABEL, false, [], null, $filter == self::FILTER_THIS_WEEK
        );

        $showActions[] = new SubButton(
            Translation::get('PeriodMonth', null, Utilities::COMMON_LIBRARIES), null, $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                self::PARAM_FILTER => self::FILTER_THIS_MONTH
            )
        ), Button::DISPLAY_LABEL, false, $filter == [], null, $filter == self::FILTER_THIS_MONTH
        );

        $showActions[] = new SubButtonDivider();

        return $showActions;
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::get_additional_parameters($additionalParameters);
    }

    public function get_tool_conditions()
    {
        $conditions = [];
        $filter = Request::get(self::PARAM_FILTER);

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
