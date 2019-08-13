<?php
namespace Chamilo\Core\Home\Rights;

use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Manager for the components
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_BLOCK_TYPE = 'block_type';
    
    // Actions
    const ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES = 'BrowseBlockTypeTargetEntities';
    const ACTION_SET_BLOCK_TYPE_TARGET_ENTITIES = 'SetBlockTypeTargetEntities';
    const DEFAULT_ACTION = self::ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES;

    /**
     * Returns the URL to the SetBlockTypeTargetEntities Component
     * 
     * @param string $blockType
     *
     * @return string
     */
    public function get_set_block_type_target_entities_url($blockType)
    {
        $parameters = array(
            self::PARAM_ACTION => self::ACTION_SET_BLOCK_TYPE_TARGET_ENTITIES, 
            self::PARAM_BLOCK_TYPE => $blockType);
        
        return $this->get_url($parameters);
    }

    /**
     *
     * @return \Chamilo\Core\Admin\Core\BreadcrumbGenerator
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    /**
     * @return GroupSubscriptionService
     */
    protected function getGroupSubscriptionService()
    {
        return $this->getService(GroupSubscriptionService::class);
    }
}
