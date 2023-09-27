<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\RegisterForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Exception;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\User\Component
 */
class RegisterComponent extends Manager implements NoAuthenticationSupportInterface
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $configurationConsulter = $this->getConfigurationConsulter();
        $translator = $this->getTranslator();

        $allowRegistration = (bool) $configurationConsulter->getSetting([Manager::CONTEXT, 'allow_registration']);

        //        if (!$allowRegistration)
        //        {
        //            throw new NotAllowedException();
        //        }

        $form = new RegisterForm(
            $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_REGISTER_USER
                ]
            )
        );

        if ($form->validate())
        {
            try
            {
                $formValues = $form->exportValues();

                $password = $formValues['pw']['pass'] == '1' ? $this->getPasswordGenerator()->generatePassword() :
                    $formValues['pw'][User::PROPERTY_PASSWORD];

                // TODO: Continue implementing this

                $user = $this->getUserService()->createUserFromParameters(
                    $formValues[User::PROPERTY_FIRSTNAME], $formValues[User::PROPERTY_LASTNAME],
                    $formValues[User::PROPERTY_USERNAME], $formValues[User::PROPERTY_OFFICIAL_CODE],
                    $formValues[User::PROPERTY_EMAIL], $password, 'Platform', $formValues[User::PROPERTY_STATUS],
                    (bool) $formValues['send_mail']
                );

                $code = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'days_valid']);

                if ($code == 0)
                {
                    $user->set_active(1);
                }
                else
                {
                    $user->set_activation_date(time());
                    $user->set_expiration_date(strtotime('+' . $code . ' days', time()));
                }

                if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_registration']) == 2)
                {
                    $user->set_approved(0);
                    $user->set_active(0);

                    return $user->create();
                }

                $parameters = [];

                if ($configurationConsulter->getSetting([Manager::CONTEXT, 'allow_registration']) == 2)
                {
                    $parameters['message'] = $translator->trans('UserAwaitingApproval', [], Manager::CONTEXT);
                }

                $parameters[Application::PARAM_CONTEXT] = '';

                return new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
            }
            catch (Exception $exception)
            {
                $this->getRequest()->request->set(
                    'error_message', $translator->trans('UsernameNotAvailable', [], Manager::CONTEXT)
                );

                $html = [];

                $html[] = $this->renderHeader();
                $html[] = $form->render();
                $html[] = $this->renderFooter();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $form->render();
            $html[] = $this->renderFooter();

            return implode(PHP_EOL, $html);
        }
    }

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->getService(PasswordGeneratorInterface::class);
    }
}
