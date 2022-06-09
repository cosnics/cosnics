<?php
namespace Chamilo\Libraries\Hashing;

/**
 * @package Chamilo\Libraries\Hashing
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HashingUtilities
{

    /**
     * @return string[]
     */
    public static function getAvailableTypes(): array
    {
        return [
            'Haval256' => 'HAVAL-256',
            'Md5' => 'MD5',
            'Sha1' => 'SHA-1',
            'Sha512' => 'SHA-512',
            'Whirlpool' => 'Whirlpool'
        ];
    }

    abstract public function hashFile(string $filePath): string;

    abstract public function hashString(string $value): string;
}
