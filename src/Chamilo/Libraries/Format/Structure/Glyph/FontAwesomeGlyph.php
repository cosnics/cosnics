<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\Glyph
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FontAwesomeGlyph extends InlineGlyph
{
    private string $style;

    public function __construct(string $type, array $extraClasses = [], ?string $title = null, string $style = 'fas')
    {
        parent::__construct($type, $extraClasses, $title);
        $this->style = $style;
    }

    /**
     * @return string[]
     */
    public function getBaseClassNames(): array
    {
        $baseClassNames = parent::getBaseClassNames();

        $baseClassNames[] = $this->getStyle();
        $baseClassNames[] = 'fa-' . $this->getType();

        return $baseClassNames;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function setStyle(string $style): FontAwesomeGlyph
    {
        $this->style = $style;

        return $this;
    }
}