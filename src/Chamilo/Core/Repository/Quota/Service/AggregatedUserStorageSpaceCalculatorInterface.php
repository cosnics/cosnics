<?php
namespace Chamilo\Core\Repository\Quota\Service;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface AggregatedUserStorageSpaceCalculatorInterface
{
    public function getMaximumAggregatedUserStorageSpace(): int;
}