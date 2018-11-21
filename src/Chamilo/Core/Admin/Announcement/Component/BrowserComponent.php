<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Table\Publication\PublicationTable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements TableSupport, DelegateComponent
{
    const PARAM_FILTER = 'filter';
    const PARAM_PUBLICATION_TYPE = 'publication_type';
    const TYPE_ALL = 1;
    const TYPE_FOR_ME = 2;
    const TYPE_FROM_ME = 3;
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    private $buttonToolbarRenderer;

    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $publicationsTable = $this->get_publications_html();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $publicationsTable;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $type = $this->get_type();

        $tabs = new DynamicVisualTabsRenderer('browser');

        if ($this->getUser()->is_platform_admin())
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    self::TYPE_ALL, Translation::get('AllPublications'),
                    Theme::getInstance()->getCommonImagePath('Treemenu/SharedObjects'),
                    $this->get_url(array(self::PARAM_PUBLICATION_TYPE => self::TYPE_ALL)), $type == self::TYPE_ALL
                )
            );
        }

        $tabs->add_tab(
            new DynamicVisualTab(
                self::TYPE_FROM_ME, Translation::get('PublishedForMe'),
                Theme::getInstance()->getCommonImagePath('Treemenu/SharedObjects'),
                $this->get_url(array(self::PARAM_PUBLICATION_TYPE => self::TYPE_FOR_ME)), $type == self::TYPE_FOR_ME
            )
        );

        $tabs->add_tab(
            new DynamicVisualTab(
                self::TYPE_FROM_ME, Translation::get('MyPublications'),
                Theme::getInstance()->getCommonImagePath('Treemenu/Publication'),
                $this->get_url(array(self::PARAM_PUBLICATION_TYPE => self::TYPE_FROM_ME)), $type == self::TYPE_FROM_ME
            )
        );

        $table = new PublicationTable(
            $this, $this->getPublicationService(), $this->getRightsService(), $this->getUserService(),
            $this->getGroupService()
        );
        $tabs->set_content($table->as_html());

        return $tabs->render();
    }

    public function add_actionbar_item($item)
    {
        $this->buttonToolbarRenderer->add_tool_action($item);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->get_user()->get_platformadmin())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('Publish', array(), Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Publish'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', array(), Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'), $this->get_url(),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $toolActions->addButton(
                new Button(
                    Translation::get('ShowToday', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Admin\Announcement', 'Filter/Day'),
                    $this->get_url(array(self::PARAM_FILTER => self::FILTER_TODAY)), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $toolActions->addButton(
                new Button(
                    Translation::get('ShowThisWeek', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Admin\Announcement', 'Filter/Week'),
                    $this->get_url(array(self::PARAM_FILTER => self::FILTER_THIS_WEEK)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $toolActions->addButton(
                new Button(
                    Translation::get('ShowThisMonth', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Admin\Announcement', 'Filter/Month'),
                    $this->get_url(array(self::PARAM_FILTER => self::FILTER_THIS_MONTH)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $type = $this->get_type();
        switch ($type)
        {
            // Begin with the publisher condition when FROM_ME and add the
            // remaining conditions. Skip the publisher
            // condition when ALL.
            case self::TYPE_FROM_ME :
                $publisher_id = $this->getUser()->getId();

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($publisher_id)
                );
            case self::TYPE_ALL :
                break;
            default :
                break;
        }

        if ($this->get_search_condition())
        {
            $conditions[] = $this->get_search_condition();
        }

        $filter = $this->getRequest()->query->get(self::PARAM_FILTER);

        switch ($filter)
        {
            case self::FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_MODIFICATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
            case self::FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_MODIFICATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
            case self::FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_MODIFICATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    public function get_search_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE),
                '*' . $query . '*'
            );

            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
                '*' . $query . '*'
            );

            return new OrCondition($conditions);
        }

        return null;
    }

    public function get_type()
    {
        $type = $this->getRequest()->query->get(self::PARAM_PUBLICATION_TYPE);

        if (!$type)
        {
            if ($this->getUser()->is_platform_admin())
            {
                $type = self::TYPE_ALL;
            }
            else
            {
                $type = self::TYPE_FOR_ME;
            }
        }

        return $type;
    }
}
