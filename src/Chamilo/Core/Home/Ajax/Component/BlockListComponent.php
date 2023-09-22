<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BlockListComponent extends Manager
{
    public const PROPERTY_BLOCKS = 'blocks';

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function run()
    {
        $result = new JsonAjaxResult(200);
        $result->set_property(
            self::PROPERTY_BLOCKS, $this->getHomeService()->getAvailableBlockRenderersForUser($this->getUser())
        );
        $result->display();
    }
}
