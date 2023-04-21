<?php
namespace Chamilo\Core\Repository\Service\IncludeParser;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Processor\Ckeditor\Processor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib.includes
 */
class IncludeEmbedParser extends IncludeTagParser
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
        return $contentObject instanceof File &&
            ($contentObject->is_flash() || $contentObject->is_video() || $contentObject->is_audio());
    }

    /**
     * @param string $source
     *
     * @return boolean
     */
    protected function isValidSource($source)
    {
        $matches = preg_match(Processor::get_repository_document_display_matching_url(), $source);

        return $matches === 1;
    }
}
