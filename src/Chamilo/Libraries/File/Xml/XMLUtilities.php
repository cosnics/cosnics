<?php
namespace Chamilo\Libraries\File\Xml;

use DOMDocument;
use DOMXPath;

/**
 * $Id: xml.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.xml
 */
class XMLUtilities
{

    /**
     * Returns the first $subnode occurence of a $node.
     * The subnode is identified by its name.
     * 
     * @param DOMNode $node
     * @param string $subnode_name
     * @return DOMNode
     */
    public static function get_first_element_by_tag_name($node, $subnode_name)
    {
        $nodes = $node->getElementsByTagName($subnode_name);
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
     * @param DOMNode $node
     * @param string $subnode_name
     * @return string
     */
    public static function get_first_element_value_by_tag_name($node, $subnode_name)
    {
        $node = XMLUtilities::get_first_element_by_tag_name($node, $subnode_name);
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
     * @param DOMNode $node
     * @param string $xpath_query
     * @return DOMNode
     */
    public static function get_first_element_by_xpath($node, $xpath_query)
    {
        // $dom = new DOMDocument();
        // $imported_node = $dom->importNode($node, true);
        // $dom->appendChild($imported_node);
        $xpath = new DOMXPath($node->ownerDocument);
        $node_list = $xpath->query($xpath_query);
        
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
     * @param string $xpath_query
     * @return string
     */
    public static function get_first_element_value_by_xpath($node, $xpath_query)
    {
        $node = self::get_first_element_by_xpath($node, $xpath_query);
        
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
     * @param mixed DOMNode or DOMNodeList $node
     * @param string $xpath_query
     * @return DOMNode
     */
    public static function get_first_element_by_relative_xpath($node, $xpath_query)
    {
        $dom = new DOMDocument();
        if (is_a($node, 'DOMNode'))
        {
            $imported_node = $dom->importNode($node, true);
            $dom->appendChild($imported_node);
        }
        elseif (is_a($node, 'DOMNodeList'))
        {
            foreach ($node as $subnode)
            {
                $imported_node = $dom->importNode($subnode, true);
                $dom->appendChild($imported_node);
            }
        }
        
        $xpath = new DOMXPath($dom);
        $node_list = $xpath->query($xpath_query);
        
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
     * The first subnode is searched by using a XPATH query relative to the given $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return string
     */
    public static function get_first_element_value_by_relative_xpath($node, $xpath_query)
    {
        $node = self::get_first_element_by_relative_xpath($node, $xpath_query);
        
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
     * @param DOMNode $node
     * @param string $xpath_query
     * @return array of string
     */
    public static function get_all_values_by_xpath($node, $xpath_query)
    {
        $node_list = self::get_all_element_by_xpath($node, $xpath_query);
        
        $values = array();
        
        if (isset($node_list))
        {
            foreach ($node_list as $node_found)
            {
                $values[] = $node_found->nodeValue;
            }
        }
        
        return $values;
    }

    /**
     * Returns a nodes list under a given node.
     * The subnodes are searched by using a XPATH query relative to the document containing the $node.
     * 
     * @param DOMNode $node
     * @param string $xpath_query
     * @return DOMNodeList
     */
    public static function get_all_element_by_xpath($node, $xpath_query)
    {
        $xpath = new DOMXPath($node->ownerDocument);
        $node_list = $xpath->query($xpath_query);
        
        return $node_list;
    }

    public static function get_all_element_by_relative_xpath($node, $xpath_query)
    {
        $dom = new DOMDocument();
        if (is_a($node, 'DOMNode'))
        {
            $imported_node = $dom->importNode($node, true);
            $dom->appendChild($imported_node);
        }
        elseif (is_a($node, 'DOMNodeList'))
        {
            foreach ($node as $subnode)
            {
                $imported_node = $dom->importNode($subnode, true);
                $dom->appendChild($imported_node);
            }
        }
        
        $xpath = new DOMXPath($dom);
        return $xpath->query($xpath_query);
    }

    /**
     * Get an attribute value, of the default value if the attribute is null or empty
     * 
     * @param DOMNode $node The node to search the attribute on
     * @param string $attribute_name The name of the attribute to get the value from
     * @param string $default_value A default value if the attribute doesn't exist or is empty
     */
    public static function get_attribute($node, $attribute_name, $default_value = null)
    {
        $value = $node->getAttribute($attribute_name);
        
        if (! isset($value) || strlen($value) == 0)
        {
            $value = $default_value;
        }
        
        return $value;
    }

    /**
     * Delete all the nodes from a DOMDocument that are found with the given xpath query
     * 
     * @param DOMDocument $dom_document The DOMDocument from which nodes must be removed
     * @param string $xpath_query
     */
    public static function delete_element_by_xpath($dom_document, $xpath_query)
    {
        $xpath = new DOMXPath($dom_document);
        $node_list = $xpath->query($xpath_query);
        
        if ($node_list->length > 0)
        {
            foreach ($node_list as $node_to_delete)
            {
                if (isset($node_to_delete->parentNode))
                {
                    $node_to_delete->parentNode->removeChild($node_to_delete);
                }
            }
        }
    }
}
