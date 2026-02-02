<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonDisplayTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonHeaderRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonHeader implements ButtonInterface, ButtonDisplayInterface
{
    use ButtonClassesTrait;
    use ButtonDisplayTrait;

    public function __construct(
        ?string $label = null, ?InlineGlyph $inlineGlyph = null, int $display = self::DISPLAY_LABEL, array $classes = []
    )
    {
        $this->setLabel($label);
        $this->setInlineGlyph($inlineGlyph);
        $this->setDisplay($display);
        $this->setClasses($classes);
    }

    /**
     * @return class-string<\Chamilo\Libraries\UserInterface\ButtonToolBar\Service\SubButtonHeaderRenderer>
     */
    public function getButtonRendererClass(): string
    {
        return SubButtonHeaderRenderer::class;
    }
}