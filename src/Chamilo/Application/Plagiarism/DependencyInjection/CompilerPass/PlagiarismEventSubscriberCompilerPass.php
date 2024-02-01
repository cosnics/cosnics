<?php

namespace Chamilo\Application\Plagiarism\DependencyInjection\CompilerPass;

use Chamilo\Application\Plagiarism\Events\PlagiarismEventDispatcher;
use Chamilo\Application\Plagiarism\Events\PlagiarismEventSubscriber;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PlagiarismEventSubscriberCompilerPass extends TaggedServicesCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $this->addTaggedServicesToService(
            $container, PlagiarismEventDispatcher::class,
            PlagiarismEventSubscriber::class, 'addSubscriber'
        );
    }
}