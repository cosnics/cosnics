<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\BlockConfiguration;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;

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
        $user_id = DataManager :: determine_user_id();

        if ($user_id === false)
        {
            JsonAjaxResult :: not_allowed();
        }

        $block = intval($this->getPostDataValue(self :: PARAM_BLOCK));
        $data = $this->getPostDataValue(self :: PARAM_DATA);

        $block = DataManager :: retrieve_by_id(Block :: class_name(), $block);

        if ($block->getUserId() == $user_id)
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
