<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Configuration\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Setting extends DataClass
{
    public const CONTEXT = 'Chamilo\Configuration';

    public const PROPERTY_APPLICATION = 'context';
    public const PROPERTY_CONTEXT = 'context';
    public const PROPERTY_USER_SETTING = 'user_setting';
    public const PROPERTY_VALUE = 'value';
    public const PROPERTY_VARIABLE = 'variable';

    public function getContext()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTEXT;
        $extendedPropertyNames[] = self::PROPERTY_VARIABLE;
        $extendedPropertyNames[] = self::PROPERTY_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_USER_SETTING;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public static function getStorageUnitName(): string
    {
        return 'configuration_setting';
    }

    public function getUserSetting(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_SETTING);
    }

    public function getValue(): mixed
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    public function getVariable(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_VARIABLE);
    }

    public function setContext(string $context): static
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);

        return $this;
    }

    public function setUserSetting(int $userSetting): static
    {
        $this->setDefaultProperty(self::PROPERTY_USER_SETTING, $userSetting);

        return $this;
    }

    public function setValue(mixed $value): static
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);

        return $this;
    }

    public function setVariable(string $variable): static
    {
        $this->setDefaultProperty(self::PROPERTY_VARIABLE, $variable);

        return $this;
    }
}