<?php

namespace Chamilo\Libraries\Architecture\Enum;

use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;

/**
 * @package Chamilo\Libraries\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum StatusEnum: int
{
    case ERROR = 3;
    case INFORMATION = 4;
    case OK = 1;
    case WARNING = 2;

    public function getGlyph(): FontAwesomeGlyph
    {
        return match ($this) {
            StatusEnum::ERROR => new FontAwesomeGlyph('minus-circle', ['text-danger'], $this->getLabel(), 'fas'),
            StatusEnum::INFORMATION => new FontAwesomeGlyph('lightbulb', ['text-info'], $this->getLabel(), 'fas'),
            StatusEnum::OK => new FontAwesomeGlyph('check-circle', ['text-success'], $this->getLabel(), 'fas'),
            StatusEnum::WARNING => new FontAwesomeGlyph('exclamation-circle', ['text-warning'], $this->getLabel(), 'fas'
            ),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            StatusEnum::ERROR => 'Error',
            StatusEnum::INFORMATION => 'Information',
            StatusEnum::OK => 'Ok',
            StatusEnum::WARNING => 'Warning',
        };
    }
}
