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
            ['X-Turnitin-EventType' => WebhookManager::WEBHOOK_SIMILARITY_COMPLETE, 'X-Turnitin-Signature' => 'test', 'Content-Type' => 'application/json'],
            '{"overall_match_percentage":100,"status":"COMPLETE","time_generated":"2019-02-05T08:34:03.602Z","time_requested":"2019-02-05T08:33:51.302Z","submission_id":"e884f478-9757-41c7-80da-37b94ebb2838","top_source_largest_matched_word_count":182}'
        );

        $response = $client->send($request);
        print_r($response->getBody()->getContents());
    }
}