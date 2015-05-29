<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Storage\DataClass\AbstractFeedback;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component to update a feedback instance
 * 
 * @package repository\content_object\portfolio\feedback$UpdaterComponent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * Executes this component
     */
    public function run()
    {
        $feedback_id = Request :: get(self :: PARAM_FEEDBACK_ID);
        $feedback = $this->get_parent()->retrieve_feedback($feedback_id);
        
        if (! $this->get_parent()->is_allowed_to_update_feedback($feedback))
        {
            throw new NotAllowedException();
        }
        
        $form = new FeedbackForm($this, $this->get_url(), $feedback);
        
        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                
                $feedback->set_comment($values[AbstractFeedback :: PROPERTY_COMMENT]);
                $success = $feedback->update();
                
                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';
                
                $message = Translation :: get(
                    $translation, 
                    array('OBJECT' => Translation :: get('Feedback')), 
                    Utilities :: COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }
            
            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            return $form->toHtml();
        }
    }

    /**
     * Returns the additional parameters
     * 
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_FEEDBACK_ID);
    }
}