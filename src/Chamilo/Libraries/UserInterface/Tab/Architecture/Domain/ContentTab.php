<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentTab extends GenericTab
{
    private string $content;

    public function __construct(
        string $identifier, string $label, string $content, ?InlineGlyph $inlineGlyph = null,
        int $display = self::DISPLAY_ICON_AND_TITLE
    )
    {
        parent::__construct($identifier, $label, $inlineGlyph, $display);
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
}
