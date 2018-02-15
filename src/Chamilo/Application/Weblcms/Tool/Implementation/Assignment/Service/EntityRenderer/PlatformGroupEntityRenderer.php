<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntityRenderer;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntityRenderer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlatformGroupEntityRenderer extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer
{
    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Assignment\Display\Renderer\EntityRenderer::findEntity()
     */
    public function findEntity()
    {
        return DataManager::retrieve_by_id(Group::class_name(), $this->getEntityId());
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
        /** @var Group $entity */
        $entity = $this->getEntity();
        return $entity->get_name();
    }
}