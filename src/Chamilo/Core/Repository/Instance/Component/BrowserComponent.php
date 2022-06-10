<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PersonalInstance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PlatformInstance;
use Chamilo\Core\Repository\Instance\Table\Instance\InstanceTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $type;

    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $parameters = $this->get_parameters();
        $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        $tabs = new TabsCollection();

        $tabs->add(
            new ContentTab(
                'personal_instance', Translation::get('PersonalInstance'), $this->get_table(PersonalInstance::class)
            )
        );

        if ($this->get_user()->is_platform_admin())
        {
            $tabs->add(
                new ContentTab(
                    'platform_instance', Translation::get('PlatformInstance'), $this->get_table(PlatformInstance::class)
                )
            );
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->getTabsRenderer()->render('instances', $tabs);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('AddExternalInstance'), new FontAwesomeGlyph('plus'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $commonActions->addButton(
                new Button(
                    Translation::get('ManageRights', null, \Chamilo\Core\Rights\Manager::package()),
                    new FontAwesomeGlyph('lock'), $this->get_url(array(self::PARAM_ACTION => self::ACTION_RIGHTS)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function get_table($type)
    {
        $this->type = $type;
        $table = new InstanceTable($this);

        return $table->as_html();
    }

    /**
     *
     * @see \common\libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_TYPE),
            new StaticConditionVariable($this->type)
        );

        if ($this->type == PersonalInstance::class)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(PersonalInstance::class, PersonalInstance::PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id())
            );
        }

        if (isset($query) && $query != '')
        {
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(Instance::class, Instance::PROPERTY_TITLE), $query
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * @deprecated Use BrowserComponent::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    public function getType()
    {
        return $this->type;
    }
}
