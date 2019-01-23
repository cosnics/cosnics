<?php
/**
 * Created by PhpStorm.
 * User: pjbro
 * Date: 2019-01-22
 * Time: 13:36
 */

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;
use Throwable;

/**
 * Class CourseTeamException
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Exception
 */
class CourseTeamAlreadyExistsException extends UserException
{
    public function __construct(Course $course, int $code = 0, Throwable $previous = null)
    {
        $message = Translation::getInstance()->getTranslator()->trans(
            "TeamAlreadyExists",
            ["COURSE_TITLE" => $course->get_title()],
            Manager::class);

        parent::__construct($message, $code, $previous);
    }
}