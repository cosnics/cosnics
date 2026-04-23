<?php
namespace Chamilo\Libraries\Protocol\Security\Service\Hashing;

use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;

/**
 * @package Chamilo\Libraries\Protocol\Security\Service\Hashing
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Haval256HashingAlgorithm extends HashingAlgorithm
{
    public function hashFile(string $filePath): string
    {
        return hash_file('haval256,5', $filePath);
    }

    public function hashString(string $value): string
    {
        return hash('haval256,5', $value);
    }
}
