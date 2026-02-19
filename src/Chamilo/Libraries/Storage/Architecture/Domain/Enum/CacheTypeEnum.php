<?php

namespace Chamilo\Libraries\Storage\Architecture\Domain\Enum;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Enum
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
enum CacheTypeEnum: int
{
    case COUNT = 1;
    case COUNT_GROUPED = 2;
    case DISTINCT = 3;
    case RECORD = 4;
    case RECORDS = 5;
    case RETRIEVE = 6;
    case RETRIEVES = 7;
}
