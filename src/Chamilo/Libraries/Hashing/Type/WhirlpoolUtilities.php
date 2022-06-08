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

    public function hashFile(string $filePath): string
    {
        return hash_file('whirlpool', $filePath);
    }

    public function hashString(string $value): string
    {
        return hash('whirlpool', $value);
    }
}
