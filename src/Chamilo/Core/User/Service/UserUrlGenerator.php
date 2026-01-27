<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\DataClassUrlGenerator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserUrlGenerator
{
    protected DataClassUrlGenerator $dataClassUrlGenerator;

    public function __construct(DataClassUrlGenerator $dataClassUrlGenerator)
    {
        $this->dataClassUrlGenerator = $dataClassUrlGenerator;
    }

    public function getChangeUserUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_LOGIN_AS, $user);
    }

    public function getDataClassUrlGenerator(): DataClassUrlGenerator
    {
        return $this->dataClassUrlGenerator;
    }

    public function getDeleteUrl(User $user): string
    {
        return $this->getDataClassUrlGenerator()->getDeleteUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_USER_ID, $user
        );
    }

    public function getDetailUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_VIEW, $user);
    }

    public function getEmailUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_EMAIL, $user);
    }

    public function getUpdateUrl(User $user): string
    {
        return $this->getDataClassUrlGenerator()->getUpdateUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_USER_ID, $user
        );
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getUserActionUrl(string $action, User $user, array $additionalParameters = []): string
    {
        return $this->getDataClassUrlGenerator()->getActionUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_USER_ID, $action, $user,
            $additionalParameters
        );
    }
}