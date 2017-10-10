<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package admin.lib
 * @author Hans De Bisschop
 */
class Registration extends DataClass
{
    // Properties
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_PRIORITY = 'priority';
    const PROPERTY_CATEGORY = 'category';

    // Types
    const TYPE_APPLICATION = 'Chamilo\Application';
    const TYPE_CORE = 'core';
    const TYPE_EXTENSIONS = 'common\extensions';
    const TYPE_LIBRARIES = 'common\libraries';
    const TYPE_CONTENT_OBJECT = 'content_object';
    const TYPE_LANGUAGE = 'language';
    const TYPE_EXTENSION = 'extension';
    const TYPE_LIBRARY = 'library';
    const TYPE_EXTERNAL_REPOSITORY_MANAGER = 'external_repository_manager';
    const TYPE_VIDEO_CONFERENCING_MANAGER = 'video_conferencing_manager';

    // Status
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    private $package;

    /**
     * Get the default properties of registrations.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_ID,
                self::PROPERTY_TYPE,
                self::PROPERTY_NAME,
                self::PROPERTY_STATUS,
                self::PROPERTY_VERSION,
                self::PROPERTY_CONTEXT,
                self::PROPERTY_PRIORITY,
                self::PROPERTY_CATEGORY));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the type of this registration.
     *
     * @return int The type
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     * Returns the name of this registration.
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

    /**
     * Returns the status of this registration.
     *
     * @return int the status
     */
    public function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    /**
     * Returns the version of the registered item.
     *
     * @return String the version
     */
    public function get_version()
    {
        return $this->get_default_property(self::PROPERTY_VERSION);
    }

    /**
     * Sets the type of this registration.
     *
     * @param $id Int the registration type.
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    /**
     * Sets the name of this registration.
     *
     * @param $name int the name.
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the status of this registration.
     *
     * @param $status int the status.
     */
    public function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }

    /**
     * Sets the version of this registered item.
     *
     * @param $version String the version.
     */
    public function set_version($version)
    {
        $this->set_default_property(self::PROPERTY_VERSION, $version);
    }

    /**
     *
     * @return int
     */
    public function get_priority()
    {
        return (int) $this->get_default_property(self::PROPERTY_PRIORITY);
    }

    /**
     *
     * @param int $priority
     *
     * @return $this
     */
    public function set_priority($priority)
    {
        $this->set_default_property(self::PROPERTY_PRIORITY, $priority);

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_category()
    {
        return (string) $this->get_default_property(self::PROPERTY_CATEGORY);
    }

    /**
     *
     * @param string $category
     *
     * @return $this
     */
    public function set_category($category)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY, $category);

        return $this;
    }

    /**
     *
     * @return int
     */
    public function is_active()
    {
        return $this->get_status();
    }

    /**
     * Activates the registration
     *
     * @param $with_update boolean - include update or not
     * @return boolean
     */
    public function activate($with_update = false)
    {
        $this->set_status(true);

        if ($with_update)
        {
            return $this->update();
        }

        true;
    }

    /**
     * Deactivates the registration
     *
     * @param $with_update boolean - include update or not
     * @return boolean
     */
    public function deactivate($with_update = false)
    {
        $this->set_status(false);

        if ($with_update)
        {
            return $this->update();
        }

        return true;
    }

    public function toggle_status()
    {
        $this->set_status(! $this->get_status());
    }

    public function delete()
    {
        return $this->on_change(parent::delete());
    }

    public static function get_types()
    {
        return array(
            self::TYPE_APPLICATION,
            self::TYPE_CONTENT_OBJECT,
            self::TYPE_CORE,
            self::TYPE_LANGUAGE,
            self::TYPE_EXTERNAL_REPOSITORY_MANAGER,
            self::TYPE_VIDEO_CONFERENCING_MANAGER,
            self::TYPE_EXTENSION,
            self::TYPE_LIBRARY);
    }

    public function can_be_activated()
    {
        return ! in_array($this->get_type(), array(self::TYPE_CORE, self::TYPE_EXTENSION, self::TYPE_LIBRARY));
    }

    public function create()
    {
        return $this->on_change(parent::create());
    }

    public function update()
    {
        return $this->on_change(parent::update());
    }

    protected function on_change($success = true)
    {
        if (! $success)
        {
            return $success;
        }

        \Chamilo\Configuration\Configuration::getInstance()->reset();

        return $success;
    }

    public function get_package()
    {
        if (! isset($this->package))
        {
            $this->package = Package::get($this->get_context());
        }

        return $this->package;
    }
}
