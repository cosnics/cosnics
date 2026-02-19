<?php
namespace Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Enum\IdentGlyphSizeEnum;

/**
 * @package Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class IdentGlyph extends FontAwesomeGlyph
{
    public function __construct(
        string $type, bool $isAligned = false, bool $isNew = false, bool $isDisabled = false,
        IdentGlyphSizeEnum $size = IdentGlyphSizeEnum::SMALL, array $extraClasses = [], ?string $title = null,
        string $style = 'fas-ci'
    )
    {
        $classes = [];

        if ($isAligned) {
            $classes[] = 'fas-ci-va';
        }

        if ($isNew) {
            $classes[] = 'fas-ci-new';
        }

        if ($isDisabled) {
            $classes[] = 'fas-ci-disabled';
        }

        $classes[] = $size->toClass();

        foreach ($extraClasses as $extraClass) {
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