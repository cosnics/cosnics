<?php
namespace Chamilo\Core\Repository\UserView\Component;

use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->getRequest()->get(self::PARAM_USER_VIEW_ID);
        $this->set_parameter(self::PARAM_USER_VIEW_ID, $ids);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $user_view_id)
            {
                $uv = new UserView();
                $uv->set_id($user_view_id);
                
                if (! $uv->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectNotDeleted', 
                        array('OBJECT' => Translation::get('UserView')), 
                        StringUtilities::LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsNotDeleted', 
                        array('OBJECT' => Translation::get('UserViews')), 
                        StringUtilities::LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectDeleted', 
                        array('OBJECT' => Translation::get('UserView')), 
                        StringUtilities::LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsDeleted', 
                        array('OBJECT' => Translation::get('UserViews')), 
                        StringUtilities::LIBRARIES);
                }
            }
            
            $this->redirectWithMessage($message, (bool) $failures, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', 
                        array('OBJECT' => Translation::get('UserView')), 
                        StringUtilities::LIBRARIES)));
        }
    }
}
