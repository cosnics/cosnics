<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplaySupport;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Manager implements
    PageDisplaySupport
{
    const TEMPORARY_STORAGE = 'survey_page_preview';

    function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    function get_answer($complex_question_id)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);

        if ($answers)
        {
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
        else
        {
            return null;
        }
    }
}
?>