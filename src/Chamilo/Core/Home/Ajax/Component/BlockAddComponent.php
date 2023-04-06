<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Renderer\Type\Basic\BlockRendererFactory;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_BLOCK = 'block';
    const PARAM_COLUMN = 'column';
    const PARAM_ORDER = 'order';
    const PROPERTY_BLOCK = 'block';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_BLOCK, self::PARAM_COLUMN);
    }

    public function unserialize_jquery($jquery)
    {
        $block_data = explode('&', $jquery);
        $blocks = array();
        
        foreach ($block_data as $block)
        {
            $block_split = explode('=', $block);
            $blocks[] = $block_split[1];
        }
        
        return $blocks;
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
        
        $columnId = $this->getPostDataValue(self::PARAM_COLUMN);
        $block = $this->getPostDataValue(self::PARAM_BLOCK);
        $context = ClassnameUtilities::getInstance()->getNamespaceParent($block, 6);
        $translationContext = ClassnameUtilities::getInstance()->getNamespaceParent($block, 2);
        $blockType = ClassnameUtilities::getInstance()->getClassnameFromNamespace($block);
        
        $block = new Block();
        $block->setParentId($columnId);
        $block->setTitle(Translation::get($blockType, null, $translationContext));
        $block->setContext($context);
        $block->setBlockType($blockType);
        $block->setVisibility(1);
        $block->setUserId($userId);
        
        if ($block->create())
        {
            $block->setSort(1);
            
            if ($block->update())
            {
                // $rendererFactory = new Factory(Renderer::TYPE_BASIC, $this);
                // $renderer = $rendererFactory->getRenderer();
                
                $homeService = new HomeService(new HomeRepository(), new ElementRightsService(new RightsRepository()));
                $blockRendererFactory = new BlockRendererFactory(
                    $this, 
                    $homeService, 
                    $block, 
                    BlockRendererFactory::SOURCE_AJAX);
                $blockRenderer = $blockRendererFactory->getRenderer();
                
                $result = new JsonAjaxResult(200);
                $result->set_property(self::PROPERTY_BLOCK, $blockRenderer->toHtml());
                $result->display();
            }
            else
            {
                JsonAjaxResult::error(500);
            }
        }
        else
        {
            JsonAjaxResult::error(500);
        }
    }
}
