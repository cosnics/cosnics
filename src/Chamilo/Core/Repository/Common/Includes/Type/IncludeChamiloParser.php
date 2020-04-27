<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Libraries\Format\Form\FormValidator;
use DOMDocument;
use DOMNode;
use DOMXPath;

class IncludeChamiloParser extends ContentObjectIncludeParser
{

    public function parseHtmlEditorField()
    {
        $values = $this->getValues();
        $contentObject = $this->getContentObject();

        $htmlEditors = $values[FormValidator::PROPERTY_HTML_EDITORS];

        foreach ($htmlEditors as $htmlEditor)
        {
            $html_editor_parts = explode('[', $htmlEditor);

            $value = $values;
            foreach ($html_editor_parts as $html_editor_part)
            {
                $part = str_replace(']', '', $html_editor_part);
                $value = $value[$part];
            }

            if (!empty($value))
            {
                $domDocument = new DOMDocument();
                $domDocument->loadHTML($value);

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

                    $placeholders =
                        $dom_xpath->query('//*[@data-co-id]'); //select all elements with the data-co-id attribute
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
    }
}
