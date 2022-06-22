<?php

namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\AccountForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountComponent extends ProfileComponent
{

    /**
     *
     * @var \Chamilo\Core\User\Form\AccountForm
     */
    private $form;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageAccount');

        $user = $this->get_user();

        $this->form =
            new AccountForm(AccountForm::TYPE_EDIT, $user, $this->get_url(), $this->getAuthenticationValidator());

        if ($this->form->validate())
        {
            $success = $this->form->update_account();
            if (!$success)
            {
                if (isset($_FILES['picture_uri']) && $_FILES['picture_uri']['error'])
                {
                    $neg_message = 'FileTooBig';
                }
                else
                {
                    $neg_message = 'UserProfileNotUpdated';
                }
            }
            else
            {
                $neg_message = 'UserProfileNotUpdated';
                $pos_message = 'UserProfileUpdated';
            }
            $this->redirectWithMessage(
                Translation::get($success ? $pos_message : $neg_message), !$success,
                array(Application::PARAM_ACTION => self::ACTION_VIEW_ACCOUNT)
            );
        }
        else
        {
            return $this->renderPage();
        }
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        return $this->form->toHtml();
    }
}
