<?php

namespace Chamilo\Libraries\Format\Validator;

use Chamilo\Libraries\File\Path;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * Builds the Symfony Validator for use with annotaded data-classes
 *
 * More information can be found at the Symfony Validator Component manual:
 * @link http://symfony.com/doc/current/book/validation.html
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SymfonyDataClassValidatorFactory
{
    /**
     * Initializes the validator
     */
    public function createValidator()
    {
        $vendorPath = Path::getInstance()->getVendorPath();

        AnnotationRegistry::registerAutoloadNamespaces(
            array(
                'Symfony\Component\Validator\Constraints' => $vendorPath . 'symfony/validator'
            )
        );

        $validator_builder = new ValidatorBuilder();
        $validator_builder->enableAnnotationMapping();
        return $validator_builder->getValidator();
    }
}