<?php

namespace Chamilo\Libraries\Format\Validator\Constraint;

use Symfony\Component\Validator\Constraints\LengthValidator;

/**
 * @package Chamilo\Libraries\Format\Validator\Constraint
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Length extends \Symfony\Component\Validator\Constraints\Length
{
    public $minMessage = 'ValidatorMinimumLengthMessage';
    public $maxMessage = 'ValidatorMaximumLengthMessage';
    public $payload = ['context' => 'Chamilo\Libraries'];

    public function validatedBy()
    {
        return LengthValidator::class;
    }
}