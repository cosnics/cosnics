<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Form\SettingsForm;
use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigurerComponent extends Manager
{

    public function run()
    {
        $external_repository_id = Request :: get(\Chamilo\Core\Repository\Manager :: PARAM_EXTERNAL_INSTANCE);

        $form = new SettingsForm($this, $external_repository_id, 'config', 'post', $this->get_url());
        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirect(
                Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'),
                ($success ? false : true));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = '<script type="text/javascript">';
            $html[] = '$(document).ready(function() {';
            $html[] = '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get(
                'ConfirmOn',
                null,
                Utilities :: COMMON_LIBRARIES) . '\', uncheckedLabel: \'' . Translation :: get(
                'ConfirmOff',
                null,
                Utilities :: COMMON_LIBRARIES) . '\'});';
            $html[] = '});';
            $html[] = '</script>';
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }
}
