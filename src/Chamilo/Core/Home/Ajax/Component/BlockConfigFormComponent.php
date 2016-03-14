<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class BlockConfigFormComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_BLOCK = 'block';
    const PROPERTY_FORM = 'form';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_BLOCK);
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

        $block = DataManager :: retrieve_by_id(
            Block :: class_name(),
            intval($this->getPostDataValue(self :: PARAM_BLOCK)));

        $formClassName = $block->getContext() . '\Integration\Chamilo\Core\Home\Form\\' . $block->getBlockType() . 'Form';

        if (class_exists($formClassName))
        {
            $form = new $formClassName($block);

            if ($block->getUserId() == $userId)
            {
                $result = new JsonAjaxResult(200);
                $result->set_property(self :: PROPERTY_FORM, $form->toHtml());
                $result->display();
            }
            else
            {
                JsonAjaxResult :: not_allowed();
            }
        }
        else
        {
            JsonAjaxResult :: not_found();
        }
    }
}
