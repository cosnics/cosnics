<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package admin.lib
 * @author  Hans De Bisschop
 */
class Registration extends DataClass
{
    public const CONTEXT = 'Chamilo\Configuration';

    public const PROPERTY_CATEGORY = 'category';
    public const PROPERTY_CONTEXT = 'context';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_PRIORITY = 'priority';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_VERSION = 'version';

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public const TYPE_APPLICATION = 'Chamilo\Application';
    public const TYPE_CONTENT_OBJECT = 'content_object';
    public const TYPE_CORE = 'Chamilo\Core';
    public const TYPE_EXTENSION = 'extension';
    public const TYPE_EXTENSIONS = 'common\extensions';
    public const TYPE_EXTERNAL_REPOSITORY_MANAGER = 'external_repository_manager';
    public const TYPE_LANGUAGE = 'language';
    public const TYPE_LIBRARIES = 'common\libraries';
    public const TYPE_LIBRARY = 'library';
    public const TYPE_VIDEO_CONFERENCING_MANAGER = 'video_conferencing_manager';

    private $package;

    /**
     * Activates the registration
     *
     * @param $with_update bool - include update or not
     *
     * @return bool
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

    public function can_be_activated()
    {
        return !in_array($this->getType(), [self::TYPE_CORE, self::TYPE_EXTENSION, self::TYPE_LIBRARY]);
    }

    public function create(): bool
    {
        return $this->on_change(parent::create());
    }

    /**
     * Deactivates the registration
     *
     * @param $with_update bool - include update or not
     *
     * @return bool
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

    public function delete(): bool
    {
        return $this->on_change(parent::delete());
    }

    /**
     * Get the default properties of registrations.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_ID,
                self::PROPERTY_TYPE,
                self::PROPERTY_NAME,
                self::PROPERTY_STATUS,
                self::PROPERTY_VERSION,
                self::PROPERTY_CONTEXT,
                self::PROPERTY_PRIORITY,
                self::PROPERTY_CATEGORY
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_registration';
    }

    /**
     * Returns the type of this registration.
     *
     * @return int The type
     */
    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     * @return string
     */
    public function get_category()
    {
        return (string) $this->getDefaultProperty(self::PROPERTY_CATEGORY);
    }

    public function get_context()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    /**
     * Returns the name of this registration.
     *
     * @return string
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public function get_package()
    {
        if (!isset($this->package))
        {
            $this->package = Package::get($this->get_context());
        }

        return $this->package;
    }

    /**
     * @return int
     */
    public function get_priority()
    {
        return (int) $this->getDefaultProperty(self::PROPERTY_PRIORITY);
    }

    /**
     * Returns the status of this registration.
     *
     * @return int the status
     */
    public function get_status()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    /**
     * Returns the type of this registration.
     *
     * @deprecated Use Registration::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    public static function get_types()
    {
        return [
            self::TYPE_APPLICATION,
            self::TYPE_CONTENT_OBJECT,
            self::TYPE_CORE,
            self::TYPE_LANGUAGE,
            self::TYPE_EXTERNAL_REPOSITORY_MANAGER,
            self::TYPE_VIDEO_CONFERENCING_MANAGER,
            self::TYPE_EXTENSION,
            self::TYPE_LIBRARY
        ];
    }

    /**
     * Returns the version of the registered item.
     *
     * @return String the version
     */
    public function get_version()
    {
        return $this->getDefaultProperty(self::PROPERTY_VERSION);
    }

    /**
     * @return int
     */
    public function is_active()
    {
        return $this->get_status();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function on_change($success = true)
    {
        if (!$success)
        {
            return $success;
        }

        /**
         * @var \Chamilo\Configuration\Service\Consulter\RegistrationConsulter $registrationConsulter
         */
        $registrationConsulter = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            RegistrationConsulter::class
        );

        $registrationConsulter->getRegistrationCacheDataPreLoader()->clearCacheData();

        return $success;
    }

    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    /**
     * @param string $category
     *
     * @return $this
     */
    public function set_category($category)
    {
        $this->setDefaultProperty(self::PROPERTY_CATEGORY, $category);

        return $this;
    }

    public function set_context($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the name of this registration.
     *
     * @param $name int the name.
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function set_priority($priority)
    {
        $this->setDefaultProperty(self::PROPERTY_PRIORITY, $priority);

        return $this;
    }

    /**
     * Sets the status of this registration.
     *
     * @param $status int the status.
     */
    public function set_status($status)
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }

    /**
     * @deprecated Use Registration::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * Sets the version of this registered item.
     *
     * @param $version String the version.
     */
    public function set_version($version)
    {
        $this->setDefaultProperty(self::PROPERTY_VERSION, $version);
    }

    public function toggle_status()
    {
        $this->set_status(!$this->get_status());
    }

    public function update(): bool
    {
        return $this->on_change(parent::update());
    }
}
