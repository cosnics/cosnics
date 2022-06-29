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
    public const SIZE_BIG = 48;
    public const SIZE_MEDIUM = 32;
    public const SIZE_MINI = 16;
    public const SIZE_SMALL = 22;

    public function __construct(
        string $type, bool $isAligned = false, bool $isNew = false, bool $isDisabled = false,
        int $size = IdentGlyph::SIZE_SMALL, array $extraClasses = [], ?string $title = null, string $style = 'fas-ci'
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
     * @return string[]
     */
    public function getBaseClassNames(): array
    {
        $baseClassNames = InlineGlyph::getBaseClassNames();

        $baseClassNames[] = 'fas';
        $baseClassNames[] = $this->getStyle();
        $baseClassNames[] = 'fas-ci-' . $this->getType();

        return $baseClassNames;
    }
}