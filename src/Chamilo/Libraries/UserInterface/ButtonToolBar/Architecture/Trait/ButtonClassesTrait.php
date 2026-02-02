<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonClassesTrait
{
    /**
     * @var string[]
     */
    private array $classes = [];

    public function addClass(string $class): static
    {
        $this->classes[] = $class;

        return $this;
    }

    public function addClasses(array $classes = []): static
    {
        $this->classes = array_merge($this->classes, $classes);

        return $this;
    }

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
