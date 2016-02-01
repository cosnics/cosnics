<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Component;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements DelegateComponent
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'Today';
    const FILTER_THIS_WEEK = 'Week';
    const FILTER_THIS_MONTH = 'Month';
    const ACTION_DOWNLOAD_SELECTED_PUBLICATIONS = 'download_selected_publications';

    public function get_tool_actions()
    {
        $toolActions = array();

        $toolActions[] = new Button(
            Translation :: get('Download'),
            Theme :: getInstance()->getCommonImagePath('Action/Save'),
            $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ZIP_AND_DOWNLOAD)),
            Button :: DISPLAY_ICON_AND_LABEL);

        $showActions = array();

        $filter = $this->getFilter();
        $variable = $filter ? $filter : 'All';

        $showActions[] = new SubButton(
            Translation :: get('PeriodAll', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null)),
            Button :: DISPLAY_LABEL,
            false,
            $filter == '' ? 'selected' : '');

        $showActions[] = new SubButton(
            Translation :: get('PeriodToday', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null,
                    self :: PARAM_FILTER => self :: FILTER_TODAY)),
            Button :: DISPLAY_LABEL,
            false,
            $filter == self :: FILTER_TODAY ? 'selected' : '');

        $showActions[] = new SubButton(
            Translation :: get('PeriodWeek', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null,
                    self :: PARAM_FILTER => self :: FILTER_THIS_WEEK)),
            Button :: DISPLAY_LABEL,
            false,
            $filter == self :: FILTER_THIS_WEEK ? 'selected' : '');

        $showActions[] = new SubButton(
            Translation :: get('PeriodMonth', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => null,
                    self :: PARAM_FILTER => self :: FILTER_THIS_MONTH)),
            Button :: DISPLAY_LABEL,
            false,
            $filter == self :: FILTER_THIS_MONTH ? 'selected' : '');

        $showAction = new DropdownButton(
            Translation :: get('Period' . $variable, null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Period'));
        $showAction->setSubButtons($showActions);

        $toolActions[] = $showAction;

        return $toolActions;
    }

    protected function getFilter()
    {
        return $this->getRequest()->query->get(self :: PARAM_FILTER);
    }

    public function get_tool_conditions()
    {
        $conditions = array();
        $filter = $this->getFilter();

        switch ($filter)
        {
            case self :: FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                break;
            case self :: FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                break;
            case self :: FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                break;
        }

        if ($filter)
        {
            $conditions[] = new InequalityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_MODIFIED_DATE),
                InequalityCondition :: GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable($time));
        }

        $browser_type = $this->get_browser_type();
        if ($browser_type == ContentObjectPublicationListRenderer :: TYPE_GALLERY ||
             $browser_type == ContentObjectPublicationListRenderer :: TYPE_SLIDESHOW)
        {
            $classes = array();

            if (ContentObject :: is_available('Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File'))
            {
                $classes[] = File :: class_name();
            }

            $image_subselect_conditions = array();

            foreach ($classes as $class)
            {
                $image_types = $class :: get_image_types();
                $image_conditions = array();
                foreach ($image_types as $image_type)
                {
                    $image_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable($class, $class :: PROPERTY_FILENAME),
                        '*.' . $image_type);
                }

                $image_condition = new OrCondition($image_conditions);

                $image_subselect_conditions[] = new SubselectCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
                    new PropertyConditionVariable($class, $class :: PROPERTY_ID),
                    $class :: get_table_name(),
                    $image_condition);
            }

            $conditions[] = new OrCondition($image_subselect_conditions);
        }

        return $conditions;
    }

    public function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $calendar_event = ContentObject :: factory(CalendarEvent :: class_name());

        $calendar_event->set_title($publication[ContentObject :: PROPERTY_TITLE]);
        $calendar_event->set_description($publication[ContentObject :: PROPERTY_DESCRIPTION]);
        $calendar_event->set_start_date($publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]);
        $calendar_event->set_end_date($publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]);
        $calendar_event->set_frequency(CalendarEvent :: FREQUENCY_NONE);

        return $calendar_event;
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }

    public function get_additional_form_actions()
    {
        return array(
            new TableFormAction(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_DOWNLOAD_SELECTED_PUBLICATIONS)),
                Translation :: get('DownloadSelected'),
                true));
    }
}
