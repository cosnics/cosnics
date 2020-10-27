<?php

namespace Chamilo\Application\ExamAssignment\Ajax\Component;

use Chamilo\Application\ExamAssignment\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class UploadExamComponent extends Manager
{
    function run()
    {
        return new JsonResponse([], 200);
    }

}
