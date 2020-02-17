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
    /**
     * @var string
     */
    private $style;

    /**
     * @param string $type
     * @param string[] $extraClasses
     * @param string $title
     * @param string $style
     */
    public function __construct($type, $extraClasses = array(), $title = null, $style = 'fa')
    {
        parent::__construct($type, $extraClasses, $title);
        $this->style = $style;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @param string $style
     *
     * @return FontAwesomeGlyph
     */
    public function setStyle(string $style): FontAwesomeGlyph
    {
        $this->style = $style;

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph::getBaseClassNames()
     */
    public function getBaseClassNames()
    {
        $baseClassNames = parent::getBaseClassNames();

        $baseClassNames[] = $this->getStyle();
        $baseClassNames[] = 'fa-' . $this->getType();

        return $baseClassNames;
    }
}