<?php
namespace Chamilo\Libraries\Hashing;

/**
 *
 * @package Chamilo\Libraries\Hashing
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HashingUtilities
{

    /**
     *
     * @param string $value
     * @return string
     */
    abstract public function hashString($value);

    /**
     *
     * @param string $filePath
     * @return string
     */
    abstract public function hashFile($filePath);

    /**
     *
     * @return string[]
     */
    public function get_available_types()
    {
        return array(
            'Haval256' => 'HAVAL-256',
            'Md5' => 'MD5',
            'Sha1' => 'SHA-1',
            'Sha512' => 'SHA-512',
            'Whirlpool' => 'Whirlpool');
    }
}
