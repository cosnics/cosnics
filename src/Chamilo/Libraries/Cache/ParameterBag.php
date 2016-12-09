<?php
namespace Chamilo\Libraries\Cache;

/**
 *
 * @package Chamilo\Libraries\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ParameterBag extends \Symfony\Component\HttpFoundation\ParameterBag
{
    const PARAM_IDENTIFIER = 'identifier';

    /**
     *
     * @return string
     */
    public function __toString()
    {
        $simpleIdentifier = $this->get(self::PARAM_IDENTIFIER);
        
        if ($simpleIdentifier)
        {
            return $simpleIdentifier;
        }
        else
        {
            return md5(serialize($this->all()));
        }
    }
}