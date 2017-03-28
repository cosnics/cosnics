<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the HotspotQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class HotspotQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing HotspotQuestion objects');
        
        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countHotspotQuestions();

        $this->logger->addInfo(sprintf('Found %s HotspotQuestion objects', $count));

        while ($offset < $count)
        {
            $hotspotQuestions = $this->contentObjectResourceFixerRepository->findHotspotQuestions($offset);

            foreach($hotspotQuestions as $hotspotQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing HotspotQuestion with id %s', $hotspotQuestion->getId()
                    )
                );

                $changed = false;

                $answers = $hotspotQuestion->get_answers();

                $this->logger->debug(sprintf('Found %s answers', count($answers)));

                foreach ($answers as $index => $answer)
                {
                    $this->logger->debug(sprintf('Parsing answer %s value', $index));

                    $value = $answer->get_answer();
                    $newValue = $this->fixResourcesInTextContent($value);

                    if ($value != $newValue)
                    {
                        $answer->set_answer($newValue);
                        $changed = true;
                    }

                    $this->logger->debug(sprintf('Parsing answer %s feedback', $index));

                    $feedback = $answer->get_comment();
                    $newFeedback = $this->fixResourcesInTextContent($feedback);

                    if ($feedback != $newFeedback)
                    {
                        $answer->set_comment($newFeedback);
                        $changed = true;
                    }
                }

                $hotspotQuestion->set_answers($answers);

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating HotspotQuestion with id %s',
                            $hotspotQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $hotspotQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing HotspotQuestion objects');
    }
}