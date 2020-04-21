<?php
namespace Chamilo\Libraries\Format\Validator;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PhpFileCache;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

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
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var ValidatorBuilder
     */
    protected $validatorBuilder;

    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     * @var bool
     */
    protected $devMode;

    /**
     * ValidatorFactory constructor.
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Validator\ValidatorBuilder $validatorBuilder
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param bool $devMode
     */
    public function __construct(
        TranslatorInterface $translator, ValidatorBuilder $validatorBuilder,
        ConfigurablePathBuilder $configurablePathBuilder, $devMode = false
    )
    {
        $this->translator = $translator;
        $this->validatorBuilder = $validatorBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->devMode = $devMode;
    }

    /**
     * @return \Chamilo\Libraries\Format\Validator\ValidatorDecorator |
     *     \Symfony\Component\Validator\Validator\ValidatorInterface
     *
     * @throws \Exception
     */
    public function createValidator()
    {
        $cachePath = $this->configurablePathBuilder->getCachePath(__NAMESPACE__);
        $cache = $this->devMode ? new ArrayCache() : new PhpFileCache($cachePath);

        $annotationReader = new CachedReader(new AnnotationReader(), $cache);
        $this->validatorBuilder->enableAnnotationMapping($annotationReader);
        $symfonyValidator = $this->validatorBuilder->getValidator();

        return new ValidatorDecorator($symfonyValidator, $this->translator);
    }
}