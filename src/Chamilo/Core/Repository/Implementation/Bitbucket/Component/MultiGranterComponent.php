<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\MultiPrivilegeForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Translation;

class MultiGranterComponent extends Manager
{

    public function run()
    {
        $privilege_form = new MultiPrivilegeForm($this);

        if ($privilege_form->validate())
        {
            $success = $privilege_form->grant_privilege();
            $message = $success ? Translation :: get('GrantPrivilegeCreated') : Translation :: get(
                'GrantPrivilegeNotCreated');

            $this->redirect($message, ! $success);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $privilege_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
