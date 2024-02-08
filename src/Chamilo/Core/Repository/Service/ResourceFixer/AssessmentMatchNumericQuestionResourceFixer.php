<?php
namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentMatchNumericQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMatchNumericQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->info('Started fixing AssessmentMatchNumericQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentMatchNumericQuestions();

        $this->logger->info(sprintf('Found %s AssessmentMatchNumericQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentMatchNumericQuestions =
                $this->contentObjectResourceFixerRepository->findAssessmentMatchNumericQuestions(
                    $offset
                );

            foreach ($assessmentMatchNumericQuestions as $assessmentMatchNumericQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentMatchNumericQuestion with id %s', $assessmentMatchNumericQuestion->getId()
                    )
                );


                $changed = false;

                $options = $assessmentMatchNumericQuestion->get_options();

                $this->logger->debug(sprintf('Found %s options', count($options)));

                foreach ($options as $index => $option)
                {
                    $this->logger->debug(sprintf('Parsing option %s feedback', $index));

                    $feedback = $option->get_feedback();
                    $newFeedback = $this->fixResourcesInTextContent($assessmentMatchNumericQuestion, $feedback);

                    if ($feedback != $newFeedback)
                    {
                        $option->set_feedback($newFeedback);
                        $changed = true;
                    }
                }

                $assessmentMatchNumericQuestion->set_options($options);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentMatchNumericQuestion with id %s',
                            $assessmentMatchNumericQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $assessmentMatchNumericQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->info('Finished fixing AssessmentMatchNumericQuestion objects');
    }
}