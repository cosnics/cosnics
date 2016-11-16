<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * this is the component for moving a forum in the list
 * 
 * @author Mattias De Pauw
 */
class MoverComponent extends Manager
{

    public function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $move = 0;
            
            if (Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION))
            {
                $move = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION);
            }
            
            $forum_publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(), 
                Request::get(self::PARAM_PUBLICATION_ID));
            
            if ($forum_publication->move($move))
            {
                $failure = false;
                $message = Translation::get(
                    'ObjectMoved', 
                    array(
                        'OBJECT' => Translation::get(
                            'FrequentlyAskedQuestions', 
                            null, 
                            'Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions')), 
                    Utilities::COMMON_LIBRARIES);
            }
            else
            {
                $failure = true;
                $message = Translation::get(
                    'ObjectNotMoved', 
                    array(
                        'OBJECT' => Translation::get(
                            'FrequentlyAskedQuestions', 
                            null, 
                            'Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions')), 
                    Utilities::COMMON_LIBRARIES);
            }
            
            $this->redirect($message, $failure, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
    }

    public function get_move_direction()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION);
    }
}
