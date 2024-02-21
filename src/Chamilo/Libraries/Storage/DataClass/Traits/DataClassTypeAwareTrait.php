<?php
namespace Chamilo\Libraries\Storage\DataClass\Traits;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassTypeAwareInterface;

/**
 * @package Chamilo\Libraries\Storage\DataClass\Traits
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataClassTypeAwareTrait
{

    abstract public function getDefaultProperty(string $name): mixed;

    public function getType(): string
    {
        return $this->getDefaultProperty(DataClassTypeAwareInterface::PROPERTY_TYPE);
    }

    abstract public function setDefaultProperty(string $name, mixed $value): static;

    public function setType(string $type): static
    {
        $this->setDefaultProperty(DataClassTypeAwareInterface::PROPERTY_TYPE, $type);

        return $this;
    }
}
