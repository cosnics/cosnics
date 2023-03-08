<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookColumn;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookData;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class ExportComponent extends Manager
{
    /**
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \Doctrine\ORM\ORMException
     */
    public function run()
    {
        $this->checkAccessRights();

        $exportService = $this->getExportService();
        $targetUsers = $this->getGradeBookServiceBridge()->getTargetUsers();
        $gradeBookData = $this->getGradeBookService()->getGradeBookData($this->getGradeBook());
        $gradebookItems = $this->getGradeBookServiceBridge()->findPublicationGradeBookItems();
        $this->getGradeBookService()->completeGradeBookData($gradeBookData, $gradebookItems);

        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', $this->getGradeBook()->get_title()) . '.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        $columns = $gradeBookData->getGradeBookColumnsOrderedByCategory();
        $resultsData = $gradeBookData->getResultsData();
        $aabsAbbr = $this->getTranslator()->trans('AabsAbbr', [], Manager::context());

        fputcsv($output, $this->getCsvHeaders($gradeBookData, $columns), ';');

        foreach ($targetUsers as $user)
        {
            fputcsv($output, $exportService->getUserResults($user, $gradeBookData, $columns, $resultsData, $aabsAbbr), ';');
        }

        fclose($output);
    }

    /**
     * @param GradeBookData $gradeBookData
     * @param GradeBookColumn[] $columns
     *
     * @return string[]
     */
    protected function getCsvHeaders(GradeBookData $gradeBookData, array $columns): array
    {
        $userHeaders = [
            $this->getTranslator()->trans('SortName', [], 'Chamilo\Application\Weblcms'),
            $this->getTranslator()->trans('LastName', [], 'Chamilo\Core\User'),
            $this->getTranslator()->trans('FirstName', [], 'Chamilo\Core\User'),
        ];

        $scoreHeaders = $this->getExportService()->getColumnTitles($gradeBookData, $columns);

        $finalScoreHeaders = [$this->getTranslator()->trans('FinalScore', [], Manager::context()) . ' %'];

        if ($gradeBookData->usesDisplayTotal())
        {
            $finalScoreHeaders[] = $this->getTranslator()->trans('FinalScoreOutOf', [], Manager::context()) . ' ' . $gradeBookData->getDisplayTotal();
        }

        return array_merge($userHeaders, $scoreHeaders, $finalScoreHeaders);
    }

    /**
     * @throws NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getGradeBookServiceBridge()->canEditGradeBook())
        {
            throw new NotAllowedException();
        }
    }
}