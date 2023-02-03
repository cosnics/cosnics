<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PersonalInstance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\PlatformInstance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Core\Repository\Instance\Table\InstanceTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $tabs = new TabsCollection();

        $tabs->add(
            new ContentTab(
                'personal_instance', $translator->trans('PersonalInstance', [], self::CONTEXT),
                $this->renderTable(PersonalInstance::class)
            )
        );

        if ($this->getUser()->is_platform_admin())
        {
            $tabs->add(
                new ContentTab(
                    'platform_instance', $translator->trans('PlatformInstance', [], self::CONTEXT),
                    $this->renderTable(PlatformInstance::class)
                )
            );
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->getTabsRenderer()->render('instances', $tabs);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('AddExternalInstance', [], self::CONTEXT), new FontAwesomeGlyph('plus'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE]), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $commonActions->addButton(
                new Button(
                    $translator->trans('ManageRights', [], \Chamilo\Core\Rights\Manager::CONTEXT),
                    new FontAwesomeGlyph('lock'), $this->get_url([self::PARAM_ACTION => self::ACTION_RIGHTS]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \QuickformException
     */
    public function getInstanceCondition(string $type): AndCondition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, CompositeDataClass::PROPERTY_TYPE),
            new StaticConditionVariable($type)
        );

        if ($type == PersonalInstance::class)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(PersonalInstance::class, PersonalInstance::PROPERTY_USER_ID),
                new StaticConditionVariable($this->getUser()->getId())
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

    public function getInstanceTableRenderer(): InstanceTableRenderer
    {
        return $this->getService(InstanceTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(string $type): string
    {
        $totalNumberOfItems =
            DataManager::count($type, new DataClassCountParameters($this->getInstanceCondition($type)));
        $instanceTableRenderer = $this->getInstanceTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $instanceTableRenderer->getParameterNames(), $instanceTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $instances = DataManager::retrieves(
            $type, new DataClassRetrievesParameters(
                $this->getInstanceCondition($type), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $instanceTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $instanceTableRenderer->render($tableParameterValues, $instances);
    }
}
