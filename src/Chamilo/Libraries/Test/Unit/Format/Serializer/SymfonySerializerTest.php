<?php
namespace Chamilo\Libraries\Test\Unit\Format\Serializer;

use Chamilo\Libraries\Architecture\Test\TestCases\DependencyInjectionBasedTestCase;
use Chamilo\Libraries\Format\Serializer\SymfonySerializerFactory;
use Chamilo\Libraries\Test\Stub\SerializedClass;
use Chamilo\Libraries\Test\Stub\SerializedSubClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class SymfonySerializerTest extends DependencyInjectionBasedTestCase
{
    protected Serializer $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = $this->getService(Serializer::class);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void
    {
        unset($this->serializer);
        parent::tearDown();
    }

    public function testSerializer()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $json = $this->serializer->serialize($class, 'json');

        $this->assertEquals('{"my_first_property":"hello","my_second_property":"world"}', $json);
    }

    public function testSerializerWithSubclass()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $subClass = new SerializedSubClass();
        $subClass->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $class->setMySingleSubclass($subClass);

        $json = $this->serializer->serialize($class, 'json');

        $this->assertEquals('{"my_first_property":"hello","my_second_property":"world","my_single_subclass":{"my_first_property":"hello","my_second_property":"world"}}', $json);
    }

    public function testSerializerWithMultiSubclasses()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $subClass = new SerializedSubClass();
        $subClass->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $subClass2 = new SerializedSubClass();
        $subClass2->setMyFirstProperty('hello2')
            ->setMYSECONDPROPERTY('world2');

        $class->setMyMultiSubclasses([$subClass, $subClass2]);

        $json = $this->serializer->serialize($class, 'json');

        $this->assertEquals('{"my_first_property":"hello","my_second_property":"world","my_multi_subclasses":[{"my_first_property":"hello","my_second_property":"world"},{"my_first_property":"hello2","my_second_property":"world2"}]}', $json);
    }

    public function testDeserialize()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $object = $this->serializer->deserialize('{"my_first_property":"hello","my_second_property":"world"}', SerializedClass::class, 'json');

        $this->assertEquals($class, $object);
    }

    public function testDeserializeArray()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $object = $this->serializer->deserialize('[{"my_first_property":"hello","my_second_property":"world"}]', SerializedClass::class . '[]', 'json');

        $this->assertEquals([$class], $object);
    }

    public function testDeserializeWithSubClass()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $subClass = new SerializedSubClass();
        $subClass->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $class->setMySingleSubclass($subClass);

        $object = $this->serializer->deserialize('{"my_first_property":"hello","my_second_property":"world","my_single_subclass":{"my_first_property":"hello","my_second_property":"world"}}', SerializedClass::class, 'json');

        $this->assertEquals($class, $object);
    }

    public function testDeserializeWithMultiSubClasses()
    {
        $class = new SerializedClass();
        $class->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $subClass = new SerializedSubClass();
        $subClass->setMyFirstProperty('hello')
            ->setMYSECONDPROPERTY('world');

        $subClass2 = new SerializedSubClass();
        $subClass2->setMyFirstProperty('hello2')
            ->setMYSECONDPROPERTY('world2');

        $class->setMyMultiSubclasses([$subClass, $subClass2]);

        $object = $this->serializer->deserialize('{"my_first_property":"hello","my_second_property":"world","my_multi_subclasses":[{"my_first_property":"hello","my_second_property":"world"},{"my_first_property":"hello2","my_second_property":"world2"}]}', SerializedClass::class, 'json');

        $this->assertEquals($class, $object);
    }

    public function testDecode()
    {
        $data = [
            'my_first_property' => 'hello',
            'my_second_property' => 'world'
        ];

        $object = $this->serializer->decode('{"my_first_property":"hello","my_second_property":"world"}', 'json');

        $this->assertEquals($data, $object);
    }


}