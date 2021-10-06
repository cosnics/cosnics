<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class TogglePresenceEntryCheckoutComponent extends Manager implements CsrfComponentInterface
{
    function run()
    {
        try
        {
            if (!$this->canUserEditPresence()) {
                throw new NotAllowedException();
            }
            $this->validatePresenceUserInput();

            $periodId = $this->getRequest()->getFromPostOrUrl('period_id');
            $userId = $this->getRequest()->getFromPostOrUrl('user_id');
            $presenceResultEntry = $this->getPresenceResultEntryService()->togglePresenceResultEntryCheckout($periodId, $userId);

            $result = [
                'status' => 'ok',
                'checked_in_date' => $presenceResultEntry->getCheckedInDate(),
                'checked_out_date' => $presenceResultEntry->getCheckedOutDate()
            ];

            return new JsonResponse($this->serialize($result), 200, [], true);
        }
        catch (\Exception $ex) {
            return new JsonResponse(['error' => ['code' => 500, 'message' => $ex->getMessage()]], 500);
        }
    }
}