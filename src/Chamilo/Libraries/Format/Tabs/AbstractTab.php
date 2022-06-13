<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractTab
{
    public const DISPLAY_ICON = 1;
    public const DISPLAY_ICON_AND_TITLE = 3;
    public const DISPLAY_TITLE = 2;

    protected int $display;

    protected string $identifier;

    protected ?InlineGlyph $inlineGlyph;

    protected string $label;

    public function __construct(
        string $identifier, string $label, ?InlineGlyph $inlineGlyph, int $display = self::DISPLAY_ICON_AND_TITLE
    )
    {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->inlineGlyph = $inlineGlyph;
        $this->display = $display;
    }

    public function getDisplay(): int
    {
        return $this->display;
    }

    public function setDisplay(int $display)
    {
        $this->display = $display;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $inlineGlyph)
    {
        $this->inlineGlyph = $inlineGlyph;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    public function isIconVisible(): bool
    {
        return $this->getDisplay() == self::DISPLAY_ICON_AND_TITLE || $this->getDisplay() == self::DISPLAY_ICON;
    }

    public function isTextVisible(): bool
    {
        return $this->getDisplay() == self::DISPLAY_ICON_AND_TITLE || $this->getDisplay() == self::DISPLAY_TITLE;
    }
}
