<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Architecture\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Renderer\Type\Basic\BlockRendererFactory;
use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class BlockConfigComponent extends Manager
{
    const PARAM_BLOCK = 'block';
    const PARAM_DATA = 'data';
    const PROPERTY_BLOCK = 'block';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_BLOCK, self::PARAM_DATA);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $userId = DataManager::determine_user_id();
        
        if ($userId === false)
        {
            JsonAjaxResult::not_allowed();
        }
        
        $block = intval($this->getPostDataValue(self::PARAM_BLOCK));
        $data = $this->getPostDataValue(self::PARAM_DATA);
        
        $block = DataManager::retrieve_by_id(Block::class_name(), $block);
        
        /** @var Element $block */
        if ($block->getUserId() == $userId)
        {
            $postedValues = $this->getPostDataValue(self::PARAM_DATA);
            
            // $rendererFactory = new \Chamilo\Core\Home\Renderer\Factory(Renderer :: TYPE_BASIC, $this);
            // $renderer = $rendererFactory->getRenderer();
            
            $homeService = new HomeService(new HomeRepository(), new ElementRightsService(new RightsRepository()));
            
            $blockRendererFactory = new BlockRendererFactory(
                $this, 
                $homeService, 
                $block, 
                BlockRendererFactory::SOURCE_AJAX);
            
            $blockRenderer = $blockRendererFactory->getRenderer();
            
            $contentObjectPublicationService = new ContentObjectPublicationService(
                new ContentObjectPublicationRepository(new PublicationRepository()));
            
            if ($blockRenderer instanceof ConfigurableInterface ||
                 $blockRenderer instanceof ContentObjectPublicationBlockInterface)
            {
                if ($blockRenderer instanceof ConfigurableInterface)
                {
                    foreach ($blockRenderer->getConfigurationVariables() as $configurationVariable)
                    {
                        $block->setSetting($configurationVariable, $postedValues[$configurationVariable]);
                    }
                }
                
                if ($blockRenderer instanceof ContentObjectPublicationBlockInterface)
                {
                    foreach ($blockRenderer->getContentObjectConfigurationVariables() as $configurationVariable)
                    {
                        $contentObjectPublicationService->setOnlyContentObjectForElement(
                            $block, 
                            $postedValues[$configurationVariable]);
                    }
                }
                
                if (isset($postedValues[Block::PROPERTY_TITLE]))
                {
                    $block->setTitle($postedValues[Block::PROPERTY_TITLE]);
                }
                
                if (! $block->update())
                {
                    JsonAjaxResult::general_error();
                }
                else
                {
                    
                    $result = new JsonAjaxResult(200);
                    $result->set_property(self::PROPERTY_BLOCK, $blockRenderer->toHtml());
                    $result->display();
                }
            }
            else
            {
                JsonAjaxResult::bad_request();
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
