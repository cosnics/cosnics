<?php
namespace Chamilo\Libraries\File\Xml;

use DOMDocument;
use DOMXPath;

/**
 *
 * @package Chamilo\Libraries\File\Xml
 */
class XMLUtilities
{

    /**
     * Returns the first $subnode occurence of a $node.
     * The subnode is identified by its name.
     *
     * @param \DOMNode $node
     * @param string $subnodeName
     * @return \DOMNode
     */
    public static function get_first_element_by_tag_name($node, $subnodeName)
    {
        $nodes = $node->getElementsByTagName($subnodeName);

        if ($nodes->length > 0)
        {
            return $nodes->item(0);
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the first $subnode occurence value of a $node
     * The subnode is identified by its name.
     *
     * @param \DOMNode $node
     * @param string $subnodeName
     * @return string
     */
    public static function get_first_element_value_by_tag_name($node, $subnodeName)
    {
        $node = XMLUtilities::get_first_element_by_tag_name($node, $subnodeName);

        if (isset($node))
        {
            return $node->nodeValue;
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the first element found by the XPATH query.
     * The first subnode is searched by using a XPATH query relative to the document containing the $node.
     *
     * @param \DOMNode $node
     * @param string $xpathQuery
     * @return \DOMNode
     */
    public static function get_first_element_by_xpath($node, $xpathQuery)
    {
        // $dom = new DOMDocument();
        // $imported_node = $dom->importNode($node, true);
        // $dom->appendChild($imported_node);
        $xpath = new DOMXPath($node->ownerDocument);
        $node_list = $xpath->query($xpathQuery);

        if ($node_list->length > 0)
        {
            return $node_list->item(0);
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the value of the first element found by the XPATH query.
     * The first subnode is searched by using a XPATH query relative to the document containing the $node.
     *
     * @param DOMNode $node
     * @param string $xpathQuery
     * @return string
     */
    public static function get_first_element_value_by_xpath($node, $xpathQuery)
    {
        $node = self::get_first_element_by_xpath($node, $xpathQuery);

        if (isset($node))
        {
            return $node->nodeValue;
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the first element found by the XPATH query.
     * The first subnode is searched by using a XPATH query relative to the given $node.
     * NOTE: this function can not be used to retrieve a node that needs to be updated
     * as the returned node is a copy of the original one, and thus is not the same object reference
     *
     * @param \DOMNode|\DOMNodeList $node
     * @param string $xpathQuery
     * @return DOMNode
     */
    public static function get_first_element_by_relative_xpath($node, $xpathQuery)
    {
        $dom = new DOMDocument();

        if (is_a($node, 'DOMNode'))
        {
            $importedNode = $dom->importNode($node, true);
            $dom->appendChild($importedNode);
        }
        elseif (is_a($node, 'DOMNodeList'))
        {
            foreach ($node as $subnode)
            {
                $importedNode = $dom->importNode($subnode, true);
                $dom->appendChild($importedNode);
            }
        }

        $xpath = new DOMXPath($dom);
        $nodeList = $xpath->query($xpathQuery);

        if ($nodeList->length > 0)
        {
            return $nodeList->item(0);
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns the value of the first element found by the XPATH query.
     * The first subnode is searched by using a XPATH query relative to the given $node.
     *
     * @param \DOMNode $node
     * @param string $xpath_query
     * @return string
     */
    public static function get_first_element_value_by_relative_xpath($node, $xpathQuery)
    {
        $node = self::get_first_element_by_relative_xpath($node, $xpathQuery);

        if (isset($node))
        {
            return $node->nodeValue;
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns all the values of a list of nodes under a given node.
     * The subnodes are searched by using a XPATH query relative to the document containing the $node.
     *
     * @param \DOMNode $node
     * @param string $xpathQuery
     * @return string[]
     */
    public static function get_all_values_by_xpath($node, $xpathQuery)
    {
        $nodeList = self::get_all_element_by_xpath($node, $xpathQuery);

        $values = array();

        if (isset($nodeList))
        {
            foreach ($nodeList as $nodeFound)
            {
                $values[] = $nodeFound->nodeValue;
            }
        }

        return $values;
    }

    /**
     * Returns a nodes list under a given node.
     * The subnodes are searched by using a XPATH query relative to the document containing the $node.
     *
     * @param \DOMNode $node
     * @param string $xpathQuery
     * @return \DOMNodeList
     */
    public static function get_all_element_by_xpath($node, $xpathQuery)
    {
        $xpath = new DOMXPath($node->ownerDocument);
        return $xpath->query($xpathQuery);
    }

    /**
     *
     * @param \DOMNode|\DOMNodeList $node
     * @param string $xpathQuery
     * @return \DOMNodeList
     */
    public static function get_all_element_by_relative_xpath($node, $xpathQuery)
    {
        $dom = new DOMDocument();

        if (is_a($node, 'DOMNode'))
        {
            $importedNode = $dom->importNode($node, true);
            $dom->appendChild($importedNode);
        }
        elseif (is_a($node, 'DOMNodeList'))
        {
            foreach ($node as $subnode)
            {
                $importedNode = $dom->importNode($subnode, true);
                $dom->appendChild($importedNode);
            }
        }

        $xpath = new DOMXPath($dom);
        return $xpath->query($xpathQuery);
    }

    /**
     * Get an attribute value, of the default value if the attribute is null or empty
     *
     * @param \DOMNode $node The node to search the attribute on
     * @param string $attributeName The name of the attribute to get the value from
     * @param string $defaultValue A default value if the attribute doesn't exist or is empty
     * @return string
     */
    public static function get_attribute($node, $attributeName, $defaultValue = null)
    {
        $value = $node->getAttribute($attributeName);

        if (! isset($value) || strlen($value) == 0)
        {
            $value = $defaultValue;
        }

        return $value;
    }

    /**
     * Delete all the nodes from a DOMDocument that are found with the given xpath query
     *
     * @param \DOMDocument $domDocument The DOMDocument from which nodes must be removed
     * @param string $xpathQuery
     */
    public static function delete_element_by_xpath($domDocument, $xpathQuery)
    {
        $xpath = new DOMXPath($domDocument);
        $nodeList = $xpath->query($xpathQuery);

        if ($nodeList->length > 0)
        {
            foreach ($nodeList as $nodeToDelete)
            {
                if (isset($nodeToDelete->parentNode))
                {
                    $nodeToDelete->parentNode->removeChild($nodeToDelete);
                }
            }
        }
    }
}
