<?php
namespace Chamilo\Core\Home\Ajax\Component;

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
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_BLOCKS, DataManager :: get_platform_blocks());
        $result->display();
    }
}
