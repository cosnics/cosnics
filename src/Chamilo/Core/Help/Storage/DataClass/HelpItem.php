<?php
namespace Chamilo\Core\Help\Storage\DataClass;

use Chamilo\Core\Help\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Help\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HelpItem extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONTEXT = 'context';
    public const PROPERTY_IDENTIFIER = 'identifier';
    public const PROPERTY_LANGUAGE = 'language';
    public const PROPERTY_URL = 'url';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_CONTEXT, self::PROPERTY_IDENTIFIER, self::PROPERTY_URL, self::PROPERTY_LANGUAGE]
        );
    }

    public static function getStorageUnitName(): string
    {
        return 'help_help_item';
    }

    public function get_context(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    public function get_identifier(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_IDENTIFIER);
    }

    public function get_language(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_LANGUAGE);
    }

    public function get_url(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_URL);
    }

    public function has_url(): bool
    {
        return (bool) $this->get_url();
    }

    public function set_context(string $context): HelpItem
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);

        return $this;
    }

    public function set_identifier(string $identifier): HelpItem
    {
        $this->setDefaultProperty(self::PROPERTY_IDENTIFIER, $identifier);

        return $this;
    }

    public function set_language(string $language): HelpItem
    {
        $this->setDefaultProperty(self::PROPERTY_LANGUAGE, $language);

        return $this;
    }

    public function set_url(string $url): HelpItem
    {
        $this->setDefaultProperty(self::PROPERTY_URL, $url);

        return $this;
    }
}
