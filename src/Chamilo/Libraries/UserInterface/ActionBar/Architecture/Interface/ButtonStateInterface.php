<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonStateInterface extends ButtonInterface
{
    public function getState(): bool;

    public function setState(bool $state): static;
}
