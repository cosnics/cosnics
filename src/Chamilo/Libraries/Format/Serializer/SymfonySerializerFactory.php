<?php
namespace Chamilo\Libraries\Format\Serializer;

use Chamilo\Libraries\Cache\Doctrine\Provider\PhpFileCache;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

class SymfonySerializerFactory
{
    protected ConfigurablePathBuilder $pathBuilder;

    public function __construct(ConfigurablePathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    public function buildSymfonySerializer(): Serializer
    {
        $cachePath = $this->pathBuilder->getCachePath(__NAMESPACE__);

        /**
         * Annotation reader for divergent property settings
         */
        $annotationReader = new CachedReader(new AnnotationReader(), new PhpFileCache($cachePath));
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader($annotationReader));

        /**
         * Default convertor = camelcase to snakecase
         * Additional convertor = annotation based
         */
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter());

        /**
         * Default extractor = based on property type
         * Additional extractor = extraction based on php doc parameters (for use in arrays))
         */
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);

        /**
         * Default normalizer = object normalizer
         * Additional normalizer = normalization for arrays and subtypes
         */
        $normalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter, null, $extractor);

        $serializer = new Serializer(
            [$normalizer, new ArrayDenormalizer()],
            ['json' => new JsonEncoder(), 'xml' => new XmlEncoder()]
        );

        return $serializer;
    }
}