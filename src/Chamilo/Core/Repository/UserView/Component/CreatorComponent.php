<?php
namespace Chamilo\Core\Repository\UserView\Component;

use Chamilo\Core\Repository\UserView\Form\UserViewForm;
use Chamilo\Core\Repository\UserView\Manager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user_view = new UserView();
        $user_view->set_user_id($this->get_user_id());
        $form = new UserViewForm(UserViewForm::TYPE_CREATE, $user_view, $this->get_url());
        if ($form->validate())
        {
            $success = $form->create_user_view();
            $user_view = $form->get_user_view();
            
            $message = $success ? Translation::get(
                'ObjectCreated', 
                array('OBJECT' => Translation::get('UserView')), 
                Utilities::COMMON_LIBRARIES) : Translation::get(
                'ObjectNotCreated', 
                array('OBJECT' => Translation::get('UserView')), 
                Utilities::COMMON_LIBRARIES);
            
            if (! $success)
            {
                $message .= '<br />' . implode('<br /', $user_view->get_errors());
            }
            
            $this->redirect($message, $success ? false : true, array(self::PARAM_ACTION => self::ACTION_BROWSE));
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
}
