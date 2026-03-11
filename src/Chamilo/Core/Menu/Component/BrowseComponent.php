<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\UserInterface\Table\ItemTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowseComponent extends Manager
{
    protected string $parentIdentifier;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);

        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar());

        $html[] = '<div class="row">';
        $html[] = '<div class="col-12 col-lg-2">';
        $html[] = $this->renderMenu();
        $html[] = '</div>';

        $html[] = '<div class="col-12 col-lg-10">';
        $html[] = $this->renderTable();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getButtonToolBar(): ButtonToolBar
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar();
        $commonActions = new ButtonGroup();
        $toolActions = new ButtonGroup();

        $dropDownButton = new DropDownButtonCollection($translator->trans('AddMenuItem', [], Manager::CONTEXT));

        foreach ($this->getItemRendererFactory()->getItemRenderers() as $itemRenderer) {
            $dropDownButton->addButton(
                new SubButton(
                    $itemRenderer->getRendererTypeName(), $itemRenderer->getRendererTypeGlyph(),
                    $this->getUrlGenerator()->fromParameters([
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::CREATE->value,
                        self::PARAM_TYPE => $itemRenderer::class
                    ])
                )
            );
        }

        $commonActions->addButton($dropDownButton);

        $buttonToolBar->addButton($commonActions);
        $buttonToolBar->addButton($toolActions);

        return $buttonToolBar;
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
        if (!isset($this->parentIdentifier)) {
            $this->parentIdentifier = $this->getRequest()->query->get(self::PARAM_PARENT, DataClass::EMPTY_UUID);
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
                ApplicationInterface::PARAM_CONTEXT => 'Chamilo\\\Core\\\Menu',
                ApplicationInterface::PARAM_ACTION => ActionEnum::ITEM_TREE_DATA->value,
            ]
        );

        $selectedPathIdentifiers = [DataClass::EMPTY_UUID];

        if ($this->getParentIdentifier() != DataClass::EMPTY_UUID) {
            $selectedPathIdentifiers = [DataClass::EMPTY_UUID, $this->getParentIdentifier()];
        }

        return $this->getJsTreeRenderer()->render(
            'itemMenu', Manager::PARAM_PARENT, $dataUrl, $selectedPathIdentifiers
        );
    }

    /**
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
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
