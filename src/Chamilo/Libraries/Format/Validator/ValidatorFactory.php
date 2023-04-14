<?php
namespace Chamilo\Libraries\Format\Validator;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Builds the Symfony Validator for use with annotaded data-classes
 * More information can be found at the Symfony Validator Component manual:
 *
 * @link    http://symfony.com/doc/current/book/validation.html
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Format\Validator
 */
class ValidatorFactory
{

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected bool $devMode;

    protected TranslatorInterface $translator;

    protected ValidatorBuilder $validatorBuilder;

    public function __construct(
        TranslatorInterface $translator, ValidatorBuilder $validatorBuilder,
        ConfigurablePathBuilder $configurablePathBuilder, bool $devMode = false
    )
    {
        $this->translator = $translator;
        $this->validatorBuilder = $validatorBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->devMode = $devMode;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createValidator(): ValidatorDecorator
    {
        $cacheAdapter = $this->devMode ? new ArrayAdapter() : new PhpFilesAdapter(
            md5('Chamilo\Libraries\Format\Validator'), 0, $this->configurablePathBuilder->getCachePath()
        );

        $annotationReader = new PsrCachedReader(new AnnotationReader(), $cacheAdapter);
        $this->validatorBuilder->enableAnnotationMapping($annotationReader);
        $symfonyValidator = $this->validatorBuilder->getValidator();

        return new ValidatorDecorator($symfonyValidator, $this->translator);
    }
}