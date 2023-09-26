<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\AccountForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountComponent extends ProfileComponent
{

    private AccountForm $accountForm;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageAccount');

        $accountForm = $this->getAccountForm();

        if ($accountForm->validate())
        {
            $success = $accountForm->update_account();

            if (!$success)
            {
                if (isset($_FILES['picture_uri']) && $_FILES['picture_uri']['error'])
                {
                    $errorMessage = 'FileTooBig';
                }
                else
                {
                    $errorMessage = 'UserProfileNotUpdated';
                }
            }
            else
            {
                $errorMessage = 'UserProfileNotUpdated';
                $successMessage = 'UserProfileUpdated';
            }

            $this->redirectWithMessage(
                $this->getTranslator()->trans($success ? $successMessage : $errorMessage), !$success,
                [Application::PARAM_ACTION => self::ACTION_VIEW_ACCOUNT]
            );
        }
        else
        {
            return $this->renderPage();
        }
    }

    public function getAccountForm(): AccountForm
    {
        if (!isset($this->accountForm))
        {
            $this->accountForm = new AccountForm(
                AccountForm::TYPE_EDIT, $this->getUser(), $this->get_url(), $this->getAuthenticationValidator()
            );
        }

        return $this->accountForm;
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        return $this->getAccountForm()->render();
    }
}
