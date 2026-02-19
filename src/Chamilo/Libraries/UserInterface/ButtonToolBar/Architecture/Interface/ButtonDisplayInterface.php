<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonDisplayInterface extends ButtonInterface
{
    public function getDisplay(): DisplayTypeEnum;

    public function getInlineGlyph(): ?InlineGlyph;

    public function getLabel(): ?string;

    public function setDisplay(DisplayTypeEnum $display): static;

    public function setInlineGlyph(?InlineGlyph $glyph): static;

    public function setLabel(?string $label): static;
}
