<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Test\Unit\Lib\Core;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Core\DependencyContainer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Test;

/**
 * This test case test the dependency container class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Tom Goethals - Hogeschool Gent
 */
class DependencyContainerTest extends Test
{

    /**
     * **************************************************************************************************************
     * Test functionality *
     * **************************************************************************************************************
     */

    /**
     * Tests if an object dependency is properly validated.
     */
    public function test_check_valid_dependency()
    {
        $this->invoke_check_valid_dependency($this);
        $this->assertTrue(true);
    }

    /**
     * Tests if a class name dependency is properly validated.
     */
    public function test_check_valid_dependency_with_class_name()
    {
        $this->invoke_check_valid_dependency('application\weblcms\tool\ephorus\DependencyContainer');
        $this->assertTrue(true);
    }

    /**
     * Tests that the check valid dependency function throws an invalid argument exception when there is an invalid
     * dependency.
     * @expectedException \InvalidArgumentException
     */
    public function test_check_valid_dependency_with_invalid_dependency()
    {
        $this->invoke_check_valid_dependency('InexistingClass2012');
    }

    /**
     * Tests that the check valid dependency function throws an invalid argument exception when there is an empty
     * dependency.
     * @expectedException \InvalidArgumentException
     */
    public function test_check_valid_dependency_with_empty_dependency()
    {
        $this->invoke_check_valid_dependency(null);
    }

    /**
     * Tests that a dependency can be added
     */
    public function test_add_dependency()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->add('dependency_container_test', $this);
        $value = $dependency_container->getDependencies();

        $this->assertEquals($this, $value['dependency_container_test']);
    }

    /**
     * Tests that a dependency can be added with a class name
     */
    public function test_add_dependency_with_class_name()
    {
        $classname = get_class($this);

        $dependency_container = new DependencyContainer();
        $dependency_container->add('dependency_container_test', $classname);
        $value = $dependency_container->getDependencies();

        $this->assertEquals($classname, $value['dependency_container_test']);
    }

    /**
     * Tests that the add dependency function throws an invalid argument exception when there is an empty dependency
     * name.
     * @expectedException \InvalidArgumentException
     */
    public function test_add_dependency_with_empty_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->add('', $this);
    }

    /**
     * Tests that the add dependency function throws an invalid argument exception when there is a dependency added with
     * an already existing dependency name
     * @expectedException \InvalidArgumentException
     */
    public function test_add_dependency_with_same_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->add('dependency_container_test', $this);
        $dependency_container->add('dependency_container_test', $this);
    }

    /**
     * Tests that a dependency can be deleted
     */
    public function test_delete_dependency()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->add('dependency_container_test', $this);
        $dependency_container->delete('dependency_container_test');

        $value = $dependency_container->getDependencies();

        $this->assertEmpty($value['dependency_container_test']);
    }

    /**
     * Tests that the delete dependency function only deletes the request dependency
     */
    public function test_delete_dependency_with_multiple_dependencies_in_container()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->add('dependency_container_test', $this);
        $dependency_container->add('dependency_container_test2', $this);
        $dependency_container->delete('dependency_container_test');

        $value = $dependency_container->getDependencies();

        $this->assertEquals($this, $value['dependency_container_test2']);
    }

    /**
     * Tests that the delete dependency function throws an invalid argument exception when there is an empty dependency
     * name.
     * @expectedException \InvalidArgumentException
     */
    public function test_delete_dependency_with_empty_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->delete('');
    }

    /**
     * Tests that the delete dependency function throws an invalid argument exception when there is an inexisting
     * dependency name.
     * @expectedException \InvalidArgumentException
     */
    public function test_delete_dependency_with_inexisting_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->delete('dependency_container_test');
    }

    /**
     * Tests that a dependency can be replaced.
     */
    public function test_replace_dependency()
    {
        $dependency_container = new DependencyContainer();
        $request = new Request();
        $result = new Result();
        $dependency_container->add('dependency_container_test', $result);
        $dependency_container->replace('dependency_container_test', $request);

        $value = $dependency_container->getDependencies();

        $this->assertEquals($request, $value['dependency_container_test']);
    }

    /**
     * Tests that the replace dependency function throws an invalid argument exception when there is an empty dependency
     * name.
     * @expectedException \InvalidArgumentException
     */
    public function test_replace_dependency_with_empty_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->replace('', $this);
    }

    /**
     * Tests that the replace dependency function throws an invalid argument exception when there is an inexisting
     * dependency name.
     * @expectedException \InvalidArgumentException
     */
    public function test_replace_dependency_with_inexisting_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->replace('dependency_container_test', $this);
    }

    /**
     * Tests that a dependency can be retrieved.
     */
    public function test_get_dependency()
    {
        $result = new Result();
        $dependency_container = new DependencyContainer();
        $dependency_container->add('dependency_container_test', $result);

        $get_result = $dependency_container->get('dependency_container_test');
        $this->assertEquals($result, $get_result);
    }

    /**
     * Tests that the get dependency function throws an invalid argument exception when there is an empty dependency
     * name.
     * @expectedException \InvalidArgumentException
     */
    public function test_get_dependency_with_empty_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->get('');
    }

    /**
     * Tests that the get dependency function throws an invalid argument exception when there is an inexisting
     * dependency name.
     * @expectedException \InvalidArgumentException
     */
    public function test_get_dependency_with_inexisting_name()
    {
        $dependency_container = new DependencyContainer();
        $dependency_container->get('dependency_container_test');
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */

    /**
     * Invokes the check_valid_dependency method on a dependency container with a value.
     *
     * @param mixed $value
     */
    protected function invoke_check_valid_dependency($value)
    {
        $container = new DependencyContainer();

        $method = $this->get_method(get_class($container), 'check_valid_dependency');
        $method->invoke($container, $value);
    }
}
