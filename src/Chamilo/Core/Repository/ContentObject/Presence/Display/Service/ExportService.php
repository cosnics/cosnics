<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ExportService
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var PresenceService
     */
    protected $presenceService;

    /**
     * @var PresenceResultPeriodService
     */
    protected $presenceResultPeriodService;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param UserService $userService
     * @param PresenceService $presenceService
     * @param PresenceResultPeriodService $presenceResultPeriodService
     */
    public function __construct(UserService $userService, PresenceService $presenceService, PresenceResultPeriodService $presenceResultPeriodService)
    {
        $this->userService = $userService;
        $this->presenceService = $presenceService;
        $this->presenceResultPeriodService = $presenceResultPeriodService;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Presence $presence
     * @param int[] $userIds
     * @param ContextIdentifier $contextIdentifier
     */
    public function exportPresence(Presence $presence, array $userIds, ContextIdentifier $contextIdentifier)
    {
        $this->outputHeaders($presence);
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        $periods = $this->presenceResultPeriodService->getResultPeriodsForPresence($presence, $contextIdentifier);
        $this->outputColumnHeadings($output, $presence, $periods);

        $users = $this->userService->getUsersFromIds($userIds, $contextIdentifier);
        $this->outputColumnRows($output, $presence, $periods, $users);
    }

    /**
     * @param array $periods
     * @param Presence $presence
     *
     * @return array
     */
    protected function getPeriodLabels(array $periods, Presence $presence): array
    {
        $periodLabels = array();

        foreach ($periods as $index => $period)
        {
            $label = empty($period['label']) ? ('P' . ($index + 1)) : $period['label'];
            $periodLabels[] = $label;
            if ($presence->hasCheckout())
            {
                $periodLabels[] = $label . ' - checkout';
            }
        }
        return $periodLabels;
    }

    /**
     * @param $status
     *
     * @return int
     */
    protected function getAliasedId($status): int
    {
        $id = $status['id'];

        if ($status['type'] == 'custom')
        {
            $id = $status['aliasses'];
        }

        if ($id == Presence::ONLINE_PRESENT_STATUS_ID)
        {
            return 3;
        }

        return $id;
    }

    /**
     * @param array $statuses
     * @param array $user
     * @param int $periodId
     *
     * @return string
     */
    protected function getCheckoutField(array $statuses, array $user, int $periodId): string
    {
        $periodStr = 'period#' . $periodId;

        if (! array_key_exists($periodStr . '-status', $user))
        {
            return '';
        }

        $statusId = $user[$periodStr . '-status'];
        $aliasedId = $this->getAliasedId($statuses[$statusId]);

        if ($aliasedId == 3 &&
            array_key_exists($periodStr . '-checked_in_date', $user) &&
            array_key_exists($periodStr . '-checked_out_date', $user))
        {
            $checkedOut = $user[$periodStr . '-checked_out_date'] > $user[$periodStr . '-checked_in_date'];
            if (isset($this->translator))
            {
                return $this->translator->trans($checkedOut ? 'CheckedOutYes' : 'CheckedOutNo', [], Manager::context());
            }
            return $checkedOut ? 'yes' : 'no';
        }

        return '';
    }

    /**
     * @param array $statuses
     * @param array $user
     * @param int $periodId
     *
     * @return string
     */
    protected function getCodeField(array $statuses, array $user, int $periodId): string
    {
        $periodStr = 'period#' . $periodId;

        if (! array_key_exists($periodStr . '-status', $user))
        {
            return '';
        }

        $statusId = $user[$periodStr . '-status'];

        return $statuses[$statusId]['code'];
    }

    /**
     * @param Presence $presence
     */
    protected function outputHeaders(Presence $presence)
    {
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', $presence->get_title()) . '.csv');
    }

    /**
     * @param $output
     * @param array $periods
     * @param Presence $presence
     */
    protected function outputColumnHeadings($output, Presence $presence, array $periods): void
    {
        // output the column headings
        fputcsv($output, array_merge(array('lastname', 'firstname', 'official_code'), $this->getPeriodLabels($periods, $presence)), ';');
    }

    /**
     * @param $output
     * @param Presence $presence
     * @param array $periods
     * @param array $users
     */
    protected function outputColumnRows($output, Presence $presence, array $periods, array $users): void
    {
        $statuses = $this->presenceService->getPresenceStatuses($presence);

        foreach ($users as $user)
        {
            $fields = array($user['lastname'], $user['firstname'], $user['official_code']);

            foreach ($periods as $period)
            {
                $fields[] = $this->getCodeField($statuses, $user, $period['id']);

                if ($presence->hasCheckout())
                {
                    $fields[] = $this->getCheckoutField($statuses, $user, $period['id']);
                }
            }
            fputcsv($output, $fields, ';');
        }
    }
}