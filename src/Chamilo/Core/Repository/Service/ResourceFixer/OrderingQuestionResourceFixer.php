<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the OrderingQuestion
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OrderingQuestionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing OrderingQuestion objects');
        
        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countOrderingQuestions();

        $this->logger->addInfo(sprintf('Found %s OrderingQuestion objects', $count));

        while ($offset < $count)
        {
            $orderingQuestions = $this->contentObjectResourceFixerRepository->findOrderingQuestions($offset);

            foreach($orderingQuestions as $orderingQuestion)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing OrderingQuestion with id %s', $orderingQuestion->getId()
                    )
                );

                $changed = false;

                $options = $orderingQuestion->get_options();
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

                $orderingQuestion->set_options($options);

                $this->logger->debug(sprintf('Parsing ordering question hint'));

                $hint = $orderingQuestion->get_hint();
                $newHint = $this->fixResourcesInTextContent($hint);

                if($newHint != $hint)
                {
                    $orderingQuestion->set_hint($newHint);
                    $changed = true;
                }

                if ($changed)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating OrderingQuestion with id %s',
                            $orderingQuestion->getId()
                        )
                    );

                    if($forceUpdate)
                    {
                        $orderingQuestion->update(false);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing OrderingQuestion objects');
    }
}