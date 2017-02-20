<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the controlled vocabulary
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $feedback_id = Request::get(self::PARAM_FEEDBACK_ID);
        $this->set_parameter(self::PARAM_FEEDBACK_ID, $feedback_id);
        
        $feedback = $this->get_parent()->retrieve_feedback($feedback_id);

        if(!$feedback instanceof Feedback)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Feedback'), $feedback_id);
        }

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
                
                $feedback->set_comment($values[Feedback::PROPERTY_COMMENT]);
                $success = $feedback->update();
                
                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';
                
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
            return $form->toHtml();
        }
    }
}