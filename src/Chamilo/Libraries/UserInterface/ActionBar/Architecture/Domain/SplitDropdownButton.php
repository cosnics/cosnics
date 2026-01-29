<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonActionInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDropDownInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonActionTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonDisplayTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonDropDownTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Service\SplitDropdownButtonRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SplitDropdownButton
    implements ButtonInterface, ButtonDisplayInterface, ButtonActionInterface, ButtonDropDownInterface
{
    use ButtonClassesTrait;
    use ButtonDisplayTrait;
    use ButtonActionTrait;
    use ButtonDropDownTrait;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, ?string $action = null,
        int $display = self::DISPLAY_ICON_AND_LABEL, ?string $confirmationMessage = null, array $classes = [],
        ?string $target = null, array $dropDownClasses = [], ArrayCollection $dropDownButtons = new ArrayCollection()
    )
    {
        $this->setLabel($label);
        $this->setInlineGlyph($inlineGlyph);
        $this->setAction($action);
        $this->setDisplay($display);
        $this->setConfirmationMessage($confirmationMessage);
        $this->setClasses($classes);
        $this->setTarget($target);
        $this->setDropDownClasses($dropDownClasses);
        $this->setDropDownButtons($dropDownButtons);
    }

    public function getButtonRendererClass(): string
    {
        return SplitDropdownButtonRenderer::class;
    }
}