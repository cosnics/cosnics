<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Package\Properties\Dependencies
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Dependencies extends ArrayCollection
{
    /**
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency $dependency
     */
    public function add_dependency(Dependency $dependency): void
    {
        $this->add($dependency);
    }

    /**
     * @return \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency[]
     */
    public function getDependencies(): array
    {
        return $this->toArray();
    }

    /**
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency[] $dependencies
     */
    public function set_dependencies(array $dependencies = []): void
    {
        $this->clear();

        foreach ($dependencies as $dependency)
        {
            $this->add($dependency);
        }
    }
}
