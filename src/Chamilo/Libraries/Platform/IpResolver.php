<?php

namespace Chamilo\Libraries\Platform;

/**
 * @package Chamilo\Libraries\Platform
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class IpResolver
{
    public function resolveIpFromRequest(ChamiloRequest $request)
    {
        $ipAddress = $request->server->get('REMOTE_ADDR');
        if ($request->server->has('HTTP_X_FORWARDED_FOR'))
        {
            $forwardedFor = $request->server->get('HTTP_X_FORWARDED_FOR');
            $possibleAddresses = explode(',', $forwardedFor);
            $ipAddress = array_pop($possibleAddresses);
        }

        return $ipAddress;
    }
}
