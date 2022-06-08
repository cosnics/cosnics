<?php
namespace Chamilo\Libraries\Hashing\Type;

use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 *
 * @package Chamilo\Libraries\Hashing\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Sha1Utilities extends HashingUtilities
{

    public function hashFile(string $filePath): string
    {
        return sha1_file($filePath);
    }

    public function hashString(string $value): string
    {
        return sha1($value);
    }
}
