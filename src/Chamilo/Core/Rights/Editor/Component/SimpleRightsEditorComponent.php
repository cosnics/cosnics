<?php
namespace Chamilo\Core\Rights\Editor\Component;

use Chamilo\Core\Rights\Editor\Form\SimpleRightsEditorForm;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Translation\Translation;

/**
 * Simple interface to edit rights
 *
 * @author Sven Vanpoucke
 * @package application.common.rights_editor_manager.component
 * @deprecated Should not be needed anymore
 */
class SimpleRightsEditorComponent extends RightsEditorComponent implements DelegateComponent
{

    public function run()
    {
        $form = new SimpleRightsEditorForm(
            $this->get_url(), $this->get_context(), $this->get_locations(), $this->get_available_rights(),
            $this->get_entities()
        );

        if ($form->validate())
        {
            $succes = $form->handle_form_submit();

            $message = Translation::get($succes ? 'RightsChanged' : 'RightsNotChanged');
            $this->redirect($message, !$succes);
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
