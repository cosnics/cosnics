<?php

namespace Chamilo\Libraries\Test\Stub;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SerializedClass
{
    protected string $myFirstProperty;
    #[SerializedName('my_second_property')]
    protected string $MYSECONDPROPERTY;
    protected SerializedSubClass $mySingleSubclass;

    /**
     * @var SerializedSubClass[]
     */
    protected array $myMultiSubclasses;

    public function getMyFirstProperty(): string
    {
        return $this->myFirstProperty;
    }

    public function setMyFirstProperty(string $myFirstProperty): SerializedClass
    {
        $this->myFirstProperty = $myFirstProperty;
        return $this;
    }

    public function getMYSECONDPROPERTY(): string
    {
        return $this->MYSECONDPROPERTY;
    }

    public function setMYSECONDPROPERTY(string $MYSECONDPROPERTY): SerializedClass
    {
        $this->MYSECONDPROPERTY = $MYSECONDPROPERTY;
        return $this;
    }

    public function getMySingleSubclass(): SerializedSubClass
    {
        return $this->mySingleSubclass;
    }

    public function setMySingleSubclass(SerializedSubClass $mySingleSubclass): SerializedClass
    {
        $this->mySingleSubclass = $mySingleSubclass;
        return $this;
    }

    public function getMyMultiSubclasses(): array
    {
        return $this->myMultiSubclasses;
    }

    public function setMyMultiSubclasses(array $myMultiSubclasses): SerializedClass
    {
        $this->myMultiSubclasses = $myMultiSubclasses;
        return $this;
    }
}