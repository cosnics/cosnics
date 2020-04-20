<?php
namespace Chamilo\Libraries\Format\Serializer;

use Chamilo\Libraries\File\Path;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;

/**
 * Factory Class for the Serializer
 *
 * @package Chamilo\Libraries\Format\Serializer
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SerializerFactory
{

    /**
     * The serializer builder
     *
     * @var \JMS\Serializer\SerializerBuilder
     */
    protected $serializerBuilder;

    /**
     * SerializerFactory constructor.
     *
     * @param \JMS\Serializer\SerializerBuilder $serializerBuilder
     */
    public function __construct(SerializerBuilder $serializerBuilder)
    {
        $this->serializerBuilder = $serializerBuilder;
    }

    /**
     *
     * @return \JMS\Serializer\Serializer
     */
    public function createSerializer()
    {
        $vendorPath = Path::getInstance()->getVendorPath();

        AnnotationRegistry::registerAutoloadNamespaces(
            array('JMS\Serializer\Annotation' => $vendorPath . 'jms/serializer/src')
        );

        return $this->serializerBuilder->build();
    }
}