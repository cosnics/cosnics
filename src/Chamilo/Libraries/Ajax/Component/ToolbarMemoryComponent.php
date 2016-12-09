<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ToolbarMemoryComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        $state = $_POST['state'];
        $_SESSION['toolbar_state'] = $state;
        
        JsonAjaxResult::success();
    }
}