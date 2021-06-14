<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

//use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ImportUsersComponent extends Manager
{
    const STATE_HEADERS = 0;
    const STATE_RESULTS = 1;
    const STATE_STATS = 2;

    function run()
    {
        try {
            if (!$this->getRequest()->isMethod('POST'))
            {
                throw new NotAllowedException();
            }
            $entityType = $this->getEvaluationServiceBridge()->getCurrentEntityType();

            if ($entityType != 0)
            {
                throw new UserException('Import functionality is only available for user entities.');
            }

            $handle = fopen($_FILES['file']['tmp_name'], 'r');
            $headers = [];
            $results = [];
            $stats = [];
            $shouldRemoveLastColumn = true;

            $state = self::STATE_HEADERS;

            $headers[] = fgetcsv($handle, null, ';');

            while (($row_tmp = fgetcsv($handle, null, ";")) !== FALSE)
            {
                if (count($row_tmp) === 1 && empty($row_tmp[0]))
                {
                    $state = self::STATE_STATS;
                    continue;
                }

                if ($state === self::STATE_HEADERS && !empty($row_tmp[2])) // $row_tmp ID field has a value
                {
                    $state = self::STATE_RESULTS;
                }

                if (!empty(end($row_tmp)))
                {
                    $shouldRemoveLastColumn = false;
                }

                switch ($state)
                {
                    case self::STATE_STATS:
                        $stats[] = $row_tmp;
                        break;
                    case self::STATE_RESULTS:
                        $results[] = $row_tmp;
                        break;
                    case self::STATE_HEADERS:
                        $headers[] = $row_tmp;
                        break;
                }
            }
            if ($headers[0][2] !== 'ID')
            {
                throw new UserException('This doesnt appear to be a Curios exported CSV file.');
            }
            if ($shouldRemoveLastColumn)
            {
                $headers = $this->removeLastColumn($headers);
                $results = $this->removeLastColumn($results);
                $stats = $this->removeLastColumn($stats);
            }

            $entityIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
            $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $entityService = $this->getEntityServiceByType($entityType);
            $selectedEntities = $entityService->getEntitiesFromIds($entityIds, $contextIdentifier, EvaluationEntityRetrieveProperties::SCORES(), new FilterParameters());
            $users = iterator_to_array($selectedEntities);
            $results = $this->mapToUsers($users, $results);

            $result = new JsonAjaxResult();
            $result->set_result_code(200);
            $result->set_properties(['header_rows' => $headers, 'result_rows' => $results, 'stat_rows' => $stats]);
            $result->display();
        }
        catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }

    /**
     * @param array $users
     * @param array $resultsList
     * @return array
     */
    private function mapToUsers(array & $users, array $resultsList): array
    {
        $lst = array();
        foreach ($resultsList as $results)
        {
            $user = $this->findAndPurgeUser($users, $results[2]);
            $lst[] = ['values' => $results, 'valid' => isset($user), 'user' => $user];
        }
        return $lst;
    }

    /**
     * @param array $users
     * @param string $userId
     *
     * @return array|null
     */
    private function findAndPurgeUser(array & $users, string $userId): ?array
    {
        foreach ($users as $key => $user)
        {
            if (str_ends_with($user['official_code'], $userId))
            {
                unset($users[$key]);
                return $user;
            }
        }
        return null;
    }

    /**
     * @param array $itemsList
     * @return array
     */
    private function removeLastColumn(array $itemsList): array
    {
        $lst = array();
        foreach ($itemsList as $items)
        {
            $lst[] = array_slice($items, 0, -1);
        }
        return $lst;
    }
}