<?php
namespace Chamilo\Libraries\Support;

use DOMDocument;

class DebugUtilities
{

    public static function show($object, $title = null, $backtrace_index = 0)
    {
        $html = array();

        $html[] = '<div class="debug">';

        $calledFrom = debug_backtrace();
        $html[] = '<strong>' . $calledFrom[$backtrace_index]['file'] . '</strong>';
        $html[] = ' (line <strong>' . $calledFrom[$backtrace_index]['line'] . '</strong>)';

        if (isset($title))
        {
            $html[] = '<h3>' . $title . '</h3>';
        }

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

        return implode("\n", $html);
    }
}
