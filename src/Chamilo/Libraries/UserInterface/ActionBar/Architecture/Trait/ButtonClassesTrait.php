<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonClassesTrait
{
    /**
     * @var string[]
     */
    private array $classes = [];

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function setClasses(array $classes): static
    {
        $this->classes = $classes;

        return $this;
    }
}
