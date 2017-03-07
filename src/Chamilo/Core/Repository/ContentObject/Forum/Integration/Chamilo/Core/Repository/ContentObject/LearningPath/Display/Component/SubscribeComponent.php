<?php

namespace Chamilo\Core\Repository\ContentObject\Forum\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;

/**
 * Subscribe to the current forum
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubscribeComponent extends Manager
{
    /**
     * Runs this component and displays it's result
     *
     * @throws NoObjectSelectedException
     * @throws ObjectNotExistException
     */
    function run()
    {
        $forumId = $this->getRequest()->get(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID
        );

        $translator = Translation::getInstance();
        $contentObjectTranslation = $translator->getTranslation('ContentObject', null, 'Chamilo\Core\Repository');

        if (empty($forumId))
        {
            throw new NoObjectSelectedException($contentObjectTranslation);
        }

        $forum = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(Forum::class_name(), $forumId);

        if (!$forum instanceof Forum)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $forumId);
        }

        $succes = \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::create_subscribe(
            $this->getUser()->getId(), $forum->getId()
        );

        $message = $succes ? 'SuccesSubscribe' : 'UnSuccesSubscribe';

        $parameters = $this->get_application()->get_parameters();
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION] =
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirect($translator->getTranslation($message), !$succes, $parameters, array(self::PARAM_ACTION));
    }
}