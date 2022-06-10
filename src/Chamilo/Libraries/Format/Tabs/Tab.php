<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Tab
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

    abstract public function render(bool $isOnlyTab = false): string;

    public function bodyFooter(): string
    {
        $html = [];

        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function bodyHeader(): string
    {
        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $this->getIdentifier() . '">';
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
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

    abstract public function getLink(): string;

    public function header(): string
    {
        $html = [];
        $html[] = '<li>';
        $html[] = '<a title="' . htmlentities(strip_tags($this->getLabel())) . '" href="' . $this->getLink() . '">';
        $html[] = '<span class="category">';

        if ($this->getInlineGlyph() && $this->isIconVisible())
        {
            $html[] = $this->getInlineGlyph()->render();
        }

        if ($this->getLabel() && $this->isTextVisible())
        {
            $html[] = '<span class="title">' . $this->getLabel() . '</span>';
        }

        $html[] = '</span>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    protected function isIconVisible(): bool
    {
        return $this->getDisplay() == self::DISPLAY_ICON_AND_TITLE || $this->getDisplay() == self::DISPLAY_ICON;
    }

    protected function isTextVisible(): bool
    {
        return $this->getDisplay() == self::DISPLAY_ICON_AND_TITLE || $this->getDisplay() == self::DISPLAY_TITLE;
    }
}
