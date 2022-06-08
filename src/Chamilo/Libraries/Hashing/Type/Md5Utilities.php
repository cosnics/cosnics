<?php
namespace Chamilo\Libraries\Hashing\Type;

use Chamilo\Libraries\Hashing\HashingUtilities;

/**
 *
 * @package Chamilo\Libraries\Hashing\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Md5Utilities extends HashingUtilities
{

    public function hashFile(string $filePath): string
    {
        return md5_file($$filePath);
    }

    public function hashString(string $value): string
    {
        return md5($value);
    }
}
