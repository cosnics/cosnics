<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentRatingQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentRatingQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing AssessmentRatingQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentRatingQuestions();

        $this->logger->addInfo(sprintf('Found %s AssessmentRatingQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentRatingQuestions =
                $this->contentObjectResourceFixerRepository->findAssessmentRatingQuestions($offset);

            foreach($assessmentRatingQuestions as $assessmentRatingQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentRatingQuestion with id %s', $assessmentRatingQuestion->getId()
                    )
                );

                $feedback = $assessmentRatingQuestion->get_feedback();
                $newFeedback = $this->fixResourcesInTextContent($feedback);

                if ($feedback != $newFeedback)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentRatingQuestion with id %s',
                            $assessmentRatingQuestion->getId()
                        )
                    );
                    
                    $assessmentRatingQuestion->set_feedback($newFeedback);

                    if($forceUpdate)
                    {
                        $assessmentRatingQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing AssessmentRatingQuestion objects');
    }
}