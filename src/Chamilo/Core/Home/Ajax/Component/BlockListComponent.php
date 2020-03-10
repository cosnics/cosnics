<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @author Hans De Bisschop
 *         @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockListComponent extends Manager
{
    const PROPERTY_BLOCKS = 'blocks';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $rightsRepository = new RightsRepository();
        $homeRepository = new HomeRepository();
        $homeService = new HomeService($homeRepository, new ElementRightsService($rightsRepository));
        $blockTypeRightsService = new BlockTypeRightsService($rightsRepository, new HomeRepository());
        
        $platformBlocks = DataManager::getPlatformBlocks();
        
        foreach ($platformBlocks as $context => &$contextBlocksInfo)
        {
            $validComponents = array();
            
            $components = $contextBlocksInfo['components'];
            foreach ($components as $component)
            {
                $class = $component['id'];
                $blockRenderer = new $class($this, $homeService, new Block());
                
                if ($blockTypeRightsService->canUserViewBlockRenderer($this->getUser(), $blockRenderer))
                {
                    $validComponents[] = $component;
                }
            }
            
            $contextBlocksInfo['components'] = $validComponents;
            
            if (empty($contextBlocksInfo['components']))
            {
                unset($platformBlocks[$context]);
            }
        }
        
        $result = new JsonAjaxResult(200);
        $result->set_property(self::PROPERTY_BLOCKS, $platformBlocks);
        $result->display();
    }
}
