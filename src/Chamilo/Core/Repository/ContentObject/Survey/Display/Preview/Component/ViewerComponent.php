<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Manager implements
    SurveyDisplaySupport
{
    const TEMPORARY_STORAGE = 'survey_preview';
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';

    function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\ContentObject\Survey\Display\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    public function get_answer($complex_question_id)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);

        $answer = $answers[$complex_question_id];
        
        if ($answer)
        {
            return $answer;
        }
        else
        {
            return null;
        }
    }

    function save_answer($complex_question_id, $answer)
    {
        if (! Session :: retrieve(self :: TEMPORARY_STORAGE))
        {
            Session :: register(self :: TEMPORARY_STORAGE, array());
        }

        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
      
        
        $answers[$complex_question_id] = $answer;
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }
}
?>