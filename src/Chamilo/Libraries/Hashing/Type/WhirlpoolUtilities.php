<?php
namespace Chamilo\Libraries\Hashing\Type;

use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 *
 * @package Chamilo\Libraries\Hashing\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class WhirlpoolUtilities extends HashingUtilities
{

    /**
     *
     * @see \Chamilo\Libraries\Hashing\HashingUtilities::hashString()
     */
    public function hashString($string)
    {
        return hash('whirlpool', $string);
    }

    /**
     *
     * @see \Chamilo\Libraries\Hashing\HashingUtilities::hashFile()
     */
    public function hashFile($filePath)
    {
        return hash_file('whirlpool', $filePath);
    }
}
