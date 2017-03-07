<?php

namespace Chamilo\Core\Repository\ContentObject\Forum\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\ForumSubscribe;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Translation;

/**
 * Unsubscribe from the current forum
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UnsubscribeComponent extends Manager
{
    function run()
    {
        $forumId = $this->getRequest()->get(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID
        );

        $translator = Translation::getInstance();
        $contentObjectTranslation = $translator->getTranslation('ContentObject', null, 'Chamilo\Core\Repository');

        if(empty($forumId))
        {
            throw new NoObjectSelectedException($contentObjectTranslation);
        }

        $forum = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(Forum::class_name(), $forumId);

        if(!$forum instanceof Forum)
        {
            throw new ObjectNotExistException($contentObjectTranslation, $forumId);
        }

        $subscription = \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_subscribe(
            $forum->getId(), $this->getUser()->getId()
        );

        if(!$subscription instanceof ForumSubscribe)
        {
            throw new UserException('CouldNotFindForumSubscription');
        }

        $succes = $subscription->delete();

        $message = $succes ? 'SuccesUnSubscribe' : 'UnSuccesUnSubscribe';

        $parameters = $this->get_application()->get_parameters();
        $parameters[\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION] =
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirect($translator->getTranslation($message), !$succes, $parameters, array(self::PARAM_ACTION));
    }
}