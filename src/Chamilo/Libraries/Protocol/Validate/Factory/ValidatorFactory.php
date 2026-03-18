<?php
namespace Chamilo\Libraries\Protocol\Validate\Factory;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package Chamilo\Libraries\Protocol\Validate\Factory
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ValidatorFactory
{
    protected Translator $translator;

    public function createValidator(): ValidatorInterface
    {
        return Validation::createValidator();
    }
}