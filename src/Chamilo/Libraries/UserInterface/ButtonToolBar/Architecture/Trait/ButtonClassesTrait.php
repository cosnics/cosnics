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

    public function isDanger(): bool
    {
        return in_array('btn-danger', $this->classes);
    }

    public function isDark(): bool
    {
        return in_array('btn-dark', $this->classes);
    }

    public function isInfo(): bool
    {
        return in_array('btn-info', $this->classes);
    }

    public function isLight(): bool
    {
        return in_array('btn-light', $this->classes);
    }

    public function isLink(): bool
    {
        return in_array('btn-link', $this->classes);
    }

    public function isPrimary(): bool
    {
        return in_array('btn-primary', $this->classes);
    }

    public function isSecondary(): bool
    {
        return in_array('btn-secondary', $this->classes);
    }

    public function isSpecial(): bool
    {
        return $this->isPrimary() || $this->isSecondary() || $this->isSuccess() || $this->isWarning() ||
            $this->isDanger() || $this->isDark() || $this->isInfo() || $this->isLight() || $this->isLink();
    }

    public function isSuccess(): bool
    {
        return in_array('btn-success', $this->classes);
    }

    public function isWarning(): bool
    {
        return in_array('btn-warning', $this->classes);
    }
}
