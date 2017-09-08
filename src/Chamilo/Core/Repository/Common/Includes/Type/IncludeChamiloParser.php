<?php
namespace Chamilo\Core\Repository\Common\Includes\Type;

use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use DOMDocument;
use DOMXPath;

class IncludeChamiloParser extends ContentObjectIncludeParser
{

    public function parse_editor()
    {
        $form = $this->get_form();
        $form_type = $form->get_form_type();
        $values = $form->exportValues();
        $content_object = $form->get_content_object();
        
        $html_editors = $form->get_html_editors();
        
        foreach ($html_editors as $html_editor)
        {
            $field_name = 'values';
            
            $html_editor_parts = explode('[', $html_editor);
            
            $value = $values;
            foreach ($html_editor_parts as $html_editor_part)
            {
                $part = str_replace(']', '', $html_editor_part);
                $value = $value[$part];
            }
            
            if (! empty($value))
            {
                $dom_document = new DOMDocument();
                $dom_document->loadHTML($value);
                
                if ($dom_document->firstChild instanceof \DOMNode)
                {
                    $dom_document->removeChild($dom_document->firstChild);
                    $dom_xpath = new DOMXPath($dom_document);
                    
                    $resources = $dom_xpath->query('//resource');
                    foreach ($resources as $resource)
                    {
                        $source = $resource->getAttribute('source');
                        $content_object->include_content_object($source);
                    }

                    $placeholders = $dom_xpath->query('//*[@data-co-id]'); //select all elements with the data-co-id attribute
                    foreach($placeholders as $placeholder)
                    {
                        /**
                         * @var \DOMNode $placeholder
                         */
                        $contentObjectId = $placeholder->getAttribute('data-co-id');
                        $content_object->include_content_object($contentObjectId);
                    }
                }
            }
        }
    }
}
