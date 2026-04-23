<?php
namespace Chamilo\Libraries\Protocol\Security\Service\Hashing;

use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;

/**
 * @package Chamilo\Libraries\Protocol\Security\Service\Hashing
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Md5HashingAlgorithm extends HashingAlgorithm
{
    public function hashFile(string $filePath): string
    {
        return md5_file($filePath);
    }

    public function hashString(string $value): string
    {
        return md5($value);
    }
}
