<?php
namespace Chamilo\Libraries\Protocol\Security\Service;

use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Protocol\Security\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SecurityUtilities
{
    private ChamiloRequest $chamiloRequest;

    private SessionInterface $session;

    public function __construct(
        SessionInterface $session, ChamiloRequest $chamiloRequest
    )
    {
        $this->session = $session;
        $this->chamiloRequest = $chamiloRequest;
    }

    /**
     * This function checks that the token generated in get_token() has been kept (prevents Cross-Site Request Forgeries
     * attacks)
     */
    public function checkToken(string $tokenType = 'post'): bool
    {
        $session = $this->getSession();
        $request = $this->getChamiloRequest();

        $sessionToken = $session->get('sec_token');
        $tokenTypeValue = $tokenType;

        if ($tokenType == 'get')
        {
            $tokenTypeValue = $request->query->get('sec_token');
        }

        if ($tokenType == 'post')
        {
            $tokenTypeValue = $request->request->get('sec_token');
        }

        if (isset($sessionToken) && isset($tokenTypeValue) && $sessionToken === $tokenTypeValue)
        {
            return true;
        }

        return false;
    }

    public function getChamiloRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * This function tackles the XSS injections.
     * Filtering for XSS is very easily done by using the htmlentities()
     * function. This kind of filtering prevents JavaScript snippets to be understood as such.
     */
    public function removeXSS(string|array|null $variable): string|array|null
    {
        if (is_array($variable))
        {
            return $this->removeXSSRecursive($variable);
        }

        // from: http://stackoverflow.com/questions/1336776/xss-filtering-function-in-php
        // from: https://gist.github.com/mbijon/1098477

        // Remove any attribute starting with "on" or xmlns
        $variable = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $variable);

        // Remove javascript: and vbscript: protocols
        $variable = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...', $variable
        );
        $variable = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...', $variable
        );
        $variable = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $variable
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $variable = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $variable
        );
        $variable = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $variable
        );
        $variable = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>', $variable
        );

        // Remove namespaced elements (we do not need them)
        $variable = preg_replace('#</*\w+:\w[^>]*+>#i', '', $variable);

        do
        {
            // Remove really unwanted tags, but allow object|embed (for html editor)
            $oldData = $variable;
            $variable = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|s(?:cript)|xml)[^>]*+>#i',
                '', $variable
            );
        }
        while ($oldData !== $variable);

        return $variable;
    }

    /**
     * @param string[] $array
     *
     * @return string[]
     */
    public function removeXSSRecursive(array $array): array
    {
        foreach ($array as $key => $value)
        {
            $key2 = $this->removeXSS($key);
            $value2 = (is_array($value)) ? $this->removeXSSRecursive($value) : $this->removeXSS(
                $value
            );

            unset($array[$key]);
            $array[$key2] = $value2;
        }

        return $array;
    }
}
