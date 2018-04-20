<?php
namespace Chamilo\Libraries\Format\Validator;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\CS\DocBlock\Annotation;

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
     * ValidatorFactory constructor.
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Validator\ValidatorBuilder $validatorBuilder
     */
    public function __construct(TranslatorInterface $translator, ValidatorBuilder $validatorBuilder)
    {
        $this->translator = $translator;
        $this->validatorBuilder = $validatorBuilder;
    }

    /**
     * @return \Chamilo\Libraries\Format\Validator\ValidatorDecorator | \Symfony\Component\Validator\Validator\ValidatorInterface
     *
     * @throws \Exception
     */
    public function createValidator()
    {
        $annotationReader = new CachedReader(new AnnotationReader(), new ArrayCache());
        $this->validatorBuilder->enableAnnotationMapping($annotationReader);
        $symfonyValidator = $this->validatorBuilder->getValidator();

        return new ValidatorDecorator($symfonyValidator, $this->translator);
    }
}