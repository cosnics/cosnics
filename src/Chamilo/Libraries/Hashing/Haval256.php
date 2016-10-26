<?php
namespace Chamilo\Libraries\Hashing;

/**
 * $Id: haval256_hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.hashing.haval256
 */
/**
 * Class that defines Haval256 hashing with 5 passes
 * 
 * @author Samumon
 */
class Haval256 extends Hashing
{

    public function create_hash($value)
    {
        return hash('haval256,5', $value);
    }

    public function create_file_hash($file)
    {
        return hash_file('haval256,5', $file);
    }
}
