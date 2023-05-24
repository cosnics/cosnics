<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\UserExporter;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\UserExporter\UserExportExtender;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * Extends the user exporter with additional data for the course groups
 *
 * @package application\weblcms\tool\course_group
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseGroupUserExportExtender implements UserExportExtender
{
    public const EXPORT_COLUMN_COURSE_GROUPS = 'course_groups';

    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function export_headers(string $courseIdentifier): array
    {
        $headers = [];

        $headers[self::EXPORT_COLUMN_COURSE_GROUPS] =
            $this->getTranslator()->trans('CourseGroups', [], Manager::CONTEXT);

        return $headers;
    }

    public function export_user(string $courseIdentifier, User $user): array
    {
        $data = [];

        $course_groups = DataManager::retrieve_course_groups_from_user($user->getId(), $courseIdentifier);

        $course_groups_subscribed = [];
        foreach ($course_groups as $course_group)
        {
            $course_groups_subscribed[] = $course_group->get_name();
        }

        $data[self::EXPORT_COLUMN_COURSE_GROUPS] = implode(', ', $course_groups_subscribed);

        return $data;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}