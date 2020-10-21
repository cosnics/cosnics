<?php
namespace Chamilo\Core\Repository\Service\IncludeParser;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Processor\HtmlEditorProcessor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib.includes
 */
class IncludeImageParser extends IncludeTagParser
{

    /**
     * @param $htmlEditorValue
     *
     * @return \DOMNodeList
     */
    protected function findTags($htmlEditorValue)
    {
        return Text::parse_html_file($htmlEditorValue, 'img');
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    protected function isValidContentObject(ContentObject $contentObject)
    {
        return $contentObject instanceof File && $contentObject->is_image();
    }

    /**
     * @param string $source
     *
     * @return boolean
     */
    protected function isValidSource($source)
    {
        $matches = preg_match(HtmlEditorProcessor::get_repository_document_display_matching_url(), $source);

        return $matches === 1;
    }
}