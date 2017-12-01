<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPath\Assignment;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPath\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntityRenderer extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer
{
    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer::findEntity()
     */
    public function findEntity()
    {
        return DataManager::retrieve_by_id(User::class_name(), $this->getEntityId());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer::renderProperties()
     */
    public function renderProperties(\Chamilo\Libraries\Storage\DataClass\DataClass $user)
    {
        $properties = array();
        $properties[Translation::get('SubmittedBy')] = $this->getEntityName();
        return $properties;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer::getEntityName()
     */
    public function getEntityName()
    {
        /** @var User $entity */
        $entity = $this->getEntity();
        return $entity->get_fullname();
    }
}