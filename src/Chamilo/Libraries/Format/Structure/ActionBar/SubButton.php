<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButton extends Button implements SubButtonInterface
{
    private bool $isActive;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, ?string $action = null,
        int $display = self::DISPLAY_ICON_AND_LABEL, ?string $confirmationMessage = null, array $classes = [],
        ?string $target = null, bool $isActive = false
    )
    {
        parent::__construct($label, $inlineGlyph, $action, $display, $confirmationMessage, $classes, $target);
        $this->isActive = $isActive;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

}