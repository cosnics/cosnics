<?php

namespace Chamilo\Libraries\Test\Integration\Format\Validator;

use Symfony\Component\Validator\Constraints AS Assert;

class TestObject
{
    /**
     * @Assert\Range(
     *      min = 120,
     *      max = 180,
     *      minMessage = "ValidatorMinimumRangeMessage",
     *      maxMessage = "ValidatorMaximumRangeMessage",
     *      payload = {"context": "Chamilo\Libraries"}
     * )
     */
    protected $intValue;

    /**
     * TestObject constructor.
     *
     * @param $intValue
     */
    public function __construct($intValue)
    {
        $this->intValue = $intValue;
    }

    /**
     * @return mixed
     */
    public function getIntValue()
    {
        return $this->intValue;
    }

    /**
     * @param mixed $intValue
     */
    public function setIntValue($intValue)
    {
        $this->intValue = $intValue;
    }

}