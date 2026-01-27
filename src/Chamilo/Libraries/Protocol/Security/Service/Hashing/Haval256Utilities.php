<?php
namespace Chamilo\Libraries\Protocol\Security\Service\Hashing;

use Chamilo\Libraries\Protocol\Security\Service\HashingUtilities;

/**
 *
 * @package Chamilo\Libraries\Hashing\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Haval256Utilities extends HashingUtilities
{

    public function hashFile(string $filePath): string
    {
        return hash_file('haval256,5', $$filePath);
    }

    public function hashString(string $value): string
    {
        return hash('haval256,5', $value);
    }
}
