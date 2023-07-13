<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

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

            if ($this->getRequest()->query->has(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION))
            {
                $move =
                    $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION);
            }

            $forum_publication = DataManager::retrieve_by_id(
                ContentObjectPublication::class, $this->getRequest()->query->get(self::PARAM_PUBLICATION_ID)
            );

            if ($forum_publication->move($move))
            {
                $failure = false;
                $message = Translation::get(
                    'ObjectMoved',
                    ['OBJECT' => Translation::get('Forum', null, 'Chamilo\Core\Repository\ContentObject\Forum')],
                    StringUtilities::LIBRARIES
                );
            }
            else
            {
                $failure = true;
                $message = Translation::get(
                    'ObjectNotMoved',
                    ['OBJECT' => Translation::get('Forum', null, 'Chamilo\Core\Repository\ContentObject\Forum')],
                    StringUtilities::LIBRARIES
                );
            }

            $this->redirectWithMessage($message, $failure, [self::PARAM_ACTION => self::ACTION_BROWSE]);
        }
    }

    public function get_move_direction()
    {
        return $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION);
    }
}
