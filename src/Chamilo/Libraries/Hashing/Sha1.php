<?php
namespace Chamilo\Libraries\Hashing;

/**
 * $Id: sha1_hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.hashing.sha1
 */
/**
 * Class that defines sha1 hashing
 * 
 * @author vanpouckesven
 */
class Sha1 extends Hashing
{

    public function create_hash($value)
    {
        return sha1($value);
    }

    public function create_file_hash($file)
    {
        return sha1_file($file);
    }
}
