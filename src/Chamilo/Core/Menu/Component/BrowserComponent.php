<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\UserInterface\Table\ItemTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tree\Menu\JsTreeRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{

    protected string $parentIdentifier;

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();

        $html[] = $this->getButtonToolbarRenderer()->render();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-2">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-10">';
        $html[] = $this->renderTable();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
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

            foreach ($this->getItemRendererFactory()->getItemRenderers() as $itemRenderer)
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

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getItemTableRenderer(): ItemTableRenderer
    {
        return $this->getService(ItemTableRenderer::class);
    }

    public function getJsTreeRenderer(): JsTreeRenderer
    {
        return $this->getService(JsTreeRenderer::class);
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

    public function renderMenu(): string
    {
        $dataUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => 'Chamilo\\\Core\\\Menu',
                Application::PARAM_ACTION => Manager::ACTION_ITEM_TREE_DATA,
            ]
        );

        $selectedPathIdentifiers = ['0'];

        if ($this->getParentIdentifier() != '0')
        {
            $selectedPathIdentifiers = ['0', $this->getParentIdentifier()];
        }

        return $this->getJsTreeRenderer()->render(
            'itemMenu', Manager::PARAM_PARENT, $dataUrl, $selectedPathIdentifiers
        );
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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
