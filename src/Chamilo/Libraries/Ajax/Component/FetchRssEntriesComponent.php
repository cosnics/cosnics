<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\File\Rss\Parser\RssFeedParserFactory;

class FetchRssEntriesComponent extends Manager implements NoAuthenticationSupport
{
    /**
     * Run the AJAX component
     */
    public function run()
    {
        $url = $this->getRequest()->get('rss_feed_url');
        $number_entries = $this->getRequest()->get('number_of_entries');

        $purifier_config = \HTMLPurifier_Config::createDefault();
        $purifier_config->set('Cache.SerializerPath', Path::getInstance()->getCachePath());
        $purifier_config->set('Cache.SerializerPermissions', 06770);

        $feed_parser = RssFeedParserFactory::create(
            new \HTMLPurifier($purifier_config), RssFeedParserFactory::SIMPLE_PIE_FEED_PARSER
        );

        $result = new JsonAjaxResult();
        $result->set_properties($feed_parser->parse($url, $number_entries));
        $result->display();
    }

    /**
     * Get an array of parameters which should be set for this call to work
     *
     * @return array
     */
    public function required_parameters()
    {
        return array('url', 'number_entries');
    }
}