<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabContentInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationInterface;
use Chamilo\Libraries\UserInterface\Tab\Service\ContentTabRenderer;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentTab extends GenericTab implements TabInterface, TabNavigationInterface, TabContentInterface
{
    private string $content;

    public function __construct(
        string $identifier, string $label, string $content, ?InlineGlyph $inlineGlyph = null,
        DisplayTypeEnum $display = DisplayTypeEnum::ICON_AND_LABEL, array $classes = []
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph, $display, $classes);
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getTabRendererClassName(): string
    {
        return ContentTabRenderer::class;
    }
}
