<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\UserExporter\CourseGroupUserExportExtender;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\UserExporter\CourseUserExportExtender;
use Chamilo\Application\Weblcms\UserExporter\Renderer\ExcelUserExportRenderer;
use Chamilo\Application\Weblcms\UserExporter\UserExporter;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Exports the user list
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExporterComponent extends Manager
{

    public function run()
    {
        $users = $this->getUsersToExport();

        $exporter = new UserExporter(
            new ExcelUserExportRenderer(),
            array(
                new CourseUserExportExtender($this->get_course_id()),
                new CourseGroupUserExportExtender($this->get_course_id())
            )
        );

        $file_path = $exporter->export($users);

        Filesystem::file_send_for_download(
            $file_path,
            true,
            'export_users_' . $this->get_course_id() . '.xlsx',
            'application/vnd.openxmlformats'
        );

        Filesystem::remove($file_path);
    }

    /**
     * Returns a list of users to export
     *
     * @return User[]
     */
    protected function getUsersToExport()
    {
        $tab = $this->getRequest()->get(self::PARAM_TAB);

        switch ($tab)
        {
            case UnsubscribeBrowserComponent::TAB_ALL:
                return $this->retrieveAllUsers();
            case UnsubscribeBrowserComponent::TAB_USERS:
                return $this->retrieveIndividualSubscribedUsers();
            case UnsubscribeBrowserComponent::TAB_PLATFORM_GROUPS_SUBGROUPS:
            case UnsubscribeBrowserComponent::TAB_PLATFORM_GROUPS_USERS:
                return $this->retrievePlatformGroupUsers();
        }

        return array();
    }

    /**
     * Retrieves all course users
     *
     * @return User[]
     */
    protected function retrieveAllUsers()
    {
        $user_records = CourseDataManager::retrieve_all_course_users($this->get_course_id());

        $users = array();

        while ($user_record = $user_records->next_result())
        {
            $users[] = DataClass::factory(User::class_name(), $user_record);
        }

        return $users;
    }

    /**
     * Retrieves all individually subscribed users
     *
     * @return User[]
     */
    protected function retrieveIndividualSubscribedUsers()
    {
        $individualUsers = CourseDataManager::retrieve_users_directly_subscribed_to_course()->as_array();

        $users = array();
        foreach ($individualUsers as $individualUserRecord)
        {
            $individualUserRecordCopy = $individualUserRecord;
            $user = DataClass::factory(User::class_name(), $individualUserRecordCopy);

            $user->set_optional_property(
                CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_STATUS,
                $individualUserRecord[CourseEntityRelation::PROPERTY_STATUS]
            );

            $user->set_optional_property(CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_TYPE, 1);

            $users[] = $user;
        }

        return $users;
    }

    /**
     * Retrieves all users from a given platform group
     */
    protected function retrievePlatformGroupUsers()
    {
        $groupTranslation = Translation::getInstance()->getTranslation('Group', null, 'Chamilo\Core\Group');

        $groupId = $this->getRequest()->get(self::PARAM_GROUP);

        if (empty($groupId))
        {
            return $this->retrieveAllUsers();
        }

        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $groupId);

        if (!$group instanceof Group)
        {
            throw new ObjectNotExistException($groupTranslation, $groupId);
        }

        $groupStatus = $this->getRequest()->get(self::PARAM_STATUS);

        $groupUsersIds = $group->get_users();

        if(empty($groupUsersIds))
        {
            return array();
        }

        $condition =
            new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $groupUsersIds);

        $groupUsers = \Chamilo\Core\User\Storage\DataManager::retrieves(User::class_name(), $condition)->as_array();

        foreach ($groupUsers as $groupUser)
        {
            $groupUser->set_optional_property(
                CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_STATUS, $groupStatus
            );

            $groupUser->set_optional_property(CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_TYPE, 2);
        }

        return $groupUsers;
    }
}
