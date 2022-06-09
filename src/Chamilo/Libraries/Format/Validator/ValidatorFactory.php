<?php
namespace Chamilo\Libraries\Format\Validator;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Builds the Symfony Validator for use with annotaded data-classes
 * More information can be found at the Symfony Validator Component manual:
 *
 * @link http://symfony.com/doc/current/book/validation.html
 * @author Sven Vanpoucke - Hogeschool Gent
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

    public function createValidator(): ValidatorDecorator
    {
        $cachePath = $this->configurablePathBuilder->getCachePath(__NAMESPACE__);
        $cache = $this->devMode ? new ArrayCache() : new PhpFileCache($cachePath);

        $annotationReader = new CachedReader(new AnnotationReader(), $cache);
        $this->validatorBuilder->enableAnnotationMapping($annotationReader);
        $symfonyValidator = $this->validatorBuilder->getValidator();

        return new ValidatorDecorator($symfonyValidator, $this->translator);
    }
}