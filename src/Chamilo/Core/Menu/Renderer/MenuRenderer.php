<?php
namespace Chamilo\Core\Menu\Renderer;

use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\Menu\Renderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class MenuRenderer
{
    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    private $itemRendererFactory;

    /**
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $chamiloRequest;

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $chamiloRequest
     */
    public function __construct(
        ItemService $itemService, RightsService $rightsService, ItemRendererFactory $itemRendererFactory,
        ChamiloRequest $chamiloRequest
    )
    {
        $this->itemService = $itemService;
        $this->rightsService = $rightsService;
        $this->itemRendererFactory = $itemRendererFactory;
        $this->chamiloRequest = $chamiloRequest;
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
     * @return \Chamilo\Core\Menu\Renderer\ItemRendererFactory
     */
    public function getItemRendererFactory(): ItemRendererFactory
    {
        return $this->itemRendererFactory;
    }

    /**
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     */
    public function setItemRendererFactory(ItemRendererFactory $itemRendererFactory): void
    {
        $this->itemRendererFactory = $itemRendererFactory;
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findRootItems()
    {
        return $this->getItemService()->findItemsByParentIdentifier(0);
    }

    /**
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getChamiloRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    /**
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $chamiloRequest
     */
    public function setChamiloRequest(ChamiloRequest $chamiloRequest): void
    {
        $this->chamiloRequest = $chamiloRequest;
    }

    /**
     * @param string $containerMode
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(string $containerMode = 'container-fluid', User $user = null)
    {
        $html = array();

        $numberOfItems = 0;
        $itemRenditions = array();

        if ($user instanceof User)
        {
            //TODO: This should move to the RightsService
            $userRights = $this->getItemService()->determineRightsForUser($user);

            foreach ($this->findRootItems() as $item)
            {
                if ($userRights[$item->getId()])
                {
                    if (!$item->isHidden())
                    {
                        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer(get_class($this), $item);
                        $itemHtml = $itemRenderer->render($item, $user);

                        if (!empty($itemHtml))
                        {
                            $numberOfItems ++;
                            $itemRenditions[] = $itemHtml;
                        }
                    }
                }
            }
        }

        $html[] = $this->renderHeader($containerMode, $numberOfItems);
        $html[] = implode(PHP_EOL, $itemRenditions);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string $containerMode
     * @param integer $numberOfItems
     *
     * @return string
     */
    abstract public function renderHeader(string $containerMode, int $numberOfItems = 0);

    /**
     *
     * @return string
     */
    abstract public function renderFooter();

}