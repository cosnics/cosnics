<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Libraries\Hashing\HashingUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Platform
 */
class Security
{
    private ChamiloRequest $chamiloRequest;

    private HashingUtilities $hashingUtilities;

    private SessionInterface $session;

    public function __construct(
        SessionInterface $session, ChamiloRequest $chamiloRequest, HashingUtilities $hashingUtilities
    )
    {
        $this->session = $session;
        $this->chamiloRequest = $chamiloRequest;
        $this->hashingUtilities = $hashingUtilities;
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

    /**
     * Checks the user agent of the client as recorder by get_ua() to prevent most session hijacking attacks.
     */
    public function checkUa(): bool
    {
        $session = $this->getSession();
        $request = $this->getChamiloRequest();

        $session_agent = $session->get('sec_ua');
        $current_agent = $request->server->get('HTTP_USER_AGENT') . $session->get('sec_ua_seed');

        if (isset($session_agent) and $session_agent === $current_agent)
        {
            return true;
        }

        return false;
    }

    /**
     * @deprecated Use Security::checkToken() now
     */
    public function check_token(string $tokenType = 'post'): bool
    {
        return self::checkToken($tokenType);
    }

    /**
     * @deprecated Use Security::checkUa() now
     */
    public function check_ua(): bool
    {
        return self::checkUa();
    }

    public function getChamiloRequest(): ChamiloRequest
    {
        return $this->chamiloRequest;
    }

    public function getHashingUtilities(): HashingUtilities
    {
        return $this->hashingUtilities;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * This function sets a random token to be included in a form as a hidden field and saves it into the user's
     * session. This later prevents Cross-Site Request Forgeries by checking that the user is really the one that sent
     * this form in knowingly (this form hasn't been generated from another website visited by the user at the same
     * time). Check the token with check_token()
     */
    public function getToken(): string
    {
        $token = $this->getHashingUtilities()->hashString(uniqid(rand(), true));
        $this->getSession()->set('sec_token', $token);

        return $token;
    }

    /**
     * Gets the user agent in the session to later check it with check_ua() to prevent most cases of session hijacking.
     */
    public function getUa()
    {
        $session = $this->getSession();
        $session->set('sec_ua_seed', uniqid(rand(), true));
        $session->set(
            'sec_ua', $this->getChamiloRequest()->server->get('HTTP_USER_AGENT') . $session->get('sec_ua_seed')
        );
    }

    /**
     * @deprecated Use Security::getToken() now
     */
    public function get_token(): string
    {
        return $this->getToken();
    }

    /**
     * @deprecated Use Security::getUa() now
     */
    public function get_ua()
    {
        self::getUa();
    }

    /**
     * This function tackles the XSS injections.
     * Filtering for XSS is very easily done by using the htmlentities()
     * function. This kind of filtering prevents JavaScript snippets to be understood as such.
     *
     * @param string|array $variable
     *
     * @return string|array
     */
    public function removeXSS($variable)
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
            $old_data = $variable;
            $variable = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|s(?:cript)|xml)[^>]*+>#i',
                '', $variable
            );
        }
        while ($old_data !== $variable);

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

    /**
     * @deprecated Use Security::removeXSS() now
     */
    public function remove_XSS(string $variable): string
    {
        return $this->removeXSS($variable);
    }

    /**
     * @param string[] $array
     *
     * @return string[]
     * @deprecated Use Security::removeXSSRecursive() now
     */
    public function remove_XSS_recursive(array $array): array
    {
        return $this->removeXSSRecursive($array);
    }
}
