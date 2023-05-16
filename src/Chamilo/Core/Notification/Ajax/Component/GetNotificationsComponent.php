<?php
namespace Chamilo\Core\Notification\Ajax\Component;

use Chamilo\Core\Notification\Ajax\Manager;
use Chamilo\Libraries\Format\Response\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetNotificationsComponent extends Manager
{

    /**
     *
     * @return string
     */
    function run()
    {
        $jsonResponse = '[{"message":"Sonia VANDERMEERSCH heeft een nieuwe inzending geplaatst in de opdracht <b>Programmeren 101</b> in de cursus <b>Web Development</b>","time":"4 uur geleden","url":"","isRead":false,"isNew":true,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van de cursus <b>Web Development</b>","id":1},{"message":"Verberg alle meldingen van de opdracht <b>Programmeren 101</b> in de cursus <b>Web Development</b>","id":2}]},{"message":"Tom DEMETS heeft de aankondiging <b>Examenrooster 2018</b> geplaatst in de cursus <b>Elektronica voor gevorderden</b>","time":"20 uur geleden","url":"","isRead":false,"isNew":true,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van de cursus <b>Elektronica voor gevorderden</b>","id":3},{"message":"Verberg alle meldingen over <b>aankondigingen</b> in de cursus <b>Elektronica voor gevorderden</b>","id":4}]},{"message":"Lucas VANDEREECKEN heeft het leerpad <b>Kritisch denken</b> aangepast in de cursus <b>Kritisch denken voor beginners</b>","time":"gisteren - 20u20","url":"","isRead":false,"isNew":false,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van de cursus <b>Kritisch denken voor beginners</b>","id":5},{"message":"Verberg alle meldingen in het leerpad <b>Kritisch denken</b> in de cursus <b>Kritisch denken voor beginners</b>","id":6}]},{"message":"Jonas DESMET heeft een het item <b>Stage attest</b> toegevoegd aan zijn portfolio","time":"20 augustus 2018 - 18u35","url":"","isRead":true,"isNew":false,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van het portfolio van <b>Jonas DESMET</b>","id":7}]},{"message":"Filip WATTEEUW heeft de publicatie <b>WEGWERKZAAMHEDEN GENT 2018 / 2019</b> toegevoegd aan de cursus <b>Burgers pesten voor beginners</b>","time":"15 augustus 2018 - 10u13","url":"","isRead":true,"isNew":false,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van de cursus <b>Burgers pesten voor beginners</b>","id":8},{"message":"Verberg alle meldingen van nieuwe publicaties in de cursus <b>Burgers pesten voor beginners</b>","id":9}]},{"message":"Jonas DESMET heeft een het item <b>Stage attest</b> toegevoegd aan zijn portfolio","time":"20 augustus 2018 - 18u35","url":"","isRead":true,"isNew":false,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van het portfolio van <b>Jonas DESMET</b>","id":7}]},{"message":"Filip WATTEEUW heeft de publicatie <b>WEGWERKZAAMHEDEN GENT 2018 / 2019</b> toegevoegd aan de cursus <b>Burgers pesten voor beginners</b>","time":"15 augustus 2018 - 10u13","url":"","isRead":true,"isNew":false,"filters":[{"message":"Verberg deze melding","id":null},{"message":"Verberg alle meldingen van de cursus <b>Burgers pesten voor beginners</b>","id":8},{"message":"Verberg alle meldingen van nieuwe publicaties in de cursus <b>Burgers pesten voor beginners</b>","id":9}]}]';

        return new JsonResponse($jsonResponse, 200, [], true);
    }
}