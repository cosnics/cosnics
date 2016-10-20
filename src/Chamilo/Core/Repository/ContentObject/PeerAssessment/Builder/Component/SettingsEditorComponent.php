<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Form\PeerAssessmentSettingsForm;
use Chamilo\Libraries\Platform\Translation;

class SettingsEditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->publication_has_scores())
        {
            $publication_id = $this->get_publication_id();
            $settings = $this->get_settings($publication_id);

            $form = new PeerAssessmentSettingsForm($this);
            $form->setDefaults($settings->get_default_properties());

            if ($form->validate())
            {
                $values = $form->exportValues();
                $settings->validate_parameters($values);
                $settings->set_publication_id($publication_id);

                $success = $settings->save();
                $message = $success ? Translation :: get('Success') : Translation :: get('Error');

                $error = $success ? false : true;

                $this->redirect($message, $error, array(self :: PARAM_ACTION => self :: DEFAULT_ACTION));
            }

            $html = array();

            $html[] = parent :: render_header();
            $html[] = $this->getButtonToolbarRenderer()->render();
            $html[] = $form->toHtml();
            $html[] = parent :: render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->redirect(null, false, array(self :: PARAM_ACTION => self :: ACTION_EDIT_ATTEMPT));
        }
    }
}
