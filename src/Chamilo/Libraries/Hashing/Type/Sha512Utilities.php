<?php
namespace Chamilo\Libraries\Hashing\Type;

use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 *
 * @package Chamilo\Libraries\Hashing\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Sha512Utilities extends HashingUtilities
{

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function hashFile($filePath)
    {
        return hash_file('sha512', $filePath);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function hashString($string)
    {
        return hash('sha512', $string);
    }
}
