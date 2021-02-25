<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Domain;

use MyCLabs\Enum\Enum;

final class EntityTypes extends Enum
{
    private const ENTITY_TYPE_USER = 0;
    private const ENTITY_TYPE_COURSE_GROUP = 1;
    private const ENTITY_TYPE_PLATFORM_GROUP = 2;
}