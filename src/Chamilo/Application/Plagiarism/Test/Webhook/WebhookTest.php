<?php

namespace Chamilo\Application\Plagiarism\Test\Webhook;

use Chamilo\Application\Plagiarism\Service\Turnitin\WebhookManager;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\File\Path;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Special integration test to test the webhook in Chamilo. DO NOT RUN THIS TEST AUTOMATICALLY.
 * (Disable the auth check in the webhook manager to make this test work)
 *
 * @package Chamilo\Application\Plagiarism\Test\Webhook
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WebhookTest extends ChamiloTestCase
{
    public function testWebhook()
    {
        $client = new Client();
        $webhookURL =
            'http://branch.local.hogent.be/index.php?go=TurnitinWebhook&application=Chamilo\Application\Plagiarism';

        $request = new Request(
            'POST', $webhookURL,
            ['X-Turnitin-EventType' => WebhookManager::WEBHOOK_SUBMISSION_COMPLETE, 'X-Turnitin-Signature' => 'testkey', 'Content-Type' => 'application/json'],
            json_encode([
                'id' => 'e884f478-9757-41c7-80da-37b94ebb2838',
                'status' => 'COMPLETED'
            ])
        );

        $response = $client->send($request);
        print_r($response->getBody()->getContents());
    }
}