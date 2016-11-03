<?php
namespace Chamilo\Libraries\Hashing;

/**
 *
 * @package Chamilo\Libraries\Hashing
 * @author Samumon
 * @author vanpouckesven
 * @deprecated Use HashingUtilities now
 */
class Md5 extends Hashing
{

    /**
     *
     * @see \Chamilo\Libraries\Hashing\Hashing::create_hash()
     */
    public function create_hash($value)
    {
        return md5($value);
    }

    /**
     *
     * @see \Chamilo\Libraries\Hashing\Hashing::create_file_hash()
     */
    public function create_file_hash($file)
    {
        return md5_file($file);
    }
}
