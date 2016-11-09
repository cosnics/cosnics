<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class ParticipantDeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        if (! Rights :: getInstance()->is_right_granted(Rights :: INVITE_RIGHT, $publication_id))
        {
            throw new NotAllowedException(false);
        }
        
        $ids = Request :: get(self :: PARAM_PARTICIPANT_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            $conditions = array();
            $conditions[] = new EqualityCondition( new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_SURVEY_PUBLICATION_ID), $publication_id);
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_USER_ID), 
                $ids);
            $succes = DataManager :: deletes(Participant :: class_name(), new AndCondition($conditions));
            
            if ($succes)
            {
                $conditions = array();
                $conditions[] = new EqualityCondition( new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_PUBLICATION_ID), $publication_id);
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_USER_ID), 
                    $ids);
                $succes = DataManager :: deletes(Answer :: class_name(), new AndCondition($conditions));
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