<?php
namespace Chamilo\Core\Lynx\Remote\Component;

use Chamilo\Core\Lynx\Manager;
use Chamilo\Core\Lynx\Remote\DataClass\Package;
use Chamilo\Core\Lynx\Remote\Table\Package\PackageTable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $current_source;
    const STATUS_INSTALLED = 1;
    const STATUS_AVAILABLE = 2;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $parameters = new DataClassCountParameters(
            new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                    \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_STATUS),
                new StaticConditionVariable(\Chamilo\Core\Lynx\Source\DataClass\Source::STATUS_ACTIVE)));
        $count = \Chamilo\Core\Lynx\Source\DataManager::count(
            \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
            $parameters);

        if ($count == 0)
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = 'NO ACTIVE SOURCE REPOSITORIES';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $current_source_id = Request::get(\Chamilo\Core\Lynx\Source\Manager::PARAM_SOURCE_ID);

        if (! $current_source_id)
        {
            $parameters = new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                        \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_STATUS),
                    new StaticConditionVariable(\Chamilo\Core\Lynx\Source\DataClass\Source::STATUS_ACTIVE)),
                array(
                    new OrderBy(
                        new PropertyConditionVariable(
                            \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                            \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_NAME))));
            $this->current_source = \Chamilo\Core\Lynx\Source\DataManager::retrieve(
                \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                $parameters);
        }
        else
        {
            $this->current_source = \Chamilo\Core\Lynx\Source\DataManager::retrieve_by_id(
                \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                (int) $current_source_id);

            if (! $this->current_source instanceof \Chamilo\Core\Lynx\Source\DataClass\Source ||
                 $this->current_source->get_status() == \Chamilo\Core\Lynx\Source\DataClass\Source::STATUS_INACTIVE)
            {
                $parameters = new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                            \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_STATUS),
                        new StaticConditionVariable(\Chamilo\Core\Lynx\Source\DataClass\Source::STATUS_ACTIVE)),
                    array(
                        new OrderBy(
                            new PropertyConditionVariable(
                                \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                                \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_NAME))));

                $this->current_source = \Chamilo\Core\Lynx\Source\DataManager::retrieve(
                    \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                    $parameters);
            }
        }

        $this->set_parameter(\Chamilo\Core\Lynx\Source\Manager::PARAM_SOURCE_ID, $this->current_source->get_id());

        $table = new PackageTable($this);

        $tabs = new DynamicVisualTabsRenderer('remote_source', $table->as_html());

        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                    \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_STATUS),
                new StaticConditionVariable(\Chamilo\Core\Lynx\Source\DataClass\Source::STATUS_ACTIVE)),
            null,
            null,
            array(
                new OrderBy(
                    new PropertyConditionVariable(
                        \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
                        \Chamilo\Core\Lynx\Source\DataClass\Source::PROPERTY_NAME))));

        $sources = \Chamilo\Core\Lynx\Source\DataManager::retrieves(
            \Chamilo\Core\Lynx\Source\DataClass\Source::class_name(),
            $parameters);

        while ($source = $sources->next_result())
        {
            $url = $this->get_url(array(\Chamilo\Core\Lynx\Source\Manager::PARAM_SOURCE_ID => $source->get_id()));
            $tabs->add_tab(
                new DynamicVisualTab(
                    $source->get_id(),
                    $source->get_name(),
                    null,
                    $url,
                    $source->get_id() == $this->get_source()->get_id()));
        }
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $tabs->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($object_table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Package::class_name(), Package::PROPERTY_SOURCE_ID),
            new StaticConditionVariable($this->get_source()->get_id()));

        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Package::class_name(), Package::PROPERTY_CONTEXT),
                '*' . $query . '*');
        }

        return new AndCondition($conditions);
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar(
                $this->get_url(
                    array(\Chamilo\Core\Lynx\Source\Manager::PARAM_SOURCE_ID => $this->get_source()->get_id())));
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('Synchronize'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Lynx\Remote', 'Action/Synchronize'),
                    $this->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_SYNCHRONIZE)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_source()
    {
        return $this->current_source;
    }
}
