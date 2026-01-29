<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonInterface
{
    public function getButtonRendererClass(): string;

    public function getClasses(): array;

    public function setClasses(array $classes): static;
}