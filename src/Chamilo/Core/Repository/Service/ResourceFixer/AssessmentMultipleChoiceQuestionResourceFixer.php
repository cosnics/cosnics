<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentMultipleChoiceQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMultipleChoiceQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing AssessmentMultipleChoiceQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentMultipleChoiceQuestions();

        $this->logger->addInfo(sprintf('Found %s AssessmentMultipleChoiceQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentMultipleChoiceQuestions = $this->contentObjectResourceFixerRepository
                ->findAssessmentMultipleChoiceQuestions($offset);

            foreach ($assessmentMultipleChoiceQuestions as $assessmentMultipleChoiceQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentMultipleChoiceQuestion with id %s',
                        $assessmentMultipleChoiceQuestion->getId()
                    )
                );

                $changed = false;

                $options = $assessmentMultipleChoiceQuestion->get_options();

                $this->logger->debug(sprintf('Found %s options', count($options)));

                foreach ($options as $index => $option)
                {
                    $this->logger->debug(sprintf('Parsing option %s value', $index));

                    $value = $option->get_value();
                    $newValue = $this->fixResourcesInTextContent($value);

                    if ($value != $newValue)
                    {
                        $option->set_value($newValue);
                        $changed = true;
                    }

                    $this->logger->debug(sprintf('Parsing option %s feedback', $index));

                    $feedback = $option->get_feedback();
                    $newFeedback = $this->fixResourcesInTextContent($feedback);

                    if ($feedback != $newFeedback)
                    {
                        $option->set_feedback($newFeedback);
                        $changed = true;
                    }
                }

                $assessmentMultipleChoiceQuestion->set_options($options);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentMultipleChoiceQuestion with id %s',
                            $assessmentMultipleChoiceQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $assessmentMultipleChoiceQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing AssessmentMultipleChoiceQuestion objects');
    }
}