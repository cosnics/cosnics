<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentMatchingQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMatchingQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing AssessmentMatchingQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentMatchingQuestions();

        $this->logger->addInfo(sprintf('Found %s AssessmentMatchingQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentMatchingQuestions = $this->contentObjectResourceFixerRepository
                ->findAssessmentMatchingQuestions($offset);

            foreach ($assessmentMatchingQuestions as $assessmentMatchingQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentMatchingQuestion with id %s', $assessmentMatchingQuestion->getId()
                    )
                );

                $changed = false;

                $options = $assessmentMatchingQuestion->get_options();

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

                $assessmentMatchingQuestion->set_options($options);

                $matches = $assessmentMatchingQuestion->get_matches();

                $this->logger->debug(sprintf('Found %s matches', count($matches)));

                $newMatches = array();

                foreach ($matches as $index => $match)
                {
                    $this->logger->debug(sprintf('Parsing match %s value', $index));

                    $newMatch = $this->fixResourcesInTextContent($match);
                    $newMatches[] = $newMatch;

                    if ($match != $newMatch)
                    {
                        $changed = true;
                    }
                }

                $assessmentMatchingQuestion->set_matches($newMatches);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentMatchingQuestion with id %s',
                            $assessmentMatchingQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $assessmentMatchingQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing AssessmentMatchingQuestion objects');
    }
}