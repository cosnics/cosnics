<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\DataClassUrlGenerator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
readonly class UserUrlGenerator
{
    public function __construct(protected DataClassUrlGenerator $dataClassUrlGenerator)
    {
    }

    public function getChangeUserUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::LOGIN_AS->value, $user);
    }

    public function getDataClassUrlGenerator(): DataClassUrlGenerator
    {
        return $this->dataClassUrlGenerator;
    }

    public function getDeleteUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::DELETE->value, $user);
    }

    public function getDetailUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::VIEW->value, $user);
    }

    public function getUpdateUrl(User $user): string
    {
        return $this->getUserActionUrl(ActionEnum::UPDATE->value, $user);
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getUserActionUrl(string $action, User $user, array $additionalParameters = []): string
    {
        return $this->dataClassUrlGenerator->getActionUrl(
            Manager::CONTEXT, ApplicationInterface::PARAM_ACTION, Manager::PARAM_USER_ID, $action, $user,
            $additionalParameters
        );
    }
}