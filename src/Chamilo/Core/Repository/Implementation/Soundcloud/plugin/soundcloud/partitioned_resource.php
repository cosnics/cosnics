<?php
/**
 * Extension of array to allow linked partitioning.
 * 
 * @package Soundcloud
 * @author Thor <thor@soundcloud.com>
 * @link http://github.com/mptre/php-soundcloud
 */
class PartitionedResource extends ArrayObject
{

    public function __construct($string)
    {
        $data = $this->parse_to_array($string);
        
        parent :: __construct($data);
    }

    public function get_next_partition($soundcloud)
    {
        $next_partition_url = $this['@attributes']['next-partition-href'];
        
        if ($next_partition_url != '')
        {
            preg_match("/([a-z]+)\?/", $next_partition_url, $matches);
            $method = $matches[0];
            
            preg_match("/(\?)(.+)/", $next_partition_url, $matches);
            $param = $matches[2];
            
            $string = $soundcloud->request($method . $param);
            
            return new PartitionedResource($string);
        }
    }
    
    // / Turns the string that the main PHP API returns into an array that works with our resource.
    private function parse_to_array($string)
    {
        // SimpleXMLElement fails due to some of the characters in the query string, so they get stripped out here...
        if (strstr($string, 'next-partition-href'))
        {
            preg_match("/\/([a-z]+\?.*)\"/", $string, $matches);
            
            $queryparams = $matches[1];
            $string = str_replace($queryparams, "", $string);
        }
        
        $data = new SimpleXMLElement($string);
        $data = get_object_vars($data);
        
        // ...and replaced here.
        if ($data['@attributes']['next-partition-href'])
        {
            $data['@attributes']['next-partition-href'] = $data['@attributes']['next-partition-href'] . $queryparams;
        }
        
        return $data;
    }
}
