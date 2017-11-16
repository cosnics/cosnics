<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Form\PeerAssessmentAttemptForm;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class AttemptCreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(self::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        $publication_id = $this->get_publication_id();
        
        $attempt_id = Request::get(self::PARAM_ATTEMPT);
        $attempt = $this->get_attempt($attempt_id);
        
        $this->set_parameter(self::PARAM_ATTEMPT, $attempt_id);
        
        $form = new PeerAssessmentAttemptForm($this);
        $form->setDefaults($attempt->get_default_properties());
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            $attempt->validate_parameters($values);
            $attempt->set_publication_id($publication_id);
            $attempt->set_hidden(false);
            
            $success = $attempt->save();
            // redirect back to the attempt overview page
            
            $message = $success ? Translation::get('Success') : Translation::get('Error');
            $error = $success ? false : true;
            $this->redirect($message, $error, array(self::PARAM_ACTION => self::ACTION_BROWSE_ATTEMPTS));
        }
        
        $html = array();
        
        $html[] = parent::render_header();
        $html[] = $this->render_action_bar();
        $html[] = $form->toHtml();
        $html[] = parent::render_footer();
        
        return implode(PHP_EOL, $html);
    }

    protected function render_action_bar()
    {
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        parent::add_additional_breadcrumbs($breadcrumbtrail);
    }
}
