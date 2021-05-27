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
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     * @param string $isocode
     *
     * @return string
     *
     * @deprecated Use getTranslation() now
     */
    public static function get($variable, $parameters = [], $context = null, $isocode = null)
    {
        return \Chamilo\Libraries\Translation\Translation::getInstance()::get(
            $variable, $parameters, $context, $isocode
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    static public function getInstance()
    {
        return \Chamilo\Libraries\Translation\Translation::getInstance();
    }
}
