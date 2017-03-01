<?php

namespace Chamilo\Application\Weblcms\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathItemAttempt;
use Chamilo\Application\Weblcms\Service\Interfaces\PublicationServiceInterface;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Recalculates the learning path progress for every learning path publication
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathProgressFixer
{
    /**
     * @var PublicationServiceInterface
     */
    protected $publicationService;

    /**
     * LearningPathProgressFixer constructor.
     *
     * @param PublicationServiceInterface $publicationService
     */
    public function __construct(PublicationServiceInterface $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     * Fixes the learning path's progress
     *
     * @param bool $dryRun - Do not execute the update statement if it's a dry run
     */
    public function fixLearningPathProgress($dryRun = false)
    {
        $publications = $this->publicationService->getPublicationsByTool('LearningPath');

        foreach($publications as $publication)
        {
            /** Do not fix publications older then the 1st of january 2016 */
            if($publication->get_publication_date() < 1451602800)
            {
                continue;
            }

            $contentObject = $publication->getContentObject();
            if(!$contentObject instanceof LearningPath)
            {
                continue;
            }

            $contentObjectPath = $contentObject->get_complex_content_object_path();

            $learningPathAttempts = $this->getLearningPathAttemptsForPublication($publication);

            foreach($learningPathAttempts as $learningPathAttempt)
            {
                $learningPathItemAttempts = $this->getLearningPathItemAttemptsForLearningPathAttempt(
                    $learningPathAttempt
                );

                $contentObjectPath->set_nodes_attempt_data($learningPathItemAttempts);

                $progress = $contentObjectPath->get_progress();

                if($learningPathAttempt->get_progress() != $progress)
                {
                    echo 'LearningPath: ' . $publication->getId() . ' - Attempt: ' . $learningPathAttempt->getId()
                        . ' - old: ' . $learningPathAttempt->get_progress() . ' - new: ' . $progress . PHP_EOL;

                    if(!$dryRun)
                    {
                        $learningPathAttempt->set_progress($progress);
                        $learningPathAttempt->update();
                    }
                }
            }
        }
    }

    /**
     * Retrieves the attempts for a given content object publication
     * 
     * @param ContentObjectPublication $publication
     *
     * @return LearningPathAttempt[]
     */
    protected function getLearningPathAttemptsForPublication(ContentObjectPublication $publication)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttempt::class_name(),
                LearningPathAttempt::PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($publication->getId())
        );

        return DataManager::retrieves(
            LearningPathAttempt::class_name(), new DataClassRetrievesParameters($condition)
        )->as_array();
    }

    /**
     * Retrieves the learning path item attempts for a given learning path attempt
     *
     * @param LearningPathAttempt $learningPathAttempt
     *
     * @return array
     */
    protected function getLearningPathItemAttemptsForLearningPathAttempt(LearningPathAttempt $learningPathAttempt)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathItemAttempt::class_name(),
                LearningPathItemAttempt::PROPERTY_LEARNING_PATH_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttempt->getId())
        );

        $learningPathItemAttemptResultSet = DataManager::retrieves(
            LearningPathItemAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        $learningPathItemAttempts = array();

        while ($learningPathItemAttempt = $learningPathItemAttemptResultSet->next_result())
        {
            $learningPathItemAttempts[$learningPathItemAttempt->get_learning_path_item_id()][] =
                $learningPathItemAttempt;
        }

        return $learningPathItemAttempts;
    }
}