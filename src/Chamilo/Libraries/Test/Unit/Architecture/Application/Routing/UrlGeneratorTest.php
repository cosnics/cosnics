<?php

namespace Chamilo\Libraries\Test\Unit\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;

use Symfony\Component\HttpFoundation\ParameterBag;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * Tests the UrlGenerator class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UrlGeneratorTest extends ChamiloTestCase
{
    /**
     * The request mock
     *
     * @var ChamiloRequest | \PHPUnit_Framework_MockObject_MockObject
     */
    private $request_mock;

    /**
     * The url generator
     *
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->request_mock = $this->createMock('Chamilo\Libraries\Platform\ChamiloRequest');

        $parameters = array('course_id' => 1, 'publication_id' => 2);
        $this->request_mock->query = new ParameterBag($parameters);

        $this->urlGenerator = new UrlGenerator($this->request_mock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->request_mock);
    }

    /**
     * Tests the generate url function without additional parameters or filters
     */
    public function test_generate_url()
    {
        $url = $this->urlGenerator->generateURL();
        $this->assertEquals('index.php?course_id=1&publication_id=2', $url);
    }

    /**
     * Tests the generate url with additional parameters
     */
    public function test_generate_url_with_additional_parameters()
    {
        $url = $this->urlGenerator->generateURL(array('tool' => 'document'));
        $this->assertEquals('index.php?course_id=1&publication_id=2&tool=document', $url);
    }

    /**
     * Tests the generate url with replacing parameters (cfr. replace existing parameters by the given parameters)
     */
    public function test_generate_url_with_replacing_parameters()
    {
        $url = $this->urlGenerator->generateURL(array('publication_id' => 3));
        $this->assertEquals('index.php?course_id=1&publication_id=3', $url);
    }

    /**
     * Tests the generate url with filters
     */
    public function test_generate_url_with_filters()
    {
        $url = $this->urlGenerator->generateURL([], array('course_id', 'publication_id'));
        $this->assertEquals('index.php', $url);
    }

    /**
     * Tests the generate context url
     */
    public function test_generate_context_url()
    {
        $url = $this->urlGenerator->generateContextURL('application\countries', 'browse');
        $this->assertEquals(
            'index.php?course_id=1&publication_id=2&application=application\countries&go=browse', $url
        );
    }

    /**
     * Tests that the generate url function does not contain parameters in the url of previous generated urls.
     * The url generation should start from the basic request
     */
    public function test_generate_url_should_not_contain_parameters_of_previous_urls()
    {
        $url1 = $this->urlGenerator->generateURL(array('go' => 'tryout'));

        $url2 = $this->urlGenerator->generateURL([]);
        $this->assertEquals('index.php?course_id=1&publication_id=2', $url2);
    }

}