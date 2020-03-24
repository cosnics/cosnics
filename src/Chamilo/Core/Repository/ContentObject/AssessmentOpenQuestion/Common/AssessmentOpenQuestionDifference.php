<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AssessmentOpenQuestionDifference extends ContentObjectDifference
{

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $propertyName
     *
     * @return string[]
     */
    public function getVisualAdditionalPropertyValue(ContentObject $contentObject, string $propertyName)
    {
        switch ($propertyName)
        {
            case AssessmentOpenQuestion::PROPERTY_QUESTION_TYPE:
                $questionTypes = $contentObject->get_types();
                $content = $questionTypes[$contentObject->get_question_type()];
                break;
            case AssessmentOpenQuestion::PROPERTY_HINT:
                $content = $this->processHtmlPropertyValue($contentObject->get_hint());
                break;
            case AssessmentOpenQuestion::PROPERTY_FEEDBACK:
                $content = $this->processHtmlPropertyValue($contentObject->get_feedback());
                break;
            default:
                $content = parent::getVisualAdditionalPropertyValue($contentObject, $propertyName);
        }

        return explode(PHP_EOL, $content);
    }
}
