<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Manager;

/**
 * @package Chamilo\Core\Tracking\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class SimpleTracker extends Tracker
{
    public const CONTEXT = Manager::CONTEXT;
}
