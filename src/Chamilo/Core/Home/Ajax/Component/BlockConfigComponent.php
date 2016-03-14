<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class BlockConfigComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_BLOCK = 'block';
    const PARAM_DATA = 'data';
    const PROPERTY_BLOCK = 'block';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_BLOCK, self :: PARAM_DATA);
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

        $block = intval($this->getPostDataValue(self :: PARAM_BLOCK));
        $data = $this->getPostDataValue(self :: PARAM_DATA);

        $block = DataManager :: retrieve_by_id(Block :: class_name(), $block);

        if ($block->getUserId() == $userId)
        {
            $postedValues = $this->getPostDataValue(self :: PARAM_DATA);

            $rendererFactory = new \Chamilo\Core\Home\Renderer\Factory(Renderer :: TYPE_BASIC, $this);
            $blockRendition = BlockRendition :: factory($rendererFactory->getRenderer(), $block);

            if ($blockRendition instanceof ConfigurableInterface)
            {
                foreach ($blockRendition->getConfigurationVariables() as $configurationVariable)
                {
                    $block->setSetting($configurationVariable, $postedValues[$configurationVariable]);
                }

                if (! $block->update())
                {
                    JsonAjaxResult :: general_error();
                }
                else
                {

                    $result = new JsonAjaxResult(200);
                    $result->set_property(self :: PROPERTY_BLOCK, $blockRendition->toHtml());
                    $result->display();
                }
            }
            else
            {
                JsonAjaxResult :: bad_request();
            }
        }
        else
        {
            JsonAjaxResult :: not_allowed();
        }
    }
}
