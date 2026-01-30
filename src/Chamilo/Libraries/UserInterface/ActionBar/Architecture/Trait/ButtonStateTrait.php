<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonStateTrait
{
    private bool $state = false;

    public function getState(): bool
    {
        return $this->state;
    }

    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
    }
}
