<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentTab extends Tab
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

    public function body(bool $isOnlyTab = false): string
    {
        $html = [];
        $html[] = $this->bodyHeader();
        $html[] = $this->content;
        $html[] = $this->bodyFooter();

        return implode(PHP_EOL, $html);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function getLink(): string
    {
        return '#' . $this->getIdentifier();
    }
}
