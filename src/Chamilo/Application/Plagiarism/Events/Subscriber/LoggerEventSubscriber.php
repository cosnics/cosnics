<?php

namespace Chamilo\Application\Plagiarism\Events\Subscriber;

use Chamilo\Application\Plagiarism\Events\Event\StrikePlagiarismScanRequestedEvent;
use Chamilo\Application\Plagiarism\Events\Event\StrikePlagiarismWebhookCalledEvent;
use Chamilo\Application\Plagiarism\Events\PlagiarismEventSubscriber;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Elastic\Monolog\Formatter\ElasticCommonSchemaFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerEventSubscriber extends PlagiarismEventSubscriber
{
    protected Logger $logger;

    public function __construct(ConfigurablePathBuilder $pathBuilder)
    {
        $this->logger = new Logger('Plagiarism');

        $handler = new StreamHandler($pathBuilder->getLogPath() . 'Plagiarism/plagiarism_events.log');
        $handler->setFormatter(new ElasticCommonSchemaFormatter());

        $this->logger->pushHandler($handler);
    }

    public static function getSubscribedEvents()
    {
        return [
            StrikePlagiarismScanRequestedEvent::class => 'onStrikePlagiarismScanRequested',
            StrikePlagiarismWebhookCalledEvent::class => 'onStrikePlagiarismWebhookCalled',
        ];
    }

    public function onStrikePlagiarismScanRequested(StrikePlagiarismScanRequestedEvent $event)
    {
        $params = $event->getRequestParameters();

        $this->logger->info(
            'New strike plagiarism scan requested',
            [
                'labels' => [
                    'document_id' => $params->getId(),
                    'callback' => $params->getCallback(),
                    'author' => $params->getAuthor(),
                    'submitter' => $params->getCoordinator(),
                    'submitter_id' => $params->getUserId()
                ]
            ]
        );
    }

    public function onStrikePlagiarismWebhookCalled(StrikePlagiarismWebhookCalledEvent $event)
    {
        $this->logger->info(
            'Strike plagiarism webhook called',
            [
                'labels' => [
                    'document_id' => $event->getDocumentId(),
                    'signature' => $event->getSignature(),
                    'url' => $event->getCallbackUrl()
                ]
            ]
        );
    }
}