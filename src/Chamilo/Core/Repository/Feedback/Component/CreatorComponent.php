<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to create the feedback
 */
class CreatorComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_parent()->is_allowed_to_create_feedback())
        {
            throw new NotAllowedException();
        }
        
        $form = new FeedbackForm($this->get_url());
        
        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                
                $feedback = $this->get_parent()->get_feedback();
                $feedback = $this->get_parent()->get_feedback();
                $feedback->set_user_id($this->get_user_id());
                $feedback->set_comment($values[Feedback::PROPERTY_COMMENT]);
                $feedback->set_creation_date(time());
                $feedback->set_modification_date(time());
                
                $success = $feedback->create();
                
                $this->notifyNewFeedback($feedback);
                
                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';
                
                $message = Translation::get(
                    $translation, 
                    array('OBJECT' => Translation::get('Feedback')), 
                    Utilities::COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }
            
            $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}