<?php
namespace Chamilo\Core\Repository\Service\IncludeParser;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Processor\Ckeditor\Processor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use DOMDocument;

/**
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
        $document = new DOMDocument();
        $document->loadHTML($htmlEditorValue);

        return $document->getElementsByTagname('img');
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    protected function isValidContentObject(ContentObject $contentObject)
    {
        return $contentObject instanceof File && $contentObject->is_image();
    }

    /**
     * @param string $source
     *
     * @return bool
     */
    protected function isValidSource($source)
    {
        $matches = preg_match(Processor::get_repository_document_display_matching_url(), $source);

        return $matches === 1;
    }
}
