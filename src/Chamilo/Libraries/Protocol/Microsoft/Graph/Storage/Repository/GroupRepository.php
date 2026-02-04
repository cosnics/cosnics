<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\GroupNotExistsException;
use Exception;
use Microsoft\Graph\Generated\Models\Group;
use Microsoft\Graph\Generated\Models\PlannerPlan;
use Microsoft\Graph\Generated\Models\ReferenceCreate;
use Microsoft\Graph\Generated\Models\User;
use Microsoft\Graph\GraphServiceClient;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @TODO Fix class
 */
class GroupRepository
{
    protected GraphServiceClient $graphServiceClient;

    protected string $platformPrefix;

    public function __construct(GraphServiceClient $graphServiceClient, string $platformPrefix = '')
    {
        $this->platformPrefix = $platformPrefix;
        $this->graphServiceClient = $graphServiceClient;
    }

    /**
     * @throws \Exception
     */
    public function createGroup(string $groupName): Group
    {
        try {
            $group = new Group();
            $group->setDescription($groupName);
            $group->setDisplayName($groupName);
            $group->setMailEnabled(false);
            $group->setMailNickname(
                str_replace('-', '_', $this->platformPrefix . Uuid::v4())
            );
            $group->setGroupTypes(['Unified']);
            $group->setSecurityEnabled(false);
            $group->setVisibility('Private');

            $createdGroup = $this->getGraphServiceClient()->groups()->post($group)->wait();

            if (!$createdGroup instanceof Group) {
                throw new Exception('Group (' . $groupName . ') not created');
            }

            return $createdGroup;
        }
        catch (Exception) {
            throw new Exception('Group (' . $groupName . ') not created');
        }
    }

    /**
     * @throws \Exception
     */
    public function createPlanForGroup(string $groupIdentifier, string $planName): ?PlannerPlan
    {
        try {
            $plan = new PlannerPlan();
            $plan->setOwner($groupIdentifier);
            $plan->setTitle($planName);

            $plannerPlan = $this->getGraphServiceClient()->planner()->plans()->post($plan)->wait();

            if (!$plannerPlan instanceof PlannerPlan) {
                throw new Exception('Plan (' . $planName . ') not be created for group (' . $groupIdentifier . ')');
            }

            return $plannerPlan;
        }
        catch (Exception) {
            throw new Exception('Plan (' . $planName . ') not be created for group (' . $groupIdentifier . ')');
        }
    }

    public function getGraphServiceClient(): GraphServiceClient
    {
        return $this->graphServiceClient;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\GroupNotExistsException
     */
    public function getGroup(string $groupIdentifier): Group
    {
        try {
            $group = $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->get()->wait();

            if (!$group instanceof Group) {
                throw new GroupNotExistsException('Group not found: ' . $groupIdentifier);
            }

            return $group;
        }
        catch (Exception) {
            throw new GroupNotExistsException('Group not found: ' . $groupIdentifier);
        }
    }

    /**
     * @throws \Exception
     */
    public function getGroupMember(string $groupIdentifier, string $azureUserIdentifier): User
    {
        try {
            $user =
                $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->members()->byDirectoryObjectId(
                    $azureUserIdentifier
                )->graphUser()->get()->wait();

            if (!$user instanceof User) {
                throw new Exception('Group member not found: ' . $groupIdentifier);
            }

            return $user;
        }
        catch (Exception) {
            throw new Exception('Group member not found: ' . $groupIdentifier);
        }
    }

    /**
     * @throws \Exception
     */
    public function getGroupOwner(string $groupIdentifier, string $azureUserIdentifier): User
    {
        try {
            $user =
                $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->owners()->byDirectoryObjectId(
                    $azureUserIdentifier
                )->graphUser()->get()->wait();

            if (!$user instanceof User) {
                throw new Exception('Group owner not found: ' . $groupIdentifier);
            }

            return $user;
        }
        catch (Exception) {
            throw new Exception('Group owner not found: ' . $groupIdentifier);
        }
    }

    public function getPlatformPrefix(): string
    {
        return $this->platformPrefix;
    }

    /**
     * @return array<\Microsoft\Graph\Generated\Models\User>
     * @throws \Exception
     */
    public function listGroupMembers(string $groupIdentifier): array
    {
        try {
            $groupMembers =
                $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->members()->graphUser()->get()
                    ->wait()->getValue();

            if (!is_array($groupMembers)) {
                throw new Exception('Group members not found: ' . $groupIdentifier);
            }

            return $groupMembers;
        }
        catch (Exception) {
            throw new Exception('Group members not found: ' . $groupIdentifier);
        }
    }

    /**
     * @return array<\Microsoft\Graph\Generated\Models\User>
     * @throws \Exception
     */
    public function listGroupOwners(string $groupIdentifier): array
    {
        try {
            $groupOwners =
                $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->owners()->graphUser()->get()
                    ->wait()->getValue();

            if (!is_array($groupOwners)) {
                throw new Exception('Group owners not found: ' . $groupIdentifier);
            }

            return $groupOwners;
        }
        catch (Exception) {
            throw new Exception('Group owners not found: ' . $groupIdentifier);
        }
    }

    /**
     * @return array<\Microsoft\Graph\Generated\Models\PlannerPlan>
     * @throws \Exception
     */
    public function listGroupPlans(string $groupIdentifier): array
    {
        try {
            $plannerPlans =
                $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->planner()->plans()->get()->wait()
                    ->getValue();

            if (!is_array($plannerPlans)) {
                throw new Exception('Group plans not found: ' . $groupIdentifier);
            }

            return $plannerPlans;
        }
        catch (Exception) {
            throw new Exception('Group plans not found: ' . $groupIdentifier);
        }
    }

    public function removeMemberFromGroup(string $groupIdentifier, string $azureUserIdentifier): bool
    {
        try {
            $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->members()->byDirectoryObjectId(
                $azureUserIdentifier
            )->ref()->delete();

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    public function removeOwnerFromGroup(string $groupIdentifier, string $azureUserIdentifier): bool
    {
        try {
            $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->owners()->byDirectoryObjectId(
                $azureUserIdentifier
            )->ref()->delete();

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    public function subscribeMemberInGroup(string $groupIdentifier, string $azureUserIdentifier): bool
    {
        try {
            $reference = new ReferenceCreate();
            $reference->setOdataId(
                'https://graph.microsoft.com/v1.0/users/' . $azureUserIdentifier
            );

            $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->members()->ref()->post($reference)
                ->wait();

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    public function subscribeOwnerInGroup(string $groupIdentifier, string $azureUserIdentifier): bool
    {
        try {
            $reference = new ReferenceCreate();
            $reference->setOdataId(
                'https://graph.microsoft.com/v1.0/users/' . $azureUserIdentifier
            );

            $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->owners()->ref()->post($reference)
                ->wait();

            return true;
        }
        catch (Exception) {
            return false;
        }
    }

    public function updateGroup(string $groupIdentifier, string $groupName): bool
    {
        try {
            $group = new Group();
            $group->setDescription($groupName);
            $group->setDisplayName($groupName);

            $this->getGraphServiceClient()->groups()->byGroupId($groupIdentifier)->patch($group);

            return true;
        }
        catch (Exception) {
            return false;
        }
    }
}