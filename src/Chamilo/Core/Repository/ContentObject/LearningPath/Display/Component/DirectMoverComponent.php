<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;
use RuntimeException;

/**
 * A mover component which moves the selected object directly to a different parent
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DirectMoverComponent extends Manager
{

    function run()
    {
        $this->validateSelectedTreeNodeData();

        $currentNode = $this->getCurrentTreeNode();
        if (!$this->canEditTreeNode($currentNode))
        {
            throw new NotAllowedException();
        }

        $parentId = $this->getRequest()->get(self::PARAM_PARENT_ID);
        $displayOrder = $this->getRequest()->get(self::PARAM_DISPLAY_ORDER);

        if (!isset($parentId) || !isset($displayOrder))
        {
            throw new RuntimeException(
                'For the direct mover to work you need to specify a parent and a display order'
            );
        }

        $path = $this->getTree();

        try
        {
            $parentNode = $path->getTreeNodeById((int) $parentId);
        }
        catch (Exception $ex)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Step'), $parentId);
        }

        try
        {
            $learningPathService = $this->getLearningPathService();
            $learningPathService->moveContentObjectToNewParent(
                $this->getCurrentTreeNode(), $parentNode, $displayOrder
            );
            $success = true;
        }
        catch (Exception $ex)
        {
            $success = false;
        }

        $new_node = null;

        if ($success)
        {
            $content_object = $currentNode->getContentObject();

            Event::trigger(
                'Activity', \Chamilo\Core\Repository\Manager::context(), array(
                    Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED,
                    Activity::PROPERTY_USER_ID => $this->get_user_id(),
                    Activity::PROPERTY_DATE => time(),
                    Activity::PROPERTY_CONTENT_OBJECT_ID => $content_object->getId(),
                    Activity::PROPERTY_CONTENT => $content_object->get_title()
                )
            );
        }

        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectUpdated' : 'ObjectNotUpdated'), array('OBJECT' => Translation::get('ContentObject')),
                Utilities::COMMON_LIBRARIES
            )
        );

        $parameters = [];
        $parameters[self::PARAM_CHILD_ID] = $this->getCurrentTreeNode()->getId();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirect($message, (!$success), $parameters, array(self::PARAM_CONTENT_OBJECT_ID));
    }
}
