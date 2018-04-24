<?php
namespace Chamilo\Libraries\Format\Validator;

use Chamilo\Libraries\File\Path;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * Builds the Symfony Validator for use with annotaded data-classes
 * More information can be found at the Symfony Validator Component manual:
 *
 * @link http://symfony.com/doc/current/book/validation.html
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Format\Validator
 */
class SymfonyDataClassValidatorFactory
{

    /**
     *
     * @return \Symfony\Component\Validator\ValidatorInterface
     */
    public function createValidator()
    {
        $validator_builder = new ValidatorBuilder();
        $validator_builder->enableAnnotationMapping();
        return $validator_builder->getValidator();
    }
}