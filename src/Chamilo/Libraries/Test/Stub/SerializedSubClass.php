<?php

namespace Chamilo\Libraries\Test\Stub;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SerializedSubClass
{
    protected string $myFirstProperty;
    #[SerializedName('my_second_property')]
    protected string $MYSECONDPROPERTY;

    public function getMyFirstProperty(): string
    {
        return $this->myFirstProperty;
    }

    public function setMyFirstProperty(string $myFirstProperty): SerializedSubClass
    {
        $this->myFirstProperty = $myFirstProperty;
        return $this;
    }

    public function getMYSECONDPROPERTY(): string
    {
        return $this->MYSECONDPROPERTY;
    }

    public function setMYSECONDPROPERTY(string $MYSECONDPROPERTY): SerializedSubClass
    {
        $this->MYSECONDPROPERTY = $MYSECONDPROPERTY;
        return $this;
    }
}