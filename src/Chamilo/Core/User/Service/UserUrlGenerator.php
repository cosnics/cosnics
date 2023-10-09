<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\DataClassUrlGenerator;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserUrlGenerator
{
    protected DataClassUrlGenerator $dataClassUrlGenerator;

    protected UrlGenerator $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator, DataClassUrlGenerator $dataClassUrlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->dataClassUrlGenerator = $dataClassUrlGenerator;
    }

    public function getApproveUrl(User $user): string
    {
        return $this->getUserActionUrl(
            Manager::ACTION_USER_APPROVER, $user, [Manager::PARAM_CHOICE => Manager::CHOICE_APPROVE]
        );
    }

    public function getChangeUserUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_CHANGE_USER, $user);
    }

    public function getDataClassUrlGenerator(): DataClassUrlGenerator
    {
        return $this->dataClassUrlGenerator;
    }

    public function getDeleteUrl(User $user): string
    {
        return $this->getDataClassUrlGenerator()->getDeleteUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_USER_USER_ID, $user
        );
    }

    public function getDenyUrl(User $user): string
    {
        return $this->getUserActionUrl(
            Manager::ACTION_USER_APPROVER, $user, [Manager::PARAM_CHOICE => Manager::CHOICE_DENY]
        );
    }

    public function getDetailUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_USER_DETAIL, $user);
    }

    public function getEditMetadataUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_MANAGE_METADATA, $user);
    }

    public function getEmailUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_MANAGE_METADATA, $user);
    }

    public function getReportingUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_REPORTING, $user);
    }

    public function getUpdateUrl(User $user): string
    {
        return $this->getDataClassUrlGenerator()->getUpdateUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_USER_USER_ID, $user
        );
    }

    public function getViewQuotaUrl(User $user): string
    {
        return $this->getUserActionUrl(Manager::ACTION_VIEW_QUOTA, $user);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param string[] $additionalParameters
     */
    protected function getUserActionUrl(string $action, User $user, array $additionalParameters = []): string
    {
        return $this->getDataClassUrlGenerator()->getActionUrl(
            Manager::CONTEXT, Application::PARAM_ACTION, Manager::PARAM_USER_USER_ID, $action, $user,
            $additionalParameters
        );
    }
}