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
 * Class CourseTeamNotExistsException
 */
class CourseTeamNotExistsException extends UserException
{
    /**
     * CourseTeamNotExistsException constructor.
     * @param Course $course
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(Course $course, int $code = 0, Throwable $previous = null)
    {
        $message = Translation::getInstance()->getTranslator()->trans(
            "TeamNotExists",
            ["COURSE_TITLE" => $course->get_title()],
            Manager::class);

        parent::__construct($message, $code, $previous);
    }
}