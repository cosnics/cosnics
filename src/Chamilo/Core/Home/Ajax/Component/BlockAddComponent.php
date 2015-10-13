<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Home\Renderer\Factory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

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
        return array(self :: PARAM_BLOCK, self :: PARAM_COLUMN);
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
        $userId = DataManager :: determine_user_id();

        if ($userId === false)
        {
            JsonAjaxResult :: not_allowed();
        }

        $column_data = explode('_', $this->getPostDataValue(self :: PARAM_COLUMN));
        $blocks = $this->unserialize_jquery($this->getPostDataValue(self :: PARAM_ORDER));

        $block = $this->getPostDataValue(self :: PARAM_BLOCK);
        $context = ClassnameUtilities :: getInstance()->getNamespaceParent($block, 6);
        $blockType = ClassnameUtilities :: getInstance()->getClassnameFromNamespace($block);

        $block = new Block();
        $block->setParentId($column_data[2]);
        $block->setTitle(Translation :: get($blockType, null, $context));

        $block->setContext($context);
        $block->setBlockType($blockType);
        $block->setVisibility(true);
        $block->setUserId($userId);
        $block->create();

        $rendererFactory = new Factory(Renderer :: TYPE_BASIC, $this);
        $renderer = $rendererFactory->getRenderer();
        $html = BlockRendition :: factory($renderer, $block)->toHtml();

        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_BLOCK, $html);
        $result->display();
    }
}
