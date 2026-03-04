<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonActionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonAttributesInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonStateInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonActionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonAttributesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonDisplayTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonStateTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButton implements ButtonInterface, ButtonDisplayInterface, ButtonActionInterface, ButtonStateInterface,
    ButtonAttributesInterface
{
    use ButtonClassesTrait;
    use ButtonDisplayTrait;
    use ButtonActionTrait;
    use ButtonStateTrait;
    use ButtonAttributesTrait;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, ?string $action = null,
        DisplayTypeEnum $display = DisplayTypeEnum::ICON_AND_LABEL, ?string $confirmationMessage = null,
        array $classes = [], ?string $target = null, bool $state = false, array $attributes = []
    )
    {
        $this->setLabel($label);
        $this->setInlineGlyph($inlineGlyph);
        $this->setAction($action);
        $this->setDisplay($display);
        $this->setConfirmationMessage($confirmationMessage);
        $this->setClasses($classes);
        $this->setTarget($target);
        $this->setState($state);
        $this->setAttributes($attributes);
    }

    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonRenderer>
     */
    public function getButtonRendererClassName(): string
    {
        return SubButtonRenderer::class;
    }
}