<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDropDownInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonDisplayTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonDropDownTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Service\DropDownButtonRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DropDownButton implements ButtonInterface, ButtonDisplayInterface, ButtonDropDownInterface
{
    use ButtonClassesTrait;
    use ButtonDisplayTrait;
    use ButtonDropDownTrait;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, int $display = self::DISPLAY_ICON_AND_LABEL,
        array $classes = [], array $dropDownClasses = [], ArrayCollection $dropDownButtons = new ArrayCollection()
    )
    {
        $this->setLabel($label);
        $this->setInlineGlyph($inlineGlyph);
        $this->setDisplay($display);
        $this->setClasses($classes);
        $this->setDropDownClasses($dropDownClasses);
        $this->setDropDownButtons($dropDownButtons);
    }

    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ActionBar\Service\DropDownButtonRenderer>
     */
    public function getButtonRendererClass(): string
    {
        return DropDownButtonRenderer::class;
    }
}