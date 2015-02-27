<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_parent()->is_allowed_to_view_feedback())
        {
            throw new NotAllowedException();
        }
        
        $form = new FeedbackForm($this->get_url());
        
        if ($form->validate())
        {
            if (! $this->get_parent()->is_allowed_to_create_feedback())
            {
                throw new NotAllowedException();
            }
            
            $values = $form->exportValues();
            
            // Create the feedback
            $feedback = $this->get_parent()->get_feedback();
            $feedback->set_user_id($this->get_user_id());
            $feedback->set_comment($values[Feedback :: PROPERTY_COMMENT]);
            $feedback->set_creation_date(time());
            $feedback->set_modification_date(time());
            
            $success = $feedback->create();
            
            $this->redirect(
                Translation :: get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated', 
                    array('OBJECT' => Translation :: get('Feedback')), 
                    Utilities :: COMMON_LIBRARIES), 
                ! $success);
        }
        else
        {
            $feedbacks = $this->get_parent()->retrieve_feedbacks();
            
            $html = array();
            
            while ($feedback = $feedbacks->next_result())
            {
                $html[] = '<div class="feedback-container ' .
                     ($feedbacks->current() % 2 == 0 ? 'feedback-container-odd' : 'feedback-container-even') . '">';
                $html[] = '<div class="feedback">';
                
                $html[] = '<div class="body">';
                $html[] = '<div class="content">';
                
                $html[] = '<span class="user">';
                $html[] = $feedback->get_user()->get_fullname();
                $html[] = '</span> ';
                $html[] = $feedback->get_comment();
                
                $html[] = '<div class="date">';
                $html[] = $this->format_date($feedback->get_creation_date());
                $html[] = '</div>';
                
                $html[] = '</div>';
                $html[] = '</div>';
                
                $html[] = '<div class="photo">';
                $html[] = '<img style="width: 32px;" src="' . $feedback->get_user()->get_full_picture_url() . '" />';
                $html[] = '</div>';
                
                $html[] = '<div class="actions">';
                
                if ($this->get_parent()->is_allowed_to_update_feedback($feedback))
                {
                    $html[] = $this->render_update_action($feedback);
                }
                
                if ($this->get_parent()->is_allowed_to_delete_feedback($feedback))
                {
                    $html[] = $this->render_delete_action($feedback);
                }
                
                $html[] = '</div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                
                // TODO: This visual fix should be replaced with a visual and logic fix, preventing the retrieval of all
                // feedback, limiting it to the first three and retrieving the rest via AJAX if and when requested
                if ($feedbacks->size() > 3 && $feedbacks->current() == 3)
                {
                    $html[] = '<div class="feedback-history">';
                }
            }
            
            if ($feedbacks->size() > 3)
            {
                $html[] = '</div>';
                
                $html[] = '<div class="feedback-container feedback-container-odd feedback-history-toggle">';
                $html[] = '<div class="feedback">';
                
                $html[] = '<div class="body">';
                $html[] = '<div class="content">';
                $html[] = '<span class="feedback-history-toggle-visible">' . Translation :: get('ViewPreviousComments') .
                     '</span>';
                $html[] = '<span class="feedback-history-toggle-invisible">' . Translation :: get(
                    'HidePreviousComments') . '</span>';
                
                $html[] = '</div>';
                $html[] = '</div>';
                
                $html[] = '<div class="photo" style="text-align: center;">' .
                     Theme :: getInstance()->getImage('action/feedback', 'png', null, null, null, false, __NAMESPACE__) .
                     '</div>';
                $html[] = '<div class="actions"></div>';
                
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
                $html[] = '<div class="clear"></div>';
                
                $html[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->namespaceToFullPath(__NAMESPACE__, true) . 'resources/javascript/feedback.js');
            }
            
            if ($this->get_parent()->is_allowed_to_create_feedback())
            {
                $html[] = '<div class="feedback-form">';
                $html[] = $form->toHtml();
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
            }
            
            return implode(PHP_EOL, $html);
        }
    }

    public function render_delete_action($feedback_publication)
    {
        $delete_url = $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_DELETE, 
                Manager :: PARAM_FEEDBACK_ID => $feedback_publication->get_id()));
        
        $title = Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES);
        
        $delete_link = '<a href="' . $delete_url . '" onclick="return confirm(\'' .
             addslashes(Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES)) . '\');"><img src="' .
             Theme :: getInstance()->getCommonImagePath() . 'action_delete.png"  alt="' . $title . '" title="' . $title .
             '"/></a>';
        
        return $delete_link;
    }

    public function render_update_action($feedback_publication)
    {
        $update_url = $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, 
                Manager :: PARAM_FEEDBACK_ID => $feedback_publication->get_id()));
        
        $title = Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES);
        $update_link = '<a href="' . $update_url . '"><img src="' . Theme :: getInstance()->getCommonImagePath() .
             'action_edit.png"  alt="' . $title . '" title="' . $title . '"/></a>';
        
        return $update_link;
    }

    public function format_date($date)
    {
        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        return DatetimeUtilities :: format_locale_date($date_format, $date);
    }
}
