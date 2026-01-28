<?php
namespace Chamilo\Libraries\Protocol\Security\Service;

/**
 * @package Chamilo\Libraries\Protocol\Security\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HashingAlgorithm
{
    abstract public function hashFile(string $filePath): string;

    abstract public function hashString(string $value): string;
}
