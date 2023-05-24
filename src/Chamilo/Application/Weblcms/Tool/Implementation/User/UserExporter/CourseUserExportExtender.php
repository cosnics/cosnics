<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\UserExporter;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\UserExporter\UserExportExtender;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * Extends the user exporter with additional data for the user list (subscription type, status)
 *
 * @package application\weblcms\tool\user
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseUserExportExtender implements UserExportExtender
{
    public const EXPORT_COLUMN_PLATFORM_GROUPS = 'platform_groups';
    public const EXPORT_COLUMN_SUBSCRIPTION_STATUS = 'subscription_status';
    public const EXPORT_COLUMN_SUBSCRIPTION_TYPE = 'subscription_type';

    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function export_headers(string $courseIdentifier): array
    {
        $translator = $this->getTranslator();

        $headers = [];

        $headers[self::EXPORT_COLUMN_SUBSCRIPTION_STATUS] =
            $translator->trans('SubscriptionStatus', [], Manager::CONTEXT);
        $headers[self::EXPORT_COLUMN_SUBSCRIPTION_TYPE] = $translator->trans('SubscriptionType', [], Manager::CONTEXT);

        if (count($this->getCoursePlatformGroups($courseIdentifier)) > 0)
        {
            $headers[self::EXPORT_COLUMN_PLATFORM_GROUPS] = $translator->trans('PlatformGroups', [], Manager::CONTEXT);
        }

        return $headers;
    }

    public function export_user(string $courseIdentifier, User $user): array
    {
        $translator = $this->getTranslator();

        $data = [];

        $data[self::EXPORT_COLUMN_SUBSCRIPTION_STATUS] = $user->getOptionalProperty(
            self::EXPORT_COLUMN_SUBSCRIPTION_STATUS
        ) == 1 ? $translator->trans('Teacher', [], Manager::CONTEXT) :
            $translator->trans('Student', [], Manager::CONTEXT);

        $data[self::EXPORT_COLUMN_SUBSCRIPTION_TYPE] = $user->getOptionalProperty(
            self::EXPORT_COLUMN_SUBSCRIPTION_TYPE
        ) == 1 ? $translator->trans('DirectSubscriptions', [], Manager::CONTEXT) : $translator->trans(
            'GroupSubscriptions', [], Manager::CONTEXT
        );

        $coursePlatformGroups = $this->getCoursePlatformGroups($courseIdentifier);

        if (count($coursePlatformGroups) > 0)
        {
            $platform_groups = $this->get_platform_group_names_for_user_in_course($coursePlatformGroups, $user);

            $data[self::EXPORT_COLUMN_PLATFORM_GROUPS] = implode(', ', $platform_groups);
        }

        return $data;
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    protected function getCoursePlatformGroups(string $courseIdentifier): array
    {
        $subscribedPlatformGroups = DataManager::retrieve_all_subscribed_platform_groups([$courseIdentifier]);

        $coursePlatformGroups = [];

        foreach ($subscribedPlatformGroups as $coursePlatformGroup)
        {
            $coursePlatformGroups[$coursePlatformGroup->getId()] = $coursePlatformGroup;
        }

        return $coursePlatformGroups;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Returns the platform group names for a given user that are also subscribed to the current course
     *
     * @param \Chamilo\Core\Group\Storage\DataClass\Group[] $coursePlatformGroups
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    protected function get_platform_group_names_for_user_in_course(array $coursePlatformGroups, User $user): array
    {
        $user_platform_groups_in_course = [];

        $user_subscribed_group_ids = $user->get_groups(true);
        foreach ($user_subscribed_group_ids as $user_subscribed_group_id)
        {
            if (array_key_exists($user_subscribed_group_id, $coursePlatformGroups))
            {
                $user_platform_groups_in_course[] = $coursePlatformGroups[$user_subscribed_group_id]->get_name();
            }
        }

        return $user_platform_groups_in_course;
    }
}