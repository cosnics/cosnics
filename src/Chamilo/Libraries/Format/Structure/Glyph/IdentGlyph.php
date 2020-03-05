<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\Glyph
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class IdentGlyph extends FontAwesomeGlyph
{
    /**
     * @param string $type
     * @param boolean $isAligned
     * @param boolean $isNew
     * @param boolean $isDisabled
     * @param integer $size
     * @param string[] $extraClasses
     * @param string $title
     * @param string $style
     */
    public function __construct(
        $type, $isAligned = false, $isNew = false, $isDisabled = false, $size = Theme::ICON_SMALL,
        $extraClasses = array(), $title = null, $style = 'fas-ci'
    )
    {
        $classes = array();

        if ($isAligned)
        {
            $classes[] = 'fas-ci-va';
        }

        if ($isNew)
        {
            $classes[] = 'fas-ci-new';
        }

        if ($isDisabled)
        {
            $classes[] = 'fas-ci-disabled';
        }

        switch ($size)
        {
            case Theme::ICON_SMALL;
                $classes[] = 'fa-lg';
                break;
            case Theme::ICON_MEDIUM;
                $classes[] = 'fa-2x';
                break;
            case Theme::ICON_BIG;
                $classes[] = 'fa-3x';
                break;
        }

        foreach ($extraClasses as $extraClass)
        {
            $classes[] = $extraClass;
        }

        parent::__construct($type, $classes, $title, $style);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph::getBaseClassNames()
     */
    public function getBaseClassNames()
    {
        $baseClassNames = InlineGlyph::getBaseClassNames();

        $baseClassNames[] = 'fas';
        $baseClassNames[] = $this->getStyle();
        $baseClassNames[] = 'fas-ci-' . $this->getType();

        return $baseClassNames;
    }
}