<?php
namespace Chamilo\Core\Metadata\Provider\Exceptions;

use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Exceptions
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class NoProviderAvailableException extends Exception
{

    /**
     *
     * @see Exception::__construct()
     */
    public function __construct($code = null, $previous = null)
    {
        parent::__construct(Translation::get('NoProviderAvailable'), $code, $previous);
    }
}