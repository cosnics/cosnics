<?php
namespace Chamilo\Configuration\Test\Archive;

use Chamilo\Libraries\File\Redirect;

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    const URL = 'http://hostname:12345/path/to/script.php/path/info?arg=value&arg2=value2#anchor';

    private $parsed_url;
    
    /*
     * array(7) { ["scheme"]=> string(4) "http" ["host"]=> string(8) "hostname" ["port"]=> int(12345) ["path"]=>
     * string(29) "/path/to/script.php/path/info" ["query"]=> string(21) "arg=value&arg2=value2" ["fragment"]=>
     * string(6) "anchor" }
     */
    protected function setUp()
    {
        $this->parsed_url =\Chamilo\ParseUrl(self :: URL);
        
        $_SERVER['PHP_SELF'] = $this->parsed_url['path'];
        $_SERVER['QUERY_STRING'] = $this->parsed_url['query'];
        $_SERVER['PATH_INFO'] = '/path/info';
        $_SERVER['HTTP_HOST'] = $this->parsed_url['host'];
        $_SERVER['HTTPS'] = preg_match('/https/i', $this->parsed_url['scheme']);
        $_SERVER['SCRIPT_NAME'] = 'script.php';
        $_SERVER['SERVER_PORT'] = $this->parsed_url['port'];
        $_SERVER['REQUEST_URI'] = $this->parsed_url['path'] . '?' . $this->parsed_url['query'] . '#' .
             $this->parsed_url['fragment'];
    }

    public function test_get_url_should_be_consistent_with_php_self()
    {
        $return_value = Redirect :: get_url();
        $this->assertEquals($_SERVER['PHP_SELF'], $return_value);
    }

    public function test_get_url_filters_parameter_based_on_arguments()
    {
        $parameters = array('greets' => 'Hi', 'bad word' => '******');
        $filters = array('bad word');
        
        $return_value = Redirect :: get_url($parameters, $filters);
        $this->assertEquals($_SERVER['PHP_SELF'] . '?greets=Hi', $return_value);
    }

    public function test_current_url_should_be_exactly_the_same()
    {
        $return_value = Redirect :: current_url();
        $this->assertEquals(self :: URL, $return_value);
    }

    public function test_get_web_link_should_be_the_same()
    {
        $return_value = Redirect :: get_web_link(self :: URL);
        $this->assertEquals(self :: URL, $return_value);
    }

    public function test_get_web_link_add_parameters_to_url()
    {
        $params = array('extraparam1' => 'extraValue1', 'extraparam2' => array('array1', 'array2', 'array3'));
        
        $expected = $params + array('arg' => "value", 'arg2' => "value2");
        $return_value = Redirect :: get_web_link(self :: URL, $params);
        
        $this->assertURLQueryContainsExactly($expected, $return_value);
    }

    public function test_get_link_should_build_application_relative_url()
    {
        $return_value = Redirect :: get_link('customApplication');
        $this->assertEquals('run.php?application=customApplication', $return_value);
    }

    public function test_get_link_should_add_arguments_according_to_params_and_filters()
    {
        $parameters = array('greets' => 'Hi', 'bad word' => '******');
        $filters = array('bad word');
        $return_value = Redirect :: get_link('customApplication', $parameters, $filters);
        
        $expected = array('application' => "customApplication", 'greets' => "Hi");
        
        $this->assertURLQueryContainsExactly($expected, $return_value);
    }

    public function test_get_link_should_call_run_script_when_type_is_application()
    {
        $return_value = Redirect :: get_link('customApplication', array(), array(), false, Redirect :: TYPE_APPLICATION);
        
        $this->assertEquals('run.php?application=customApplication', $return_value);
    }

    public function test_get_link_should_call_core_script_when_type_is_core()
    {
        $return_value = Redirect :: get_link('coreApplication', array(), array(), false, Redirect :: TYPE_CORE);
        $this->assertEquals('core.php?application=coreApplication', $return_value);
    }

    public function test_get_link_should_call_index_script_when_type_is_index()
    {
        $return_value = Redirect :: get_link('coreApplication', array(), array(), false, Redirect :: TYPE_INDEX);
        $this->assertEquals('index.php', $return_value);
    }

    public function test_get_link_should_call_index_script_when_type_is_something_else()
    {
        $return_value = Redirect :: get_link('coreApplication', array(), array(), false, "unknown");
        $this->assertEquals('index.php', $return_value);
    }

    public function test_get_web_link_encode_entities_when_specified()
    {
        $url = "un script nécessitant d'être encodé & including <different> entities";
        
        $unencoded_return_value = Redirect :: get_web_link($url, array(), false);
        $encoded_return_value = Redirect :: get_web_link($url, array(), true);
        
        $this->assertEquals($url, $unencoded_return_value);
        $this->assertEquals($url,\Chamilo\HtmlEntityDecode($encoded_return_value));
    }

    private function assertURLQueryContains(array $expected, $url)
    {
        $parsed_return_value = parse_url(urldecode($return_value));
        parse_str($parsed_return_value['query'], $parsed_return_query);
        
        $not_found = array_diff($expected, $parsed_return_query);
        
        $this->assertEquals(array(), $not_found);
    }

    private function assertURLQueryContainsExactly(array $expected, $url)
    {
        $parsed_return_value = parse_url(urldecode($url));
        parse_str($parsed_return_value['query'], $parsed_return_query);
        
        $this->assertEquals($expected, $parsed_return_query);
    }
}
