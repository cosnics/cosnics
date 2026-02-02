<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonInterface
{
    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface>
     */
    public function getButtonRendererClass(): string;

    public function getClasses(): array;

    public function setClasses(array $classes): static;
}