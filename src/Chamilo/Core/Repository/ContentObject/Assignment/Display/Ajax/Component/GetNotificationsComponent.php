<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Manager;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetNotificationsComponent extends Manager
{
    /**
     *
     * @return string
     */
    function run()
    {
        $jsonResponse = '[{"message":"<b>Kevin VAN EENOO</b> heeft een verbeterde versie opgeladen bij de inzending van <b>Sonia VANDERMEERSCH</b>","time":"4 uur geleden","url":"","isRead":false,"isNew":true,"filters":[]},' .
            '{"message":"<b>Tom DEMETS</b> heeft nieuwe feedback geplaatst op de inzending van <b>Sonia VANDERMEERSCH</b>","time":"20 uur geleden","url":"","isRead":false,"isNew":true,"filters":[]},' .
            '{"message":"<b>Sonia VANDERMEERSCH</b> heeft een nieuwe inzending geplaatst","time":"gisteren - 20u20","url":"","isRead":true,"isNew":false,"filters":[]}]';


//        $filter = $filterManager->getFilterForPath('publication:' . $publicationId);
//        $notifications = $notificationManager->getNotificationsForFilters([$filter]);

        $filterPath = 'Chamilo\Application\Weblcms::Publication:5';
        $filter = $filterManager->getFilterForPath($filterPath);
        $notifications = $notificationManager->getNotificationsForFilters([$filter]);

        return new JsonResponse($jsonResponse, 200, [], true);
    }
}