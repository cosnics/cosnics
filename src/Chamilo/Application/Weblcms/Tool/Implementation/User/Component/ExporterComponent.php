<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\UserExporter\CourseGroupUserExportExtender;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Domain\UserExportParameters;
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
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Exports the user list
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExporterComponent extends Manager
{

    public function run()
    {
        $userExportParameters = $this->getUserExportParameters();

        $exporter = new UserExporter(
            new ExcelUserExportRenderer(),
            array(
                new CourseUserExportExtender($this->get_course_id()),
                new CourseGroupUserExportExtender($this->get_course_id())
            )
        );

        $file_path = $exporter->export($userExportParameters->getUsers());

        Filesystem::file_send_for_download(
            $file_path,
            true,
            $userExportParameters->getExportFilename(),
            'application/vnd.openxmlformats'
        );

        Filesystem::remove($file_path);
    }

    /**
     * Returns a list of users to export
     *
     * @return UserExportParameters
     */
    protected function getUserExportParameters()
    {
        $tab = $this->getRequest()->get(self::PARAM_TAB);

        switch ($tab)
        {
            case UnsubscribeBrowserComponent::TAB_ALL:
                return $this->exportAllUsers();
            case UnsubscribeBrowserComponent::TAB_USERS:
                return $this->exportIndividualSubscribedUsers();
            case UnsubscribeBrowserComponent::TAB_PLATFORM_GROUPS_SUBGROUPS:
            case UnsubscribeBrowserComponent::TAB_PLATFORM_GROUPS_USERS:
                return $this->exportPlatformGroupUsers();
        }

        return array();
    }

    /**
     * Retrieves all course users
     *
     * @return UserExportParameters
     */
    protected function exportAllUsers()
    {
        $user_records = CourseDataManager::retrieve_all_course_users(
            $this->get_course_id(), null, null, null, array(
                new OrderBy(new StaticConditionVariable('subscription_status', false)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
            )
        );

        $users = array();

        while ($user_record = $user_records->next_result())
        {
            $users[] = DataClass::factory(User::class_name(), $user_record);
        }

        $filename = Translation::getInstance()->getTranslation(
            'ExportUsersFilename', array(
                'COURSE_NAME' => $this->createSafeName($this->get_course()->get_title())
            )
        );

        return new UserExportParameters($users, $filename . '.xlsx');
    }

    /**
     * Retrieves all individually subscribed users
     *
     * @return UserExportParameters
     */
    protected function exportIndividualSubscribedUsers()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticColumnConditionVariable($this->get_course_id())
        );

        $individualUsers = CourseDataManager::retrieve_users_directly_subscribed_to_course(
            $condition, null, null, array(
                new OrderBy(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_STATUS
                    )
                ),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
                new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
            )
        )->as_array();

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

        $filename = Translation::getInstance()->getTranslation(
            'ExportDirectlySubscribedUsersFilename', array(
                'COURSE_NAME' => $this->createSafeName($this->get_course()->get_title())
            )
        );

        return new UserExportParameters($users, $filename . '.xlsx');
    }

    /**
     * Retrieves all users from a given platform group
     *
     * @return UserExportParameters
     * @throws ObjectNotExistException
     */
    protected function exportPlatformGroupUsers()
    {
        $groupTranslation = Translation::getInstance()->getTranslation('Group', null, 'Chamilo\Core\Group');

        $groupId = $this->getRequest()->get(self::PARAM_GROUP);

        if (empty($groupId))
        {
            return $this->exportAllUsers();
        }

        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $groupId);

        if (!$group instanceof Group)
        {
            throw new ObjectNotExistException($groupTranslation, $groupId);
        }

        $groupStatus = $this->determineGroupStatus($group);

        $groupUsersIds = $group->get_users();

        if (empty($groupUsersIds))
        {
            return array();
        }

        $condition =
            new InCondition(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), $groupUsersIds);

        $orderBy = array(
            new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)),
            new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME))
        );

        $groupUsers = \Chamilo\Core\User\Storage\DataManager::retrieves(
            User::class_name(), new DataClassRetrievesParameters(
                $condition, null, null, $orderBy
            )
        )->as_array();

        foreach ($groupUsers as $groupUser)
        {
            $groupUser->set_optional_property(
                CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_STATUS, $groupStatus
            );

            $groupUser->set_optional_property(CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_TYPE, 2);
        }

        $filename = Translation::getInstance()->getTranslation(
            'ExportGroupUsersFilename', array(
                'GROUP_NAME' => $this->createSafeName($group->get_name())
            )
        );

        return new UserExportParameters($groupUsers, $filename . '.xlsx');
    }

    /**
     * Determines the status for a group in the current course
     *
     * @param Group $group
     *
     * @return int
     */
    protected function determineGroupStatus($group)
    {
        $parentIds = array();

        $parents = $group->get_ancestors(true);
        while ($parent = $parents->next_result())
        {
            $parentIds[] = $parent->getId();
        }

        $condition = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class_name(), CourseEntityRelation::PROPERTY_ENTITY_ID),
            $parentIds
        );

        $directlySubscribedGroups = CourseDataManager::retrieve_groups_directly_subscribed_to_course($condition);

        while ($directlySubscribedGroup = $directlySubscribedGroups->next_result())
        {
            if ($directlySubscribedGroup[CourseEntityRelation::PROPERTY_STATUS] == CourseEntityRelation::STATUS_TEACHER)
            {
                return CourseEntityRelation::STATUS_TEACHER;
            }
        }

        return CourseEntityRelation::STATUS_STUDENT;
    }

    /**
     * Creates a safe name from a possible unsafe name
     *
     * @param string $unsafeName
     *
     * @return string
     */
    protected function createSafeName($unsafeName)
    {
        $stringUtilities = StringUtilities::getInstance();
        $string = $stringUtilities->createString($unsafeName);
        $safeName = $string->toLowerCase()->toAscii()->underscored();

        return (string) $safeName;
    }
}
