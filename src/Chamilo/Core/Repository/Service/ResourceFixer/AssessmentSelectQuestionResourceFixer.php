<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentSelectQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentSelectQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing AssessmentSelectQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentSelectQuestions();

        $this->logger->addInfo(sprintf('Found %s AssessmentSelectQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentSelectQuestions = $this->contentObjectResourceFixerRepository
                ->findAssessmentSelectQuestions($offset);

            foreach($assessmentSelectQuestions as $assessmentSelectQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentSelectQuestion with id %s', $assessmentSelectQuestion->getId()
                    )
                );

                $changed = false;

                $options = $assessmentSelectQuestion->get_options();
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

                $assessmentSelectQuestion->set_options($options);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentSelectQuestion with id %s',
                            $assessmentSelectQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $assessmentSelectQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing AssessmentSelectQuestion objects');
    }
}