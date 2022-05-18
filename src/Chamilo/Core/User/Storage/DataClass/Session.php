<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * user.
 *
 * @author GillardMagali
 */
class Session extends DataClass
{

    const PROPERTY_DATA = 'data';
    const PROPERTY_LIFETIME = 'lifetime';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SAVE_PATH = 'save_path';
    const PROPERTY_SESSION_ID = 'session_id';

    /**
     * Returns the data of this Session.
     *
     * @return string The data.
     */
    public function get_data()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATA);
    }

    /**
     * Get the data class data manager
     *
     * @return \libraries\storage\data_manager\DataManager
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_SESSION_ID;
        $extendedPropertyNames[] = self::PROPERTY_MODIFIED;
        $extendedPropertyNames[] = self::PROPERTY_LIFETIME;
        $extendedPropertyNames[] = self::PROPERTY_DATA;
        $extendedPropertyNames[] = self::PROPERTY_SAVE_PATH;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function get_expiration_time()
    {
        return $this->get_modified() + $this->get_lifetime();
    }

    /**
     * Returns the lifetime of this Session.
     *
     * @return int The lifetime.
     */
    public function get_lifetime()
    {
        return $this->getDefaultProperty(self::PROPERTY_LIFETIME);
    }

    /**
     * Returns the modified of this Session.
     *
     * @return int The modified.
     */
    public function get_modified()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIED);
    }

    /**
     * Returns the name of this Session.
     *
     * @return string The name.
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Returns the save_path of this Session.
     *
     * @return string The save_path.
     */
    public function get_save_path()
    {
        return $this->getDefaultProperty(self::PROPERTY_SAVE_PATH);
    }

    /**
     * Returns the session_id of this Session.
     *
     * @return string The session_id.
     */
    public function get_session_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_SESSION_ID);
    }

    /**
     * Returns the table name for this dataclass
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'user_session';
    }

    public function is_valid()
    {
        return $this->get_expiration_time() > time();
    }

    /**
     * Sets the data of this Session.
     *
     * @param string $data
     */
    public function set_data($data)
    {
        $this->setDefaultProperty(self::PROPERTY_DATA, $data);
    }

    /**
     * Sets the lifetime of this Session.
     *
     * @param int $lifetime
     */
    public function set_lifetime($lifetime)
    {
        $this->setDefaultProperty(self::PROPERTY_LIFETIME, $lifetime);
    }

    /**
     * Sets the modified of this Session.
     *
     * @param int $modified
     */
    public function set_modified($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIED, $modified);
    }

    /**
     * Sets the name of this Session.
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the save_path of this Session.
     *
     * @param string $save_path
     */
    public function set_save_path($save_path)
    {
        $this->setDefaultProperty(self::PROPERTY_SAVE_PATH, $save_path);
    }

    /**
     * Sets the session_id of this Session.
     *
     * @param string $session_id
     */
    public function set_session_id($session_id)
    {
        $this->setDefaultProperty(self::PROPERTY_SESSION_ID, $session_id);
    }
}
