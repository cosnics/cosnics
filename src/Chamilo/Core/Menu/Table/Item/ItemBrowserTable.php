<?php
namespace Chamilo\Core\Menu\Table\Item;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Core\Menu\Table\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemBrowserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_ITEM;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @var integer
     */
    private $parentIdentifier;

    /**
     * @param $component
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     * @param integer $parentIdentifier
     *
     * @throws \Exception
     */
    public function __construct(
        $component, Translator $translator, ItemService $itemService, RightsService $rightsService,
        ItemRendererFactory $itemRendererFactory, int $parentIdentifier
    )
    {
        $this->translator = $translator;
        $this->itemService = $itemService;
        $this->rightsService = $rightsService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->parentIdentifier = $parentIdentifier;

        parent::__construct($component);
    }

    /**
     * @return \Chamilo\Core\Menu\Factory\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Factory\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @return integer
     */
    public function getParentIdentifier(): int
    {
        return $this->parentIdentifier;
    }

    /**
     * @param integer $parentIdentifier
     */
    public function setParentIdentifier(int $parentIdentifier): void
    {
        $this->parentIdentifier = $parentIdentifier;
    }

    /**
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Core\Menu\Table\Item\ItemBrowserTableCellRenderer
     * @throws \Exception
     */
    public function get_cell_renderer()
    {
        if (!isset($this->cellRenderer))
        {
            $this->cellRenderer = new ItemBrowserTableCellRenderer(
                $this, $this->getTranslator(), $this->getItemService(), $this->getRightsService(),
                $this->getItemRendererFactory()
            );
        }

        return $this->cellRenderer;
    }

    /**
     * @return \Chamilo\Core\Menu\Table\Item\ItemBrowserTableDataProvider
     */
    public function get_data_provider()
    {
        if (!isset($this->dataProvider))
        {
            $this->dataProvider =
                new ItemBrowserTableDataProvider($this, $this->getItemService(), $this->getParentIdentifier());
        }

        return $this->dataProvider;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function get_implemented_form_actions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)),
                $this->getTranslator()->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }
}
