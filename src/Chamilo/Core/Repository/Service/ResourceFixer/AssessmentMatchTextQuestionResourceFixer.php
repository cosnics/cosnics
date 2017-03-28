<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentMatchTextQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMatchTextQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing AssessmentMatchTextQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentMatchTextQuestions();

        $this->logger->addInfo(sprintf('Found %s AssessmentMatchTextQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentMatchTextQuestions =
                $this->contentObjectResourceFixerRepository->findAssessmentMatchTextQuestions(
                    $offset
                );

            foreach($assessmentMatchTextQuestions as $assessmentMatchTextQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentMatchTextQuestion with id %s', $assessmentMatchTextQuestion->getId()
                    )
                );

                $changed = false;

                $options = $assessmentMatchTextQuestion->get_options();

                $this->logger->debug(sprintf('Found %s options', count($options)));

                foreach ($options as $index => $option)
                {
                    $this->logger->debug(sprintf('Parsing option %s feedback', $index));

                    $feedback = $option->get_feedback();
                    $newFeedback = $this->fixResourcesInTextContent($feedback);

                    if ($feedback != $newFeedback)
                    {
                        $option->set_feedback($newFeedback);
                        $changed = true;
                    }
                }

                $assessmentMatchTextQuestion->set_options($options);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentMatchTextQuestion with id %s',
                            $assessmentMatchTextQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $assessmentMatchTextQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing AssessmentMatchTextQuestion objects');
    }
}