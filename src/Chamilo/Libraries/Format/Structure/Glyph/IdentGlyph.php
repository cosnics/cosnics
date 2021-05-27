<?php
namespace Chamilo\Libraries\Format\Structure\Glyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\Glyph
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class IdentGlyph extends FontAwesomeGlyph
{
    const SIZE_BIG = 48;
    const SIZE_MEDIUM = 32;
    const SIZE_MINI = 16;
    const SIZE_SMALL = 22;

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
        $type, $isAligned = false, $isNew = false, $isDisabled = false, $size = IdentGlyph::SIZE_SMALL,
        $extraClasses = [], $title = null, $style = 'fas-ci'
    )
    {
        $classes = [];

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
            case IdentGlyph::SIZE_SMALL;
                $classes[] = 'fa-lg';
                break;
            case IdentGlyph::SIZE_MEDIUM;
                $classes[] = 'fa-2x';
                break;
            case IdentGlyph::SIZE_BIG;
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