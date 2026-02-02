<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonDisplayInterface extends ButtonInterface
{
    public const DISPLAY_ICON = 1;
    public const DISPLAY_ICON_AND_LABEL = 3;
    public const DISPLAY_LABEL = 2;

    public function getDisplay(): int;

    public function getInlineGlyph(): ?InlineGlyph;

    public function getLabel(): ?string;

    public function setDisplay(int $display): static;

    public function setInlineGlyph(?InlineGlyph $glyph): static;

    public function setLabel(?string $label): static;
}
