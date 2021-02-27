<?php

namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Renderer\Type\Basic\BlockRendererFactory;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class BlockGetContentComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_BLOCK = 'block';
    const PROPERTY_CONTENT = 'content';

    public function run()
    {
        $userId = DataManager::determine_user_id();

        if ($userId === false)
        {
            JsonAjaxResult::not_allowed();

            return;
        }

        $blockId = intval($this->getRequest()->getFromUrl(self::PARAM_BLOCK));
        $block = DataManager::retrieve_by_id(Block::class_name(), $blockId);

        if (!$block instanceof Block)
        {
            JsonAjaxResult::bad_request();

            return;
        }

        /** @var Element $block */
        if ($block->getUserId() != $userId)
        {
            JsonAjaxResult::not_allowed();

            return;
        }

        $homeService = new HomeService(new HomeRepository(), new ElementRightsService(new RightsRepository()));

        $blockRendererFactory = new BlockRendererFactory(
            $this,
            $homeService,
            $block,
            BlockRendererFactory::SOURCE_AJAX
        );

        $blockRenderer = $blockRendererFactory->getRenderer();

        $result = new JsonAjaxResult(200);
        $result->set_property(self::PROPERTY_CONTENT, $blockRenderer->displayContent());
        $result->display();
    }
}
