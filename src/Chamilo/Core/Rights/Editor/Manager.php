<?php
namespace Chamilo\Core\Rights\Editor;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * Rights editor manager for unlimited amount of entities.
 * With simple and advanced interface.
 *
 * @package    application.common.rights_editor_manager
 * @author     Sven Vanpoucke
 * @deprecated Should not be needed anymore
 */
abstract class Manager extends Application
{
    public const ACTION_EDIT_SIMPLE_RIGHTS = 'SimpleRightsEditor';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_EDIT_SIMPLE_RIGHTS;

    public const PARAM_ACTION = 'rights_action';
    public const PARAM_ENTITY_ID = 'entity_id';
    public const PARAM_ENTITY_TYPE = 'entity_type';
    public const PARAM_RIGHT_ID = 'right_id';

    private $context;

    private $entities;

    private $locations;

    private $selected_entity;

    /**
     * Retrieves additional information from the parent application
     *
     * @return String
     */
    public function get_additional_information()
    {
        if (method_exists($this->get_parent(), 'get_additional_information'))
        {
            return $this->get_parent()->get_additional_information();
        }
    }

    /**
     * Retrieves the available rights
     *
     * @return array
     */
    public function get_available_rights()
    {
        $locations = $this->get_locations();

        return $this->get_parent()->get_available_rights($locations[0]);
    }

    public function get_context()
    {
        return $this->context;
    }

    public function get_entities()
    {
        return $this->entities;
    }

    /**
     * Builds the url to browse an entity
     *
     * @param int $entity_type
     *
     * @return String
     */
    public function get_entity_url($entity_type)
    {
        return $this->get_url([self::PARAM_ENTITY_TYPE => $entity_type]);
    }

    public function get_locations()
    {
        return $this->locations;
    }

    // Url building

    /**
     * Gets the selected entity type and if no type selected, uses the first available entity
     *
     * @return String
     */
    public function get_selected_entity()
    {
        if (!$this->selected_entity)
        {
            $selected_entity = $this->get_parameter(self::PARAM_ENTITY_TYPE);
            if ($selected_entity)
            {
                $this->selected_entity = $this->entities[$selected_entity];
            }
            else
            {
                $first_entity = array_shift($this->get_entities());
                if ($first_entity)
                {
                    array_unshift($this->get_entities(), $first_entity);
                    $this->selected_entity = $first_entity;
                }
            }
        }

        return $this->selected_entity;
    }

    public function set_context($context)
    {
        $this->context = $context;
    }

    public function set_entities($entities)
    {
        if (count($entities) == 0)
        {
            throw new Exception(Translation::get('NoEntitiesSelected'));
        }

        $this->entities = $entities;
    }

    public function set_locations($locations)
    {
        if (count($locations) == 0)
        {
            throw new Exception(Translation::get('NoLocationsSelected'));
        }

        $this->locations = $locations;
    }
}
