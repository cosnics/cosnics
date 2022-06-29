<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Button extends AbstractButton
{
    use ActionButtonTrait;

    /**
     * @todo Move this to trait once everyone moves to PHP 5.6. Currently not working in trait due to bug
     *       https://bugs.php.net/bug.php?id=65576
     */
    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, ?string $action = null,
        int $display = self::DISPLAY_ICON_AND_LABEL, ?string $confirmationMessage = null, array $classes = [],
        ?string $target = null
    )
    {
        parent::__construct($label, $inlineGlyph, $display, $classes);
        $this->initializeActionButton($action, $confirmationMessage, $target);
    }
}