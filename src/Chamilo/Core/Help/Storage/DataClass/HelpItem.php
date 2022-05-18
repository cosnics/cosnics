<?php
namespace Chamilo\Core\Help\Storage\DataClass;

use Chamilo\Core\Help\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package help.lib
 */
class HelpItem extends DataClass
{
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_IDENTIFIER = 'identifier';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_URL = 'url';

    /**
     * Get the default properties of all groups.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_CONTEXT, self::PROPERTY_IDENTIFIER, self::PROPERTY_URL, self::PROPERTY_LANGUAGE));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the name of this group.
     *
     * @return String The name
     */
    public function get_context()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    public function get_identifier()
    {
        return $this->getDefaultProperty(self::PROPERTY_IDENTIFIER);
    }

    /**
     * Returns the url of this group.
     *
     * @return String The url
     */
    public function get_url()
    {
        return $this->getDefaultProperty(self::PROPERTY_URL);
    }

    public function get_language()
    {
        return $this->getDefaultProperty(self::PROPERTY_LANGUAGE);
    }

    /**
     * Sets the name of this group.
     *
     * @param String $name the name.
     */
    public function set_context($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
    }

    public function set_identifier($identifier)
    {
        $this->setDefaultProperty(self::PROPERTY_IDENTIFIER, $identifier);
    }

    /**
     * Sets the url of this group.
     *
     * @param String $url the url.
     */
    public function set_url($url)
    {
        $this->setDefaultProperty(self::PROPERTY_URL, $url);
    }

    public function set_language($language)
    {
        $this->setDefaultProperty(self::PROPERTY_LANGUAGE, $language);
    }

    public function has_url()
    {
        return (bool) $this->get_url();
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'help_help_item';
    }
}
