<?php

namespace Chamilo\Libraries\UserInterface\Glyph\Architecture\Enum;

/**
 * @package Chamilo\Libraries\UserInterface\Glyph\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum IdentGlyphSizeEnum: int
{
    case BIG = 48;
    case MEDIUM = 32;
    case MINI = 16;
    case SMALL = 22;

    public function toClass(): string
    {
        return match ($this) {
            IdentGlyphSizeEnum::BIG => 'fa-3x',
            IdentGlyphSizeEnum::MEDIUM => 'fa-2x',
            IdentGlyphSizeEnum::MINI => 'fa-1x',
            IdentGlyphSizeEnum::SMALL => 'fa-lg',
        };
    }
}
