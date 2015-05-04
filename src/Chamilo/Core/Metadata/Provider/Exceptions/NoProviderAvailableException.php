<?php
namespace Chamilo\Core\Metadata\Provider\Exceptions;

use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Exceptions
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NoProviderAvailableException extends \Exception
{

    /**
     *
     * @see Exception::__construct()
     */
    public function __construct($code, $previous)
    {
        parent :: __construct(Translation :: get('NoProviderAvailable'), $code, $previous);
    }
}