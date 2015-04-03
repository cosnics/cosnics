<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class ParticipantDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        if (! Rights :: is_allowed_in_surveys_subtree(
            Rights :: RIGHT_INVITE, 
            $publication_id, 
            Rights :: TYPE_PUBLICATION))
        {
            throw new NotAllowedException();
        }
        
        $ids = Request :: get(self :: PARAM_PARTICIPANT_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $condition = new InCondition(
                new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_ID), 
                $ids);
            $succes = DataManager :: deletes(Participant :: class_name(), $condition);
            
            if ($succes)
            {
                $answer_condition = new InCondition(
                    new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_SURVEY_PARTICIPANT_ID), 
                    $ids);
                $succes = DataManager :: deletes(Participant :: class_name(), $answer_condition);
            }
            
            if (! $succes)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedParticipantDeleted';
                }
                else
                {
                    $message = 'SelectedParticipantDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedParticipantsDeleted';
                }
                else
                {
                    $message = 'SelectedParticipantsDeleted';
                }
            }
            
            $this->redirect(
                Translation :: get($message), 
                ($failures ? true : false), 
                array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS, 
                    self :: PARAM_PUBLICATION_ID => $publication_id));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoParticipantsSelected')));
        }
    }
}
?>