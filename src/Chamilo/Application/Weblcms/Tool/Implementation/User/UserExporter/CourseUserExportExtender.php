<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\UserExporter;

use Chamilo\Application\Weblcms\UserExporter\UserExportExtender;
use Chamilo\Core\Group\Service\GroupSubscriptionService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extends the user exporter with additional data for the user list (subscription type, status)
 * 
 * @package application\weblcms\tool\user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseUserExportExtender implements UserExportExtender
{
    const EXPORT_COLUMN_SUBSCRIPTION_STATUS = 'subscription_status';
    const EXPORT_COLUMN_SUBSCRIPTION_TYPE = 'subscription_type';
    const EXPORT_COLUMN_PLATFORM_GROUPS = 'platform_groups';
    /**
     * @var GroupSubscriptionService
     */
    protected $groupSubscriptionService;

    /**
     * The platform groups that are subscribed to the course, inclusively the subgroups
     * 
     * @var Group[int]
     */
    private $course_platform_groups;

    /**
     * The constructor
     *
     * @param int $course_id
     * @param GroupSubscriptionService $groupSubscriptionService
     */
    public function __construct($course_id, GroupSubscriptionService $groupSubscriptionService)
    {
        $course_platform_groups = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_subscribed_platform_groups(
            array($course_id));
        
        $this->course_platform_groups = array();
        
        while ($course_platform_group = $course_platform_groups->next_result())
        {
            $this->course_platform_groups[$course_platform_group->get_id()] = $course_platform_group;
        }

        $this->groupSubscriptionService = $groupSubscriptionService;
    }

    /**
     * Exports additional headers
     * 
     * @return array
     */
    public function export_headers()
    {
        $headers = array();
        
        $headers[self::EXPORT_COLUMN_SUBSCRIPTION_STATUS] = Translation::get('SubscriptionStatus');
        $headers[self::EXPORT_COLUMN_SUBSCRIPTION_TYPE] = Translation::get('SubscriptionType');
        
        if (count($this->course_platform_groups) > 0)
        {
            $headers[self::EXPORT_COLUMN_PLATFORM_GROUPS] = Translation::get('PlatformGroups');
        }
        
        return $headers;
    }

    /**
     * Exports additional data for a given user
     * 
     * @param User $user
     *
     * @return array
     */
    public function export_user(User $user)
    {
        $data = array();
        
        $data[self::EXPORT_COLUMN_SUBSCRIPTION_STATUS] = $user->get_optional_property(
            self::EXPORT_COLUMN_SUBSCRIPTION_STATUS) == 1 ? Translation::get('Teacher') : Translation::get('Student');
        
        $data[self::EXPORT_COLUMN_SUBSCRIPTION_TYPE] = $user->get_optional_property(
            self::EXPORT_COLUMN_SUBSCRIPTION_TYPE) == 1 ? Translation::get('DirectSubscriptions') : Translation::get(
            'GroupSubscriptions');
        
        if (count($this->course_platform_groups) > 0)
        {
            $platform_groups = $this->get_platform_group_names_for_user_in_course($user);
            
            $data[self::EXPORT_COLUMN_PLATFORM_GROUPS] = implode(", ", $platform_groups);
        }
        
        return $data;
    }

    /**
     * Returns the platform group names for a given user that are also subscribed to the current course
     * 
     * @param User $user
     *
     * @return Group[]
     */
    protected function get_platform_group_names_for_user_in_course(User $user)
    {
        $user_platform_groups_in_course = array();
        
        $user_subscribed_group_ids = $this->groupSubscriptionService->findAllGroupIdsForUser($user);
        foreach ($user_subscribed_group_ids as $user_subscribed_group_id)
        {
            if (array_key_exists($user_subscribed_group_id, $this->course_platform_groups))
            {
                $user_platform_groups_in_course[] = $this->course_platform_groups[$user_subscribed_group_id]->get_name();
            }
        }
        
        return $user_platform_groups_in_course;
    }
}