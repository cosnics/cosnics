<?php
namespace Chamilo\Application\Calendar\Extension\Google\Package;

use Chamilo\Libraries\Architecture\Application\WebApplicationRemover;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Remover extends WebApplicationRemover
{
    public const CONTEXT = Installer::CONTEXT;
}
