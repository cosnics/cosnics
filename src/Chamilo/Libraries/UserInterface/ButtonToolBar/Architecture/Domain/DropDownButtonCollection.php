<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDropDownCollectionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonDisplayTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\DropDownButtonCollectionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\DropDownButtonRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DropDownButtonCollection implements ButtonInterface, ButtonDisplayInterface, ButtonDropDownCollectionInterface
{
    use ButtonClassesTrait;
    use ButtonDisplayTrait;
    use DropDownButtonCollectionTrait;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null,
        DisplayTypeEnum $display = DisplayTypeEnum::ICON_AND_LABEL, array $classes = [], array $dropDownClasses = [],
        ArrayCollection $dropDownButtons = new ArrayCollection()
    )
    {
        $this->setLabel($label);
        $this->setInlineGlyph($inlineGlyph);
        $this->setDisplay($display);
        $this->setClasses($classes);
        $this->setDropDownClasses($dropDownClasses);
        $this->setButtons($dropDownButtons);
    }

    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ButtonToolBar\Service\DropDownButtonRenderer>
     */
    public function getButtonRendererClassName(): string
    {
        return DropDownButtonRenderer::class;
    }
}