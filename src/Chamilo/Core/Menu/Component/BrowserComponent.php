<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Menu\ItemMenu;
use Chamilo\Core\Menu\Table\ItemTableRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    protected string $parentIdentifier;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $html = [];

        $html[] = $this->renderHeader();

        $html[] = $this->getButtonToolbarRenderer()->render();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-2">';
        $html[] = $this->getMenu()->renderAsTree();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-10">';
        $html[] = $this->renderTable();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = Manager::PARAM_ITEM;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $dropDownButton = new DropdownButton($translator->trans('AddMenuItem', [], Manager::CONTEXT));

            foreach ($this->getItemRendererFactory()->getAvailableItemRenderers() as $itemRenderer)
            {
                $dropDownButton->addSubButton(
                    new SubButton(
                        $itemRenderer->getRendererTypeName(), $itemRenderer->getRendererTypeGlyph(),
                        $this->getUrlGenerator()->fromParameters([
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            self::PARAM_ACTION => self::ACTION_CREATE,
                            self::PARAM_TYPE => $itemRenderer::class
                        ])
                    )
                );
            }

            $commonActions->addButton($dropDownButton);

            if ($this->getRightsService()->areRightsEnabled())
            {
                $toolActions->addButton(
                    new Button(

                        $translator->trans('Rights', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('lock'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_RIGHTS]), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->getService(ItemRendererFactory::class);
    }

    public function getItemTableRenderer(): ItemTableRenderer
    {
        return $this->getService(ItemTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getMenu(): ItemMenu
    {
        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_BROWSE,
                self::PARAM_PARENT => '__ITEM__'
            ]
        );

        return new ItemMenu(
            $this->getItemRendererFactory(), $this->getItemService(), $this->getTranslator(), $urlFormat,
            $this->getParentIdentifier()
        );
    }

    public function getParentIdentifier(): string
    {
        if (!isset($this->parentIdentifier))
        {
            $this->parentIdentifier = $this->getRequest()->query->get(self::PARAM_PARENT, '0');
        }

        return $this->parentIdentifier;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getItemService()->countItemsByParentIdentifier($this->getParentIdentifier());
        $itemTableRenderer = $this->getItemTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $itemTableRenderer->getParameterNames(), $itemTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $items = $this->getItemService()->findItemsByParentIdentifier(
            $this->getParentIdentifier(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $itemTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $itemTableRenderer->render($tableParameterValues, $items);
    }
}
