<?php
namespace Chamilo\Core\Repository\External;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class ExternalObject
{
    use ClassContext;

    const PROPERTY_CREATED = 'created';

    const PROPERTY_DESCRIPTION = 'description';

    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';

    const PROPERTY_ID = 'id';

    const PROPERTY_MODIFIED = 'modified';

    const PROPERTY_OWNER_ID = 'owner_id';

    const PROPERTY_OWNER_NAME = 'owner_name';

    const PROPERTY_RIGHTS = 'rights';

    const PROPERTY_TITLE = 'title';

    const PROPERTY_TYPE = 'type';

    const RIGHT_DELETE = 2;

    const RIGHT_DOWNLOAD = 4;

    const RIGHT_EDIT = 1;

    const RIGHT_USE = 3;

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

    /**
     *
     * @param $default_properties array
     */
    public function __construct($default_properties = [])
    {
        $this->default_properties = $default_properties;
    }

    /**
     * @return string
     */
    public function getGlyphNamespace()
    {
        return self::package() . '\Type\\' .
            StringUtilities::getInstance()->createString($this->get_icon_name())->upperCamelize();
    }

    public static function get_available_rights()
    {
        return array(self::RIGHT_DELETE, self::RIGHT_DOWNLOAD, self::RIGHT_EDIT, self::RIGHT_USE);
    }

    public function get_connector()
    {
        $external_instance = DataManager::retrieve_by_id(
            Instance::class, $this->get_external_repository_id()
        );

        return DataConnector::getInstance($external_instance);
    }

    /**
     *
     * @return int
     */
    public function get_created()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATED);
    }

    public function getDefaultProperties()
    {
        return $this->default_properties;
    }

    /**
     *
     * @param $default_properties the $default_properties to set
     */
    public function setDefaultProperties($default_properties)
    {
        $this->default_properties = $default_properties;
    }

    /**
     * Gets a default property of this data class object by name.
     *
     * @param $name string The name of the property.
     * @param mixed
     */
    public function getDefaultProperty($name)
    {
        return (isset($this->default_properties) && array_key_exists($name, $this->default_properties)) ?
            $this->default_properties[$name] : null;
    }

    /**
     * Get the default properties of all data classes.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_ID;
        $extendedPropertyNames[] = self::PROPERTY_EXTERNAL_REPOSITORY_ID;
        $extendedPropertyNames[] = self::PROPERTY_TITLE;
        $extendedPropertyNames[] = self::PROPERTY_DESCRIPTION;
        $extendedPropertyNames[] = self::PROPERTY_OWNER_ID;
        $extendedPropertyNames[] = self::PROPERTY_OWNER_NAME;
        $extendedPropertyNames[] = self::PROPERTY_CREATED;
        $extendedPropertyNames[] = self::PROPERTY_MODIFIED;
        $extendedPropertyNames[] = self::PROPERTY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_RIGHTS;

        return $extendedPropertyNames;
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    /**
     *
     * @return int
     */
    public function get_external_repository_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    /**
     * @param int $size
     * @param bool $isAvailable
     * @param string[] $extraClasses
     *
     * @return string
     */
    public function get_icon_image(
        $size = IdentGlyph::SIZE_SMALL, $isAvailable = true,
        $extraClasses = []
    )
    {

        $glyphTitle = Translation::get(
            'Type' . StringUtilities::getInstance()->createString($this->get_type())->upperCamelize(), null,
            static::context()
        );

        $glyph = new NamespaceIdentGlyph(
            $this->getGlyphNamespace(), true, false, !$isAvailable, $size, $extraClasses, $glyphTitle
        );

        return $glyph->render();
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
    public function get_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ID);
    }

    /**
     *
     * @return int
     */
    public function get_modified()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    /**
     *
     * @return string
     */
    public function get_owner_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_OWNER_ID);
    }

    public function get_owner_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_OWNER_NAME);
    }

    /**
     *
     * @param $right int
     *
     * @return boolean
     */
    public function get_right($right)
    {
        $rights = $this->get_rights();
        if (!in_array($right, array_keys($rights)))
        {
            return false;
        }
        else
        {
            return $rights[$right];
        }
    }

    /**
     *
     * @return array
     */
    public function get_rights()
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHTS);
    }

    /**
     *
     * @return ExternalSync
     */
    public function get_synchronization_data()
    {
        if (!isset($this->synchronization_data))
        {
            $sync_conditions = [];
            $sync_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    SynchronizationData::class, SynchronizationData::PROPERTY_EXTERNAL_OBJECT_ID
                ), new StaticConditionVariable($this->get_id())
            );
            $sync_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    SynchronizationData::class, SynchronizationData::PROPERTY_EXTERNAL_ID
                ), new StaticConditionVariable($this->get_external_repository_id())
            );
            $sync_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                new StaticConditionVariable(Session::get_user_id()), ContentObject::getTableName()
            );
            $sync_condition = new AndCondition($sync_conditions);

            $this->synchronization_data = DataManager::retrieve_synchronization_data(
                $sync_condition
            );
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
     * @return string
     */
    public function get_title()
    {
        return $this->getDefaultProperty(self::PROPERTY_TITLE);
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     *
     * @return boolean
     */
    public function is_deletable()
    {
        return $this->get_right(self::RIGHT_DELETE);
    }

    /**
     *
     * @return boolean
     */
    public function is_downloadable()
    {
        return $this->get_right(self::RIGHT_DOWNLOAD);
    }

    /**
     *
     * @return boolean
     */
    public function is_editable()
    {
        return $this->get_right(self::RIGHT_EDIT);
    }

    /**
     *
     * @return boolean
     */
    public function is_importable()
    {
        return !$this->get_synchronization_data() instanceof SynchronizationData;
    }

    /**
     *
     * @return boolean
     */
    public function is_usable()
    {
        return $this->get_right(self::RIGHT_USE);
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static::context();
    }

    /**
     *
     * @param $created int
     */
    public function set_created($created)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATED, $created);
    }

    /**
     * Sets a default property of this data class by name.
     *
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function setDefaultProperty($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    /**
     *
     * @param $description string
     */
    public function set_description($description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @param $external_repository_id int
     */
    public function set_external_repository_id($external_repository_id)
    {
        $this->setDefaultProperty(self::PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    /**
     *
     * @param $id string
     */
    public function set_id($id)
    {
        $this->setDefaultProperty(self::PROPERTY_ID, $id);
    }

    /**
     *
     * @param $modified int
     */
    public function set_modified($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     *
     * @param $owner_id string
     */
    public function set_owner_id($owner_id)
    {
        $this->setDefaultProperty(self::PROPERTY_OWNER_ID, $owner_id);
    }

    public function set_owner_name($owner_name)
    {
        return $this->setDefaultProperty(self::PROPERTY_OWNER_NAME, $owner_name);
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
     *
     * @param $rights array
     */
    public function set_rights($rights)
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHTS, $rights);
    }

    /**
     *
     * @param $title string
     */
    public function set_title($title)
    {
        $this->setDefaultProperty(self::PROPERTY_TITLE, $title);
    }

    /**
     *
     * @param $type string
     */
    public function set_type($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }
}
