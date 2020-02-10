<?php
namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\JsonResponse;
use function array_key_exists;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AjaxExceptionResponse extends JsonResponse
{
    public function __construct(\Exception $ex)
    {
        $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $errorCodeMapping = [
            ObjectNotExistException::class => JsonResponse::HTTP_NOT_FOUND,
            NoObjectSelectedException::class => JsonResponse::HTTP_NOT_FOUND,
            OptimisticLockException::class => JsonResponse::HTTP_CONFLICT,
            NotAllowedException::class => JsonResponse::HTTP_FORBIDDEN,

        ];

        $exceptionClass = get_class($ex);
        if(array_key_exists($exceptionClass, $errorCodeMapping))
        {
            $code = $errorCodeMapping[$exceptionClass];
        }

        parent::__construct(['error' => ['code' => $code, 'message' => $ex->getMessage()]]);
    }
}
