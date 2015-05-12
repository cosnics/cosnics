<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Home\Renderer\Factory;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_CONTEXT = 'block_context';
    const PARAM_COMPONENT = 'block_component';
    const PARAM_COLUMN = 'column';
    const PARAM_ORDER = 'order';
    const PROPERTY_BLOCK = 'block';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_CONTEXT, self :: PARAM_COMPONENT, self :: PARAM_COLUMN, self :: PARAM_ORDER);
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
        $user_id = DataManager :: determine_user_id();

        if ($user_id === false)
        {
            JsonAjaxResult :: not_allowed();
        }

        $column_data = explode('_', $this->getPostDataValue(self :: PARAM_COLUMN));
        $blocks = $this->unserialize_jquery($this->getPostDataValue(self :: PARAM_ORDER));

        $block = new Block();
        $block->set_column($column_data[2]);
        $block->set_title(
            Translation :: get(
                (string) StringUtilities :: getInstance()->createString(
                    $this->getPostDataValue(self :: PARAM_COMPONENT))->upperCamelize(),
                null,
                $this->getPostDataValue(self :: PARAM_CONTEXT)));
        $registration = DataManager :: retrieve_home_block_registration_by_context_and_block(
            $this->getPostDataValue(self :: PARAM_CONTEXT),
            $this->getPostDataValue(self :: PARAM_COMPONENT));
        $block->set_registration_id($registration->get_id());
        $block->set_visibility('1');
        $block->set_user($user_id);
        $block->create();

        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            User :: class_name(),
            (int) Session :: get_user_id());

        $rendererFactory = new Factory(Renderer :: TYPE_BASIC, $this);
        $renderer = $rendererFactory->getRenderer();
        $html = BlockRendition :: factory($renderer, $block)->as_html();

        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_BLOCK, $html);
        $result->display();
    }
}
