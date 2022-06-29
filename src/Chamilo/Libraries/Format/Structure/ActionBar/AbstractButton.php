<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractButton extends AbstractButtonToolBarItem
{
    public const DISPLAY_ICON = 1;
    public const DISPLAY_ICON_AND_LABEL = 3;
    public const DISPLAY_LABEL = 2;

    private int $display;

    private ?InlineGlyph $inlineGlyph;

    private ?string $label;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, int $display = self::DISPLAY_ICON_AND_LABEL,
        array $classes = []
    )
    {
        parent::__construct($classes);

        $this->label = $label;
        $this->display = $display;
        $this->inlineGlyph = $inlineGlyph;
    }

    public function getDisplay(): int
    {
        return $this->display;
    }

    public function setDisplay(int $display)
    {
        $this->display = $display;
    }

    public function getInlineGlyph(): ?InlineGlyph
    {
        return $this->inlineGlyph;
    }

    public function setInlineGlyph(?InlineGlyph $inlineGlyph)
    {
        $this->inlineGlyph = $inlineGlyph;
    }

    /**
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label)
    {
        $this->label = $label;
    }
}