<?php
namespace Chamilo\Libraries\Platform;

/**
 *
 * @package Chamilo\Libraries\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Relocated to Chamilo\Libraries\Platform\Translation
 */
class Translation
{

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    static public function getInstance()
    {
        return \Chamilo\Libraries\Translation\Translation::getInstance();
    }

    /**
     *
     * @deprecated Use getTranslation() now
     * @param string $variable
     * @param string[] $parameters
     * @return string
     *
     */
    public static function get($variable, $parameters = array(), $context = null, $isocode = null)
    {
        return \Chamilo\Libraries\Translation\Translation::getInstance()::get(
            $variable,
            $parameters,
            $context,
            $isocode);
    }
}
