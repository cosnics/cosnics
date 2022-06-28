<?php
namespace Chamilo\Core\Home\Rights;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Manager for the components
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES = 'BrowseBlockTypeTargetEntities';
    public const ACTION_SET_BLOCK_TYPE_TARGET_ENTITIES = 'SetBlockTypeTargetEntities';

    public const DEFAULT_ACTION = self::ACTION_BROWSE_BLOCK_TYPE_TARGET_ENTITIES;

    public const PARAM_BLOCK_TYPE = 'block_type';

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

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
            self::PARAM_BLOCK_TYPE => $blockType
        );

        return $this->get_url($parameters);
    }
}
