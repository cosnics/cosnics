<?php
namespace Chamilo\Core\Repository\External;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class ExternalObject
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var array
     */
    private $default_properties;

    /**
     *
     * @var ExternalSync
     */
    private $synchronization_data;
    const PROPERTY_ID = 'id';
    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_OWNER_NAME = 'owner_name';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_RIGHTS = 'rights';
    const RIGHT_EDIT = 1;
    const RIGHT_DELETE = 2;
    const RIGHT_USE = 3;
    const RIGHT_DOWNLOAD = 4;

    /**
     *
     * @param $default_properties array
     */
    public function __construct($default_properties = array ())
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Get the default properties of all data classes.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_ID;
        $extended_property_names[] = self :: PROPERTY_EXTERNAL_REPOSITORY_ID;
        $extended_property_names[] = self :: PROPERTY_TITLE;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_OWNER_ID;
        $extended_property_names[] = self :: PROPERTY_OWNER_NAME;
        $extended_property_names[] = self :: PROPERTY_CREATED;
        $extended_property_names[] = self :: PROPERTY_MODIFIED;
        $extended_property_names[] = self :: PROPERTY_TYPE;
        $extended_property_names[] = self :: PROPERTY_RIGHTS;
        return $extended_property_names;
    }

    /**
     * Gets a default property of this data class object by name.
     *
     * @param $name string The name of the property.
     * @param mixed
     */
    public function get_default_property($name)
    {
        return (isset($this->default_properties) && array_key_exists($name, $this->default_properties)) ? $this->default_properties[$name] : null;
    }

    /**
     *
     * @param $default_properties the $default_properties to set
     */
    public function set_default_properties($default_properties)
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Sets a default property of this data class by name.
     *
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function set_default_property($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    public function get_default_properties()
    {
        return $this->default_properties;
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     *
     * @return string
     */
    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     *
     * @return int
     */
    public function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     *
     * @return string
     */
    public function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    public function get_owner_name()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_NAME);
    }

    public function set_owner_name($owner_name)
    {
        return $this->set_default_property(self :: PROPERTY_OWNER_NAME, $owner_name);
    }

    /**
     *
     * @return int
     */
    public function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     *
     * @return int
     */
    public function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     *
     * @return array
     */
    public function get_rights()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHTS);
    }

    /**
     *
     * @param $right int
     * @return boolean
     */
    public function get_right($right)
    {
        $rights = $this->get_rights();
        if (! in_array($right, array_keys($rights)))
        {
            return false;
        }
        else
        {
            return $rights[$right];
        }
    }

    public static function get_available_rights()
    {
        return array(self :: RIGHT_DELETE, self :: RIGHT_DOWNLOAD, self :: RIGHT_EDIT, self :: RIGHT_USE);
    }

    /**
     *
     * @param $title string
     */
    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     *
     * @param $id string
     */
    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     *
     * @param $external_repository_id int
     */
    public function set_external_repository_id($external_repository_id)
    {
        $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    /**
     *
     * @param $description string
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @param $owner_id string
     */
    public function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    /**
     *
     * @param $created int
     */
    public function set_created($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    /**
     *
     * @param $modified int
     */
    public function set_modified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    /**
     *
     * @param $type string
     */
    public function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     *
     * @param $rights array
     */
    public function set_rights($rights)
    {
        $this->set_default_property(self :: PROPERTY_RIGHTS, $rights);
    }

    /**
     *
     * @param $right int
     * @param $value boolean
     */
    public function set_right($right, $value)
    {
        $rights = $this->get_rights();
        $rights[$right] = $value;
        $this->set_rights($rights);
    }

    /**
     * Gets the name of the icon corresponding to this external_repository object.
     */
    public function get_icon_name()
    {
        return $this->get_type();
    }

    /**
     *
     * @return string
     */
    public function get_icon_image()
    {
        $source = Theme :: getInstance()->getImagePath(
            static :: context(),
            'Types/' . StringUtilities :: getInstance()->createString($this->get_icon_name())->upperCamelize());
        
        $name = Translation :: get(
            'Type' . StringUtilities :: getInstance()->createString($this->get_type())->upperCamelize(),
            null,
            static :: context());

        return '<img src="' . $source . '" alt="' . $name . '" title="' . $name . '" />';
    }

    /**
     *
     * @return boolean
     */
    public function is_usable()
    {
        return $this->get_right(self :: RIGHT_USE);
    }

    /**
     *
     * @return boolean
     */
    public function is_editable()
    {
        return $this->get_right(self :: RIGHT_EDIT);
    }

    /**
     *
     * @return boolean
     */
    public function is_deletable()
    {
        return $this->get_right(self :: RIGHT_DELETE);
    }

    /**
     *
     * @return boolean
     */
    public function is_downloadable()
    {
        return $this->get_right(self :: RIGHT_DOWNLOAD);
    }

    /**
     *
     * @return ExternalSync
     */
    public function get_synchronization_data()
    {
        if (! isset($this->synchronization_data))
        {
            $sync_conditions = array();
            $sync_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData :: class_name(),
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData :: PROPERTY_EXTERNAL_OBJECT_ID),
                new StaticConditionVariable($this->get_id()));
            $sync_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData :: class_name(),
                    \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData :: PROPERTY_EXTERNAL_ID),
                new StaticConditionVariable($this->get_external_repository_id()));
            $sync_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
                new StaticConditionVariable(Session :: get_user_id()),
                ContentObject :: get_table_name());
            $sync_condition = new AndCondition($sync_conditions);

            $this->synchronization_data = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_synchronization_data(
                $sync_condition);
        }

        return $this->synchronization_data;
    }

    /**
     *
     * @return int
     */
    public function get_synchronization_status()
    {
        return $this->get_synchronization_data()->get_synchronization_status(null, $this->get_modified());
    }

    /**
     *
     * @return boolean
     */
    public function is_importable()
    {
        return ! $this->get_synchronization_data() instanceof \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
    }

    public function get_connector()
    {
        $external_instance = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_by_id(
            Instance :: class_name(),
            $this->get_external_repository_id());
        return DataConnector :: getInstance($external_instance);
    }

    /**
     * Get the type of the ExternalObject
     *
     * @return string
     * @deprecated Use context() to get the namespace (= type)
     */
    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
