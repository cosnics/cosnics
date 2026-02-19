<?php
namespace Chamilo\Core\Admin\Architecture\Enum;

/**
 * @package Chamilo\Core\Admin\Architecture\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum LanguageCodeEnum: string
{
    case ISO_639_1 = 'iso639-1';
    case ISO_639_2 = 'iso639-2';
    case ISO_639_3 = 'iso639-3';
    case LINGUASPHERE = 'linguasphere';
    case GLOTTOLOG = 'glottolog';
}
