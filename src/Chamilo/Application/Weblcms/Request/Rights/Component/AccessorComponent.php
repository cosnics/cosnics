<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Component;

use Chamilo\Application\Weblcms\Request\Rights\Form\RightsForm;
use Chamilo\Application\Weblcms\Request\Rights\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;

class AccessorComponent extends Manager
{

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $form = new RightsForm($this, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->set_rights();
            
            $this->redirect(
                Translation::get($success ? 'AccessRightsSaved' : 'AccessRightsNotSaved'), !$success
            );
        }
        else
        {
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $this->get_tabs(self::ACTION_ACCESS, $form->toHtml())->render();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
?>