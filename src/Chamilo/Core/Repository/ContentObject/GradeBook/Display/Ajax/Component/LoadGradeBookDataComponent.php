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
}