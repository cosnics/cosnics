<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the AssessmentMatrixQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentMatrixQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing AssessmentMatrixQuestion objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countAssessmentMatrixQuestions();

        $this->logger->addInfo(sprintf('Found %s AssessmentMatrixQuestion objects', $count));

        while ($offset < $count)
        {
            $assessmentMatrixQuestions =
                $this->contentObjectResourceFixerRepository->findAssessmentMatrixQuestions($offset);

            foreach ($assessmentMatrixQuestions as $assessmentMatrixQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing AssessmentMatrixQuestion with id %s', $assessmentMatrixQuestion->getId()
                    )
                );

                $changed = false;

                $options = $assessmentMatrixQuestion->get_options();

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

                $assessmentMatrixQuestion->set_options($options);

                $matches = $assessmentMatrixQuestion->get_matches();
                $newMatches = array();

                $this->logger->debug(sprintf('Found %s matches', count($matches)));

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

                $assessmentMatrixQuestion->set_matches($newMatches);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating AssessmentMatrixQuestion with id %s',
                            $assessmentMatrixQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $assessmentMatrixQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing AssessmentMatrixQuestion objects');
    }
}