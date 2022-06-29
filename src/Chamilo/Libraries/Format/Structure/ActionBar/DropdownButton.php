<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DropdownButton extends AbstractButton
{
    use DropdownButtonTrait;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, int $display = self::DISPLAY_ICON_AND_LABEL,
        array $classes = [], array $dropdownClasses = [], array $subButtons = []
    )
    {
        parent::__construct($label, $inlineGlyph, $display, $classes);
        $this->initializeDropdownButton($dropdownClasses, $subButtons);
    }

}