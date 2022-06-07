<?php
namespace Chamilo\Libraries\Support;

use DOMDocument;

/**
 *
 * @package Chamilo\Libraries\Support
 */
class DebugUtilities
{

    /**
     * @param object|array|string $object
     * @throws \DOMException
     */
    public static function show($object, ?string $title = null, int $backtrace_index = 0): string
    {
        $html = [];

        $html[] = '<div class="panel panel-info">';

        if (isset($title))
        {
            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . $title . '</h3>';
            $html[] = '</div>';
        }

        $html[] = '<div class="panel-body">';

        $calledFrom = debug_backtrace();
        $html[] = '<strong>' . $calledFrom[$backtrace_index]['file'] . '</strong>';
        $html[] = ' (line <strong>' . $calledFrom[$backtrace_index]['line'] . '</strong>)';

        $html[] = ('<pre>');

        if (is_array($object))
        {
            print_r($object);
        }
        elseif (is_a($object, 'DOMDocument'))
        {
            $html[] = 'DOMDocument:<br/><br/>';

            $object->formatOutput = true;
            $xml_string = $object->saveXML();
            $html[] = htmlentities($xml_string);
        }
        elseif (is_a($object, 'DOMNodeList') || is_a($object, 'DOMElement'))
        {
            $dom = new DOMDocument();
            $debugElement = $dom->createElement('debug');
            $dom->appendChild($debugElement);

            if (is_a($object, 'DOMNodeList'))
            {
                $html[] = 'DOMNodeList:<br/><br/>';

                foreach ($object as $node)
                {
                    $node = $dom->importNode($node, true);
                    $debugElement->appendChild($node);
                }
            }
            elseif (is_a($object, 'DOMElement'))
            {
                $html[] = 'DOMElement:<br/><br/>';

                $node = $dom->importNode($object, true);
                $debugElement->appendChild($node);
            }

            $dom->formatOutput = true;
            $xml_string = $dom->saveXML();
            $html[] = htmlentities($xml_string);
        }
        elseif (is_object($object))
        {
            $html[] = print_r($object);
        }
        else
        {
            $html[] = $object;
        }

        $html[] = ('</pre>');
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
