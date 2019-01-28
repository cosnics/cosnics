<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WebhookManager
{
    const WEBHOOK_SUBMISSION_COMPLETE = 'SUBMISSION_COMPLETE';
    const WEBHOOK_SIMILARITY_COMPLETE = 'SIMILARITY_COMPLETE';

    /**
     * @var \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    protected $turnitinRepository;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationWriter
     */
    protected $configurationWriter;

    /**
     * WebhookManager constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Configuration\Service\ConfigurationWriter $configurationWriter
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository,
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter,
        \Chamilo\Configuration\Service\ConfigurationWriter $configurationWriter
    )
    {
        $this->turnitinRepository = $turnitinRepository;
        $this->configurationConsulter = $configurationConsulter;
        $this->configurationWriter = $configurationWriter;
    }

    /**
     * @return bool
     */
    public function isWebhookRegistered()
    {
        return !empty($this->getWebhookId());
    }

    /**
     * @throws \Exception
     */
    public function registerWebhook()
    {
        if ($this->isWebhookRegistered())
        {
            throw new \InvalidArgumentException(
                'The given webhook is already registered and can not be registered again'
            );
        }

        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::context(),
                Application::PARAM_ACTION => Manager::ACTION_TURNITIN_WEBHOOK
            ]
        );

        $webhookUrl = $redirect->getUrl();
        $webhookSecret = base64_encode(hash('sha256', uniqid()));

        $createWebhookResponse = $this->turnitinRepository->createWebhook(
            $webhookSecret, $webhookUrl,
            [self::WEBHOOK_SUBMISSION_COMPLETE, self::WEBHOOK_SIMILARITY_COMPLETE],
            'Chamilo Webhook',
            (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
        );

        $webhookId = $createWebhookResponse['id'];
        if (empty($webhookId))
        {
            throw new \RuntimeException(
                sprintf(
                    'The given webhook ID could not be found in the response (%s)', var_export($createWebhookResponse)
                )
            );
        }

        $this->storeWebhookId($webhookId);
        $this->storeWebhookSecret($webhookSecret);
    }

    /**
     * Deletes the webhook
     */
    public function deleteWebhook()
    {
        if(!$this->isWebhookRegistered())
        {
            throw new \InvalidArgumentException(
                'The given webhook is not registered and can not be deleted'
            );
        }

        $webhookId = $this->getWebhookId();
        $this->turnitinRepository->deleteWebhook($webhookId);

        $this->storeWebhookId('');
        $this->storeWebhookSecret('');
    }

    /**
     * @throws \Exception
     */
    public function removeWebhook()
    {
        if (!$this->isWebhookRegistered())
        {
            throw new \InvalidArgumentException(
                'The given webhook is not registered and can not be remove'
            );
        }

        $this->turnitinRepository->deleteWebhook($this->getWebhookId());

        $this->storeWebhookId('');
        $this->storeWebhookSecret('');
    }

    /**
     * @return bool
     */
    public function isConfigurationValid()
    {
        return $this->turnitinRepository->isValidConfig();
    }

    /**
     * @return string
     */
    protected function getWebhookId()
    {
        return $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'turnitin_webhook_id']
        );
    }

    /**
     * @param string $webhookId
     */
    protected function storeWebhookId(string $webhookId = '')
    {
        $this->configurationWriter->writeSetting('Chamilo\Application\Plagiarism', 'turnitin_webhook_id', $webhookId);
    }

    /**
     * @param string $webhookSecret
     */
    protected function storeWebhookSecret(string $webhookSecret)
    {
        $this->configurationWriter->writeSetting(
            'Chamilo\Application\Plagiarism', 'turnitin_webhook_secret', $webhookSecret
        );
    }

}