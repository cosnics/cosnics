<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Rss\Parser\RssFeedParserFactory;
use Chamilo\Libraries\File\Rss\Parser\SimplePieRssFeedParser;

/**
 * @package Chamilo\Libraries\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FetchRssEntriesComponent extends Manager implements NoAuthenticationSupportInterface
{

    /**
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $url = $this->getRequest()->getFromRequestOrQuery('rss_feed_url');
        $number_entries = $this->getRequest()->getFromRequestOrQuery('number_of_entries');

        /**
         * WARNING! ONLY DO THIS WHEN YOU ARE SURE THAT YOU DON'T NEED TO WRITE TO THE SESSION ANYMORE.
         * THIS FUNCTION MAKES SURE THAT THE SESSION IS NOT BLOCKED WHEN PARSING (INVALID) RSS FEEDS.
         */
        session_write_close();

        $result = new JsonAjaxResult();
        $result->set_properties($this->getSimplePieRssFeedParser()->parse($url, $number_entries));
        $result->display();
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return ['rss_feed_url', 'number_of_entries'];
    }

    protected function getSimplePieRssFeedParser(): SimplePieRssFeedParser
    {
        return $this->getService(SimplePieRssFeedParser::class);
    }
}