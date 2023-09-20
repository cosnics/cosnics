<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Domain\UserExportParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\UserExporter\CourseUserExportExtender;
use Chamilo\Application\Weblcms\UserExporter\UserExporter;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
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

        $file_path = $this->getUserExporter()->export($this->get_course_id(), $userExportParameters->getUsers());

        $this->getFilesystemTools()->sendFileForDownload(
            $file_path, $userExportParameters->getExportFilename(), 'application/vnd.openxmlformats'
        );

        $this->getFilesystem()->remove($file_path);
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

    /**
     * Determines the status for a group in the current course
     *
     * @param Group $group
     *
     * @return int
     */
    protected function determineGroupStatus($group)
    {
        $parentIds = [];

        $parents = $group->get_ancestors(true);
        foreach ($parents as $parent)
        {
            $parentIds[] = $parent->getId();
        }

        $condition = new InCondition(
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_ENTITY_ID),
            $parentIds
        );

        $directlySubscribedGroups = CourseDataManager::retrieve_groups_directly_subscribed_to_course($condition);

        foreach ($directlySubscribedGroups as $directlySubscribedGroup)
        {
            if ($directlySubscribedGroup[CourseEntityRelation::PROPERTY_STATUS] == CourseEntityRelation::STATUS_TEACHER)
            {
                return CourseEntityRelation::STATUS_TEACHER;
            }
        }

        return CourseEntityRelation::STATUS_STUDENT;
    }

    /**
     * Retrieves all course users
     *
     * @return UserExportParameters
     */
    protected function exportAllUsers()
    {
        $user_records = CourseDataManager::retrieve_all_course_users(
            $this->get_course_id(), null, null, null, new OrderBy([
                new OrderProperty(new StaticConditionVariable('subscription_status', false)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
            ])
        );

        $users = [];

        foreach ($user_records as $user_record)
        {
            $users[] = DataClass::factory(User::class, $user_record);
        }

        $filename = Translation::getInstance()->getTranslation(
            'ExportUsersFilename', [
                'COURSE_NAME' => $this->createSafeName($this->get_course()->get_title())
            ]
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
            new PropertyConditionVariable(CourseEntityRelation::class, CourseEntityRelation::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id(), false)
        );

        $individualUsers = CourseDataManager::retrieve_users_directly_subscribed_to_course(
            $condition, null, null, new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable(
                        CourseEntityRelation::class, CourseEntityRelation::PROPERTY_STATUS
                    )
                ),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
            ])
        );

        $users = [];
        foreach ($individualUsers as $individualUserRecord)
        {
            $individualUserRecordCopy = $individualUserRecord;
            $user = DataClass::factory(User::class, $individualUserRecordCopy);

            $user->setOptionalProperty(
                CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_STATUS,
                $individualUserRecord[CourseEntityRelation::PROPERTY_STATUS]
            );

            $user->setOptionalProperty(CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_TYPE, 1);

            $users[] = $user;
        }

        $filename = Translation::getInstance()->getTranslation(
            'ExportDirectlySubscribedUsersFilename', [
                'COURSE_NAME' => $this->createSafeName($this->get_course()->get_title())
            ]
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

        $groupId = $this->getRequest()->getFromRequestOrQuery(self::PARAM_GROUP);

        if (empty($groupId))
        {
            return $this->exportAllUsers();
        }

        $group = $this->getGroupService()->findGroupByIdentifier($groupId);

        $groupStatus = $this->determineGroupStatus($group);

        $groupUsersIds = $group->get_users();

        if (empty($groupUsersIds))
        {
            $groupUsers = [];
        }
        else
        {
            $condition = new InCondition(new PropertyConditionVariable(User::class, User::PROPERTY_ID), $groupUsersIds);

            $orderBy = [
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME)),
                new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME))
            ];

            $groupUsers = DataManager::retrieves(
                User::class, new DataClassRetrievesParameters(
                    $condition, null, null, new OrderBy($orderBy)
                )
            );

            foreach ($groupUsers as $groupUser)
            {
                $groupUser->setOptionalProperty(
                    CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_STATUS, $groupStatus
                );

                $groupUser->setOptionalProperty(CourseUserExportExtender::EXPORT_COLUMN_SUBSCRIPTION_TYPE, 2);
            }
        }

        $filename = Translation::getInstance()->getTranslation(
            'ExportGroupUsersFilename', [
                'GROUP_NAME' => $this->createSafeName($group->get_name())
            ]
        );

        return new UserExportParameters($groupUsers, $filename . '.xlsx');
    }

    /**
     * Returns a list of users to export
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\User\Domain\UserExportParameters
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function getUserExportParameters()
    {
        $tab = $this->getRequest()->getFromRequestOrQuery(self::PARAM_TAB);

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

        throw new UserException(
            $this->getTranslator()->trans('ExportTypeNotFound', null, 'Chamilo\Application\Weblcms')
        );
    }

    protected function getUserExporter(): UserExporter
    {
        return $this->getService('Chamilo\Application\Weblcms\Tool\Implementation\User\UserExporter');
    }
}
