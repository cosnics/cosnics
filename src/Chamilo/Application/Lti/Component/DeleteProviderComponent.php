<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Form\ProviderFormType;
use Chamilo\Application\Lti\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Lti\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleteProviderComponent extends \Chamilo\Application\Lti\Manager
{

    /**
     * @return string
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageProviders');

        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $provider = $this->getProviderFromRequest();

        try
        {
            $this->getProviderService()->deleteProvider($provider);
            $message = 'ProviderDeleted';
            $success = true;
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            $message = 'ProviderNotDeleted';
            $success = false;
        }

        $this->redirect(
            $this->getTranslator()->trans($message, [], Manager::context()), !$success,
            [self::PARAM_ACTION => self::ACTION_MANAGE_PROVIDERS], [self::PARAM_PROVIDER_ID]
        );

        return;
    }

    public function get_additional_parameters()
    {
        return [self::PARAM_PROVIDER_ID];
    }
}