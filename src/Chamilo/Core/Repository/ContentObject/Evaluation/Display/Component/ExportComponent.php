<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class ExportComponent extends Manager
{
    public function run()
    {
        $this->checkAccessRights();

        try
        {
            $evaluation = $this->getEvaluation();
            $userIds = $this->getEvaluationServiceBridge()->getTargetEntityIds();
            $contextIdentifier = $this->getEvaluationServiceBridge()->getContextIdentifier();
            $selectedUsers = $this->getEntityService()->getEntitiesFromIds($userIds, $contextIdentifier, EvaluationEntityRetrieveProperties::ALL());

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', $evaluation->get_title()) . '.csv');

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            // output the column headings
            fputcsv($output, array('lastname', 'firstname', 'official_code', 'score', 'rubric'), ';');

            // output the rows
            foreach ($selectedUsers as $user)
            {
                fputcsv($output, array($user['lastname'], $user['firstname'], $user['official_code'], $user['score'], $user['rubric']), ';');
            }
        } catch (\Exception $ex)
        {
            // todo
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getEvaluationServiceBridge()->canEditEvaluation())
        {
            throw new NotAllowedException();
        }
    }

}