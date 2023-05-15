<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Package;

use Chamilo\Libraries\Architecture\Application\WebApplicationRemover;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Remover extends WebApplicationRemover
{
    public const CONTEXT = Installer::CONTEXT;
}
