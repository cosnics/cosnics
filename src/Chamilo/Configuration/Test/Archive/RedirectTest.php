<?php
namespace Chamilo\Configuration\Test\Archive;

use Chamilo\Libraries\File\Redirect;
use PHPUnit_Framework_TestCase;
use function Chamilo\HtmlEntityDecode;
use function Chamilo\ParseUrl;

class RedirectTest extends PHPUnit_Framework_TestCase
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
        $this->parsed_url = ParseUrl(self::URL);
        
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
        $redirect = new Redirect();
        $return_value = $redirect->getUrl();
        $this->assertEquals($_SERVER['PHP_SELF'], $return_value);
    }

    public function test_get_url_filters_parameter_based_on_arguments()
    {
        $parameters = array('greets' => 'Hi', 'bad word' => '******');
        $filters = array('bad word');
        
        $redirect = new Redirect($parameters, $filters);
        $return_value = $redirect->getUrl();
        $this->assertEquals($_SERVER['PHP_SELF'] . '?greets=Hi', $return_value);
    }

    public function test_current_url_should_be_exactly_the_same()
    {
        $redirect = new Redirect();
        $return_value = $redirect->getCurrentUrl();
        $this->assertEquals(self::URL, $return_value);
    }

    public function test_get_web_link_add_parameters_to_url()
    {
        $params = array('extraparam1' => 'extraValue1', 'extraparam2' => array('array1', 'array2', 'array3'));
        
        $expected = $params + array('arg' => "value", 'arg2' => "value2");
        
        $redirect = new Redirect($params);
        $return_value = $redirect->getUrl();
        
        $this->assertURLQueryContainsExactly($expected, $return_value);
    }

    public function test_get_link_should_build_application_relative_url()
    {
        $redirect = new Redirect(array('application' => 'customApplication'));
        $return_value = $redirect->getUrl();
        $this->assertEquals('run.php?application=customApplication', $return_value);
    }

    public function test_get_link_should_add_arguments_according_to_params_and_filters()
    {
        $parameters = array('greets' => 'Hi', 'bad word' => '******');
        $filters = array('bad word');
        
        $redirect = new Redirect($parameters, $filters);
        $return_value = $redirect->getUrl();
        
        $expected = array('application' => "customApplication", 'greets' => "Hi");
        
        $this->assertURLQueryContainsExactly($expected, $return_value);
    }

    public function test_get_web_link_encode_entities_when_specified()
    {
        $params = array('test' => "un script nécessitant d'être encodé & including <different> entities");
        
        $redirect = new Redirect($params, [], false);
        $unencoded_return_value = $redirect->getUrl();
        
        $redirect = new Redirect($params, [], true);
        $encoded_return_value = $redirect->getUrl();
        
        $this->assertEquals($url, $unencoded_return_value);
        $this->assertEquals($url, HtmlEntityDecode($encoded_return_value));
    }

    private function assertURLQueryContains(array $expected, $url)
    {
        $parsed_return_value = parse_url(urldecode($return_value));
        parse_str($parsed_return_value['query'], $parsed_return_query);
        
        $not_found = array_diff($expected, $parsed_return_query);
        
        $this->assertEquals([], $not_found);
    }

    private function assertURLQueryContainsExactly(array $expected, $url)
    {
        $parsed_return_value = parse_url(urldecode($url));
        parse_str($parsed_return_value['query'], $parsed_return_query);
        
        $this->assertEquals($expected, $parsed_return_query);
    }
}
