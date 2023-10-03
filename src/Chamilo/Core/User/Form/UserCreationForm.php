<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;

class UserCreationForm extends UserForm
{
    public function __construct(string $action)
    {
        parent::__construct('user_create', $action);
    }

    /**
     * @throws \QuickformException
     */
    public function buildForm(): void
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $requireEmail = (bool) $configurationConsulter->getSetting([Manager::CONTEXT, 'require_email']);
        $requireOfficialCode = (bool) $configurationConsulter->getSetting([Manager::CONTEXT, 'require_official_code']);

        $this->buildPersonalDetailsCategoryForm(true, true, true, true, $requireEmail, true, $requireOfficialCode);
        $this->buildPasswordCategoryForm();
        $this->buildPictureCategoryForm();
        $this->buildAccountCategoryForm();
        $this->buildOtherCategoryForm();
        $this->addSaveResetButtons();
    }

    /**
     * @throws \QuickformException
     */
    public function setDefaults($defaultValues = [], $filter = null)
    {
        $defaultValues[self::PROPERTY_TIME_PERIOD_FOREVER] = 1;

        $defaultValues[User::PROPERTY_EXPIRATION_DATE] = strtotime(
            '+ ' . intval($this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'days_valid'])) . 'Days',
            time()
        );

        $defaultValues[User::PROPERTY_DISK_QUOTA] = '209715200';
        $defaultValues[User::PROPERTY_PLATFORMADMIN] = 0;
        $defaultValues[User::PROPERTY_ACTIVE] = 1;
        $defaultValues[User::PROPERTY_STATUS] = User::STATUS_STUDENT;
        $defaultValues[self::PROPERTY_SEND_MAIL] = 0;
        $defaultValues[self::PROPERTY_GENERATE_PASSWORD] = 1;

        parent::setDefaults($defaultValues);
    }
}
