<?php
namespace Chamilo\Configuration\Test\Archive;

use Chamilo\Libraries\Platform\Session\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    public function test_get_should_return_existing_value()
    {
        $_GET['key'] = "expected";
        $returnValue = Request::get('key');
        $this->assertEquals("expected", $returnValue);
    }

    public function test_get_with_undefined_key_should_return_null()
    {
        unset($_GET['key']);
        $returnValue = Request::get('key');
        $this->assertNull($returnValue);
    }

    public function test_get_should_return_value_after_set_get()
    {
        Request::set_get('key', 'expected');
        $returnValue = Request::get('key');
        $this->assertEquals("expected", $returnValue);
    }

    public function test_set_get_should_change_value_when_called_multiple_times()
    {
        Request::set_get('key', 'firstValue');
        Request::set_get('key', 'secondValue');
        $returnValue = Request::get('key');
        $this->assertEquals("secondValue", $returnValue);
    }

    public function test_post_should_return_existing_value()
    {
        $_POST['key'] = "expected";
        $returnValue = Request::post('key');
        $this->assertEquals("expected", $returnValue);
    }

    public function test_post_with_undefined_key_should_return_null()
    {
        unset($_POST['key']);
        $returnValue = Request::post('key');
        $this->assertNull($returnValue);
    }

    public function test_post_and_get_are_separated()
    {
        $_POST['key'] = "post";
        $_GET['key'] = "get";
        $this->assertEquals("post", Request::post('key'));
        $this->assertEquals("get", Request::get('key'));
    }

    public function test_file_should_return_existing_value()
    {
        $_FILES['key'] = "expected";
        $returnValue = Request::file('key');
        $this->assertEquals("expected", $returnValue);
    }

    public function test_env_should_return_existing_value()
    {
        $_ENV['key'] = "expected";
        $returnValue = Request::environment('key');
        $this->assertEquals("expected", $returnValue);
    }

    public function test_server_should_return_existing_value()
    {
        $_SERVER['key'] = "expected";
        $returnValue = Request::server('key');
        $this->assertEquals("expected", $returnValue);
    }
}
