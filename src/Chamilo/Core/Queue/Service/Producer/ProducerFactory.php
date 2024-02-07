<?php
namespace Chamilo\Core\Queue\Service\Producer;

use Enqueue\Dbal\DbalContext;
use Enqueue\Pheanstalk\PheanstalkContext;
use Interop\Queue\Context;
use RuntimeException;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProducerFactory
{
    /**
     * @var \Interop\Queue\Context | PheanstalkContext | DbalContext
     */
    protected $psrContext;

    /**
     * ProducerFactory constructor.
     *
     * @param \Interop\Queue\Context $psrContext
     */
    public function __construct(Context $psrContext)
    {
        $this->psrContext = $psrContext;
    }

    /**
     * @return \Chamilo\Core\Queue\Service\Producer\ProducerInterface
     */
    public function createProducer()
    {
        $contextClass = get_class($this->psrContext);
        switch ($contextClass)
        {
            case PheanstalkContext::class:
                return new BeanstalkProducer($this->psrContext);
            case DbalContext::class:
                return new DBALProducer($this->psrContext);
        }

        throw new RuntimeException('Could not find a valid producer for context ' . $contextClass);
    }
}