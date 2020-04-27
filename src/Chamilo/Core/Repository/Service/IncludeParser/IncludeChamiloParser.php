<?php
namespace Chamilo\Core\Repository\Service\IncludeParser;

use Chamilo\Core\Repository\Service\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use DOMDocument;
use DOMNode;
use DOMXPath;

class IncludeChamiloParser extends ContentObjectIncludeParser
{

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $htmlEditorValue
     */
    protected function parseHtmlEditorValue(ContentObject $contentObject, string $htmlEditorValue)
    {
        $domDocument = new DOMDocument();
        $domDocument->loadHTML($htmlEditorValue);

        if ($domDocument->firstChild instanceof DOMNode)
        {
            $domDocument->removeChild($domDocument->firstChild);
            $dom_xpath = new DOMXPath($domDocument);

            $resources = $dom_xpath->query('//resource');
            foreach ($resources as $resource)
            {
                $source = $resource->getAttribute('source');
                $contentObject->include_content_object($source);
            }

            //select all elements with the data-co-id attribute
            $placeholders = $dom_xpath->query('//*[@data-co-id]');

            foreach ($placeholders as $placeholder)
            {
                /**
                 * @var \DOMNode $placeholder
                 */
                $contentObjectId = $placeholder->getAttribute('data-co-id');
                $contentObject->include_content_object($contentObjectId);
            }
        }
    }

}
