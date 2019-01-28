<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Service\Turnitin\EulaService;
use Chamilo\Application\Plagiarism\Service\Turnitin\WebhookManager;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinWebhookManagerComponent extends Manager
{
    const PARAM_REGISTER_WEBHOOK = 'RegisterWebhook';
    const PARAM_DELETE_WEBHOOK = 'DeleteWebhook';

    /**
     * @return string|\Symfony\Component\HttpFoundation\Response
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        if(!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $registerWebhook = $this->getRequest()->getFromUrl(self::PARAM_REGISTER_WEBHOOK);
        if($registerWebhook)
        {
            return $this->registerWebhook();
        }

        $deleteWebhook = $this->getRequest()->getFromUrl(self::PARAM_DELETE_WEBHOOK);
        if($deleteWebhook)
        {
            return $this->deleteWebhook();
        }

        return $this->displayWebhookInfo();
    }

    /**
     * Registers the webhook
     */
    protected function registerWebhook()
    {
        try
        {
            $this->getWebhookManager()->registerWebhook();
            $this->redirect($this->getTranslator()->trans('WebhookRegistered', [], Manager::context()), false);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            $this->redirect($this->getTranslator()->trans('WebhookNotRegistered', [], Manager::context()), true);
        }
    }

    /**
     * Deletes the webhook
     */
    protected function deleteWebhook()
    {
        try
        {
            $this->getWebhookManager()->deleteWebhook();
            $this->redirect($this->getTranslator()->trans('WebhookDeleted', [], Manager::context()), false);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            $this->redirect($this->getTranslator()->trans('WebhookNotDeleted', [], Manager::context()), true);
        }
    }

    /**
     * @return string
     */
    protected function displayWebhookInfo()
    {
        $html = [];

        $html[] = $this->render_header();

        if($this->getWebhookManager()->isWebhookRegistered())
        {
            $html[] = '<div class="alert alert-info">';
            $html[] = $this->getTranslator()->trans('WebhookInstalled', [], Manager::context());
            $html[] = '</div>';

            $deleteWebhookUrl = $this->get_url([self::PARAM_DELETE_WEBHOOK => 1]);

            $html[] = '<div class="text-center">';
            $html[] = '<a href="' . $deleteWebhookUrl . '">';
            $html[] = '<button type="button" class="btn btn-warning">';
            $html[] = $this->getTranslator()->trans('DeleteWebhook', [], Manager::context());
            $html[] = '</button>';
            $html[] = '</a>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="alert alert-danger">';
            $html[] = $this->getTranslator()->trans('WebhookNotInstalled', [], Manager::context());
            $html[] = '</div>';

            if($this->getWebhookManager()->isConfigurationValid())
            {
                $registerWebhookUrl = $this->get_url([self::PARAM_REGISTER_WEBHOOK => 1]);

                $html[] = '<div class="text-center">';
                $html[] = '<a href="' . $registerWebhookUrl . '">';
                $html[] = '<button type="button" class="btn btn-primary">';
                $html[] = $this->getTranslator()->trans('InstallWebhook', [], Manager::context());
                $html[] = '</button>';
                $html[] = '</a>';
                $html[] = '</div>';
            }
            else
            {
                $html[] = '<div class="alert alert-danger">';
                $html[] = $this->getTranslator()->trans('ConfigurationInvalid', [], Manager::context());
                $html[] = '</div>';
            }

        }


        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\Turnitin\WebhookManager
     */
    protected function getWebhookManager()
    {
        return $this->getService(WebhookManager::class);
    }
}