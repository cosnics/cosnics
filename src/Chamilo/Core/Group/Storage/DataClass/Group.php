<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Sven Vanpoucke
 */
class Group extends NestedSet
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CODE = 'code';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_NAME = 'name';

    public function getCode(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CODE);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_NAME,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_CODE
            ]
        );
    }

    public function getDescription(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    public function getName(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public static function getStorageUnitName(): string
    {
        return 'group_group';
    }

    public function setCode(?string $code): static
    {
        $this->setDefaultProperty(self::PROPERTY_CODE, $code);

        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);

        return $this;
    }

    public function setName(?string $name): static
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);

        return $this;
    }
}
