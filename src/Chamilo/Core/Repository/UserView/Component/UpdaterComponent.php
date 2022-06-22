<?php
namespace Chamilo\Core\Repository\UserView\Component;

use Chamilo\Core\Repository\UserView\Form\UserViewForm;
use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\UserView\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();
        
        $id = Request::get(self::PARAM_USER_VIEW_ID);
        $this->set_parameter(self::PARAM_USER_VIEW_ID, $id);
        if ($id)
        {
            $user_view = DataManager::retrieve_by_id(UserView::class, $id);
            
            $form = new UserViewForm(
                UserViewForm::TYPE_EDIT, 
                $user_view, 
                $this->get_url(array(self::PARAM_USER_VIEW_ID => $id)), 
                $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_user_view();
                $user_view = $form->get_user_view();
                
                $message = Translation::get(
                    $success ? 'ObjectUpdated' : 'ObjectNotUpdated', 
                    array('OBJECT' => Translation::get('UserView')), 
                    StringUtilities::LIBRARIES);
                
                if (! $success)
                {
                    $message .= '<br />' . implode('<br /', $user_view->getErrors());
                }
                
                $this->redirectWithMessage(
                    $message, !$success,
                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
            }
            else
            {
                $html = [];
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
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
