<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Configuration\Package\Action\Remover;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\Renderer\ApplicationItemRenderer;

/**
 * Base class for specific removal extensions of web applications
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class WebApplicationRemover extends Remover
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function extra(): bool
    {
        $context = static::CONTEXT;

        $itemService = $this->getItemService();

        $items = $itemService->findApplicationItems();

        foreach ($items as $item)
        {
            if ($item->getSetting(ApplicationItemRenderer::CONFIGURATION_APPLICATION) == $context)
            {
                if (!$itemService->deleteItem($item))
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function getItemService(): CachedItemService
    {
        return $this->getService(CachedItemService::class);
    }
}
