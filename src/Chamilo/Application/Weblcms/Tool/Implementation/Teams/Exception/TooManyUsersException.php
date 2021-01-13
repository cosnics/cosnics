<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;

/***
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TooManyUsersException extends UserException
{
    /**
     * TooManyUsersException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            Translation::getInstance()->getTranslator()->trans("TooManyUsersException", [], Manager::class)
        );
    }
}