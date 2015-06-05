<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Renderer\Renderer;
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

        if ($block->get_user() == $user_id)
        {
            $homeblock_config = $block->parse_settings();
            $values = $this->getPostDataValue(self :: PARAM_DATA);

            $problems = 0;

            foreach ($homeblock_config['settings'] as $category_name => $settings)
            {
                foreach ($settings as $name => $setting)
                {
                    if ($setting['locked'] != 'true')
                    {
                        $conditions = array();
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                BlockConfiguration :: class_name(),
                                BlockConfiguration :: PROPERTY_BLOCK_ID),
                            new StaticConditionVariable($block->get_id()));
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                BlockConfiguration :: class_name(),
                                BlockConfiguration :: PROPERTY_VARIABLE),
                            new StaticConditionVariable($name));
                        $condition = new AndCondition($conditions);

                        $block_config = DataManager :: retrieve(
                            BlockConfiguration :: class_name(),
                            new DataClassRetrieveParameters($condition));
                        $block_config->set_value($values[$name]);
                        if (! $block_config->update())
                        {
                            $problems ++;
                        }
                    }
                }
            }

            if ($problems > 0)
            {
                JsonAjaxResult :: general_error();
            }
            else
            {
                DataClassCache :: truncates(array(Block :: class_name(), BlockConfiguration :: class_name()));
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    User :: class_name(),
                    (int) Session :: get_user_id());
                $renderer = Renderer :: factory(Renderer :: TYPE_BASIC, $user);
                $html = BlockRendition :: factory($renderer, $block)->as_html();

                $result = new JsonAjaxResult(200);
                $result->set_property(self :: PROPERTY_BLOCK, $html);
                $result->display();
            }
        }
        else
        {
            JsonAjaxResult :: not_allowed();
        }
    }
}
