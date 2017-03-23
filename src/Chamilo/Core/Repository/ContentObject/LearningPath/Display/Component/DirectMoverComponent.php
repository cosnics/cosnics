<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A mover component which moves the selected object directly to a different parent
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DirectMoverComponent extends Manager
{

    function run()
    {
        $this->validateSelectedLearningPathChild();

        $currentNode = $this->getCurrentLearningPathTreeNode();
        if (!$this->canEditLearningPathTreeNode($currentNode))
        {
            throw new NotAllowedException();
        }

        $parentId = $this->getRequest()->get(self::PARAM_PARENT_ID);
        $displayOrder = $this->getRequest()->get(self::PARAM_DISPLAY_ORDER);

        if (!$parentId || !$displayOrder)
        {
            throw new \RuntimeException(
                'For the direct mover to work you need to specify a parent and a display order'
            );
        }

        $path = $this->getLearningPathTree();

        try
        {
            $parentNode = $path->getLearningPathTreeNodeById((int) $parentId);
        }
        catch (\Exception $ex)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Step'), $parentId);
        }

        try
        {
            $learningPathChildService = $this->getLearningPathChildService();
            $learningPathChildService->moveContentObjectToOtherLearningPath(
                $this->getCurrentLearningPathTreeNode(), $parentNode, $displayOrder
            );
            $success = true;
        }
        catch (\Exception $ex)
        {
            $success = false;
        }

        $new_node = null;

        if ($success)
        {
            $content_object = $currentNode->getContentObject();

            Event::trigger(
                'Activity',
                \Chamilo\Core\Repository\Manager::context(),
                array(
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
                ($success ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                array('OBJECT' => Translation::get('ContentObject')),
                Utilities::COMMON_LIBRARIES
            )
        );

        $parameters = array();
        $parameters[self::PARAM_CHILD_ID] = $this->getCurrentLearningPathTreeNode()->getId();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirect($message, (!$success), $parameters, array(self::PARAM_CONTENT_OBJECT_ID));
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID, self::PARAM_FULL_SCREEN);
    }
}
