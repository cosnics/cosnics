<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonDisplayTrait
{
    private int $display = 0;

    private ?InlineGlyph $inlineGlyph = null;

    private ?string $label = null;

    public function getDisplay(): int
    {
        return $this->display;
    }

    public function setDisplay(int $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $glyph): static
    {
        $this->inlineGlyph = $glyph;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
