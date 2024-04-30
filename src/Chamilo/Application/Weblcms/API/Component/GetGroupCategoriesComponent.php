<?php

namespace Chamilo\Application\Weblcms\API\Component;

use Chamilo\Application\Weblcms\API\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetGroupCategoriesComponent extends Manager
{
    function run(): JsonResponse
    {
        return new JsonResponse([]);
    }
}