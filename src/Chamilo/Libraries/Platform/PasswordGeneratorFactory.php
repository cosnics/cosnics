<?php
namespace Chamilo\Libraries\Platform;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

/**
 * @package Chamilo\Libraries\Platform
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PasswordGeneratorFactory
{

    public function createPasswordGenerator(): ComputerPasswordGenerator
    {
        $passwordGenerator = new ComputerPasswordGenerator();

        $passwordGenerator->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true);
        $passwordGenerator->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true);
        $passwordGenerator->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true);
        $passwordGenerator->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false);
        $passwordGenerator->setOptionValue(ComputerPasswordGenerator::OPTION_LENGTH, 8);
        $passwordGenerator->setOptionValue(ComputerPasswordGenerator::OPTION_AVOID_SIMILAR, true);

        return $passwordGenerator;
    }
}