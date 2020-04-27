<?php
namespace Chamilo\Core\Repository\Service\IncludeParser;

use Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib.includes
 */
class IncludeYoutubeParser extends IncludeTagParser
{
    /**
     * @param $htmlEditorValue
     *
     * @return \DOMNodeList
     */
    protected function findTags($htmlEditorValue)
    {
        return Text::parse_html_file($htmlEditorValue, 'embed');
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    protected function isValidContentObject(ContentObject $contentObject)
    {
        return $contentObject instanceof Youtube;
    }

    /**
     * @param string $source
     *
     * @return boolean
     */
    protected function isValidSource($source)
    {
        return stripos($source, 'http://www.youtube.com/v/') !== false;
    }
}
