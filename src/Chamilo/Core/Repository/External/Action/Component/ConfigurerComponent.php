<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Form\SettingsForm;
use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ConfigurerComponent extends Manager
{

    public function run()
    {
        $external_repository_id = Request::get(\Chamilo\Core\Repository\Manager::PARAM_EXTERNAL_INSTANCE);

        $form = new SettingsForm(
            $this, $external_repository_id, 'config', FormValidator::FORM_METHOD_POST, $this->get_url()
        );
        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirect(
                Translation::get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), !$success
            );
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
