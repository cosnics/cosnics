<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TabNavigationInterface
{
    public function getIdentifier(): string;

    public function getInlineGlyph(): ?InlineGlyph;

    public function getLabel(): string;

    public function isIconVisible(): bool;

    public function isTextVisible(): bool;

    public function setLabel(string $label): static;
}