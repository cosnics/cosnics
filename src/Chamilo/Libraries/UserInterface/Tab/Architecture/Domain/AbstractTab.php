<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractTab
{
    protected DisplayTypeEnum $display;

    protected string $identifier;

    protected ?InlineGlyph $inlineGlyph;

    protected string $label;

    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph,
        DisplayTypeEnum $display = DisplayTypeEnum::ICON_AND_LABEL
    )
    {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->inlineGlyph = $inlineGlyph;
        $this->display = $display;
    }

    public function getDisplay(): DisplayTypeEnum
    {
        return $this->display;
    }

    public function setDisplay(DisplayTypeEnum $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $inlineGlyph): static
    {
        $this->inlineGlyph = $inlineGlyph;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function isIconVisible(): bool
    {
        return $this->getDisplay() == DisplayTypeEnum::ICON_AND_LABEL || $this->getDisplay() == DisplayTypeEnum::ICON;
    }

    public function isTextVisible(): bool
    {
        return $this->getDisplay() == DisplayTypeEnum::ICON_AND_LABEL || $this->getDisplay() == DisplayTypeEnum::LABEL;
    }
}
