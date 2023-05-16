<?php
namespace Chamilo\Libraries\Test\Integration\Format\Validator;

use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Test\Integration\Format\Validator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ValidatorTest extends DependencyInjectionBasedTestCase
{
    /**
     * Tests that the validator translates the validation message and adds the parameters
     */
    public function testValidatorTranslation()
    {
        $this->getTranslator()->setLocale('en');

        $testObject = new TestObject(5);
        $validator = $this->getValidator();

        $errorMessage = $validator->validate($testObject)->get(0)->getMessage();
        $this->assertEquals("The given value of 5 is less than the minimum allowed value of 120", $errorMessage);
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected function getValidator()
    {
        return $this->getService('Symfony\Component\Validator\Validator');
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->getService(Translator::class);
    }
}