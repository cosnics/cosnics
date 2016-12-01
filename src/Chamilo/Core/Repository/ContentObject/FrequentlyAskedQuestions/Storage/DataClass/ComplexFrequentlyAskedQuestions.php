<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestionsItem\Storage\DataClass\FrequentlyAskedQuestionsItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * Portfolio complex content object item
 * 
 * @package repository\content_object\portfolio$ComplexPortfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ComplexFrequentlyAskedQuestions extends ComplexContentObjectItem
{

    public function get_allowed_types()
    {
        return array(FrequentlyAskedQuestions::class_name(), FrequentlyAskedQuestionsItem::class_name());
    }
}
