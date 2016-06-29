<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Common\Template\Template;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestionOption;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestionOption;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestionOption;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestionOption;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestionOption;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestionOption;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestion;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestionAnswer;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestion;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestionOption;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpgraderComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        $this->fixTemplates();
        $this->fixAssessmentMatchNumericQuestions();
        $this->fixAssessmentMatchTextQuestions();
        $this->fixAssessmentMatchingQuestions();
        $this->fixAssessmentMatrixQuestions();
        $this->fixAssessmentMultipleChoiceQuestions();
        $this->fixAssessmentSelectQuestions();
        $this->fixHotspotQuestions();
        $this->fixOrderingQuestions();
        $this->fixAssignmentAllowedTypes();

        $this->fixHomeBlockPublications();
    }

    private function fixTemplates()
    {
        $existingTemplates = DataManager:: retrieves(
            TemplateRegistration:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingTemplate = $existingTemplates->next_result())
        {
            try
            {
                $template = Template:: get($existingTemplate->get_content_object_type(), $existingTemplate->get_name());
                $existingTemplate->set_template($template);
                $existingTemplate->update();
            }
            catch (\Exception $exception)
            {
                $existingTemplate->delete();
            }
        }
    }

    private function fixAssessmentMatchNumericQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            AssessmentMatchNumericQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new AssessmentMatchNumericQuestionOption(
                    $oldOption->value,
                    $oldOption->tolerance,
                    $oldOption->score,
                    $oldOption->feedback
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixAssessmentMatchTextQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            AssessmentMatchTextQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new AssessmentMatchTextQuestionOption(
                    $oldOption->value,
                    $oldOption->score,
                    $oldOption->feedback
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixAssessmentMatchingQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            AssessmentMatchingQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new AssessmentMatchingQuestionOption(
                    $oldOption->value,
                    $oldOption->match,
                    $oldOption->score,
                    $oldOption->feedback
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixAssessmentMatrixQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            AssessmentMatrixQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new AssessmentMatrixQuestionOption(
                    $oldOption->value,
                    $oldOption->score,
                    $oldOption->feedback,
                    $oldOption->matches
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixAssessmentMultipleChoiceQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            AssessmentMultipleChoiceQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new AssessmentMultipleChoiceQuestionOption(
                    $oldOption->value,
                    $oldOption->correct,
                    $oldOption->score,
                    $oldOption->feedback
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixAssessmentSelectQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            AssessmentSelectQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new AssessmentSelectQuestionOption(
                    $oldOption->value,
                    $oldOption->correct,
                    $oldOption->score,
                    $oldOption->feedback
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixHotspotQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            HotspotQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_answers();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new HotspotQuestionAnswer(
                    $oldOption->answer,
                    $oldOption->comment,
                    $oldOption->weight,
                    $oldOption->hotspot_coordinates
                );
            }

            $existingQuestion->set_answers($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixOrderingQuestions()
    {
        $existingQuestions = DataManager:: retrieves(
            OrderingQuestion:: class_name(),
            new DataClassRetrievesParameters()
        );

        while ($existingQuestion = $existingQuestions->next_result())
        {
            $oldOptions = $existingQuestion->get_options();
            $newOptions = array();

            foreach ($oldOptions as $key => $oldOption)
            {
                $oldOption = $this->fix_object($oldOption);

                $newOptions[$key] = new OrderingQuestionOption(
                    $oldOption->value,
                    $oldOption->order,
                    $oldOption->score,
                    $oldOption->feedback
                );
            }

            $existingQuestion->set_options($newOptions);

            DataManager:: update($existingQuestion);
        }
    }

    private function fixAssignmentAllowedTypes()
    {
        $existingAssignments = DataManager:: retrieves(Assignment:: class_name(), new DataClassRetrievesParameters());

        while ($existingAssignment = $existingAssignments->next_result())
        {
            $oldAllowedTypes = $existingAssignment->get_allowed_types();
            $oldAllowedTypes = explode(',', $oldAllowedTypes);

            $newAllowedTypes = array();

            foreach ($oldAllowedTypes as $oldAllowedType)
            {
                $packageName = ClassnameUtilities:: getInstance()->getPackageNameFromNamespace($oldAllowedType);
                $newAllowedTypes[] = 'Chamilo\Core\Repository\ContentObject\\' . $packageName . '\Storage\DataClass\\' .
                    $packageName;
            }

            $existingAssignment->set_allowed_types(implode(',', $newAllowedTypes));

            DataManager:: update($existingAssignment);
        }
    }

    /**
     * Takes an __PHP_Incomplete_Class and casts it to a stdClass object.
     * All properties will be made public in this
     * step.
     *
     * @since 1.1.0
     *
     * @param object $object __PHP_Incomplete_Class
     *
     * @return object
     */
    function fix_object($object)
    {
        // preg_replace_callback handler. Needed to calculate new key-length.
        $fix_key = create_function('$matches', 'return ":" . strlen( $matches[1] ) . ":\"" . $matches[1] . "\"";');

        // 1. Serialize the object to a string.
        $dump = serialize($object);

        // 2. Change class-type to 'stdClass'.
        $dump = preg_replace('/^O:\d+:"[^"]++"/', 'O:8:"stdClass"', $dump);

        // 3. Make private and protected properties public.
        $dump = preg_replace_callback('/:\d+:"\0.*?\0([^"]+)"/', $fix_key, $dump);

        // 4. Unserialize the modified object again.
        return unserialize($dump);
    }

    protected function fixHomeBlockPublications()
    {
        $contentObjectPublicationService = new ContentObjectPublicationService(
            new ContentObjectPublicationRepository(
                new PublicationRepository()
            )
        );
        $formats = array();
        $formats[] = 's:10:"use_object"';

        $conditions = array();

        foreach ($formats as $format)
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Element::class_name(), Element::PROPERTY_CONFIGURATION),
                '*' . $format . '*'
            );
        }

        $condition = new OrCondition($conditions);

        $blocks = \Chamilo\Core\Home\Storage\DataManager::retrieves(
            Block::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        while ($block = $blocks->next_result())
        {
            $useObjectId = $block->getSetting('use_object');
            $contentObjectPublicationService->setOnlyContentObjectForElement($block, $useObjectId);
            
            $block->removeSetting('use_object');
            $block->update();
        }
    }
}