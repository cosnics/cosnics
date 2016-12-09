<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Quota\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;

class AccessorComponent extends Manager
{

    public function run()
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
                Translation::get($success ? 'AccessRightsSaved' : 'AccessRightsNotSaved'), 
                ($success ? false : true));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $this->get_tabs(self::ACTION_ACCESS, $form->toHtml())->render();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
