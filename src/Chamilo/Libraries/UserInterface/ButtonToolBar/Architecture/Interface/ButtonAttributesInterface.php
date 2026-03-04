<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonAttributesInterface extends ButtonInterface
{
    /**
     * @param string[] $value
     */
    public function addAttribute(string $name, array $value): static;

    /**
     * @param string[][] $attributes
     */
    public function addAttributes(array $attributes = []): static;

    /**
     * @return string[]
     */
    public function getAttribute(string $name): array;

    /**
     * @return string[][]
     */
    public function getAttributes(): array;

    /**
     * @param string[][] $attributes
     */
    public function setAttributes(array $attributes): static;
}