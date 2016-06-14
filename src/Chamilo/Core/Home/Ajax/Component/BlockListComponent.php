<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @author Hans De Bisschop
 *         @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockListComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PROPERTY_BLOCKS = 'blocks';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $blockTypeRightsService = new BlockTypeRightsService(new RightsRepository(), new HomeRepository());

        $platformBlocks = DataManager :: getPlatformBlocks();

        foreach($platformBlocks as $context => &$contextBlocksInfo)
        {
            $validComponents = array();

            $components = $contextBlocksInfo['components'];
            foreach($components as $component)
            {
                if($blockTypeRightsService->canUserViewBlockType($this->getUser(), $component['id']))
                {
                    $validComponents[] = $component;
                }
            }

            $contextBlocksInfo['components'] = $validComponents;
        }

        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_BLOCKS, $platformBlocks);
        $result->display();
    }
}
