<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookUserJSONModel;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookScore;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadGradeBookDataComponent extends Manager implements CsrfComponentInterface
{
    function runAjaxComponent()
    {
        $targetUsers = $this->getGradeBookServiceBridge()->getTargetUsers();
        $gradeBookData = $this->getGradeBookAjaxService()->getGradeBookData($this->getGradeBook());
        $gradebookItems = $this->getGradeBookServiceBridge()->findPublicationGradeBookItems();
        $this->getGradeBookAjaxService()->updateGradeBookData($gradeBookData, $gradebookItems);

        $users = array_map(function(User $user) {
            return GradeBookUserJSONModel::fromUser($user);
        }, $targetUsers);

        $scores = array_map(function(GradeBookScore $score) {
            return $score->toJSONModel();
        }, $gradeBookData->getGradeBookScores()->toArray());

        return ['gradebook' => $gradeBookData->toJSONModel(), 'users' => $users, 'scores' => $scores];
    }

    /*public function getGradeBookObjectData(GradeBookData $gradebookData): array
    {
        $resultsData = [
            [ 'id' => 1, 'student' => 'Student 1', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 20  ], ['id' => 2, 'value' => 60], ['id' => 4, 'value' => 80], ['id' => 5, 'value' => 50], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 2, 'student' => 'Student 2', 'results' => [['id' => 1, 'value' => 30  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 50], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 65], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 3, 'student' => 'Student 3', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 50  ], ['id' => 2, 'value' => 30], ['id' => 4, 'value' => 70], ['id' => 5, 'value' => 80], ['id' => 6, 'value' => 95], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 4, 'student' => 'Student 4', 'results' => [['id' => 1, 'value' => 80  ], ['id' => 3, 'value' => null], ['id' => 2, 'value' => 40], ['id' => 4, 'value' => 40], ['id' => 5, 'value' => 30], ['id' => 6, 'value' => 75], ['id' => 7, 'value' => 50]] ],
            [ 'id' => 5, 'student' => 'Student 5', 'results' => [['id' => 1, 'value' => null], ['id' => 3, 'value' => 60  ], ['id' => 2, 'value' => 10], ['id' => 4, 'value' => 90], ['id' => 5, 'value' => 40], ['id' => 6, 'value' => 25], ['id' => 7, 'value' => 50]] ]
        ];

        return [
            'resultsData' => $resultsData
        ];
    }*/
}