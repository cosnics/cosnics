<?php
namespace Chamilo\Core\Repository\ContentObject\File\Filter\Renderer;

use Chamilo\Core\Repository\ContentObject\File\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\FileType;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Render the parameters set via FilterData as conditions
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionFilterRenderer extends \Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer
{

    /*
     * (non-PHPdoc) @see \core\repository\FilterRenderer::render()
     */
    public function render()
    {
        $filter_data = $this->get_filter_data();
        $general_condition = parent::render();
        
        $conditions = [];
        
        if ($general_condition instanceof Condition)
        {
            $conditions[] = $general_condition;
        }
        
        if ($filter_data->has_filter_property(FilterData::FILTER_FILESIZE))
        {
            $format = $filter_data->get_filter_property(FilterData::FILTER_FORMAT);
            $filesize = $filter_data->get_filter_property(FilterData::FILTER_FILESIZE);
            $filesize_bytes = $filesize * pow(1024, $format);
            $compare = $filter_data->get_filter_property(FilterData::FILTER_COMPARE);
            
            if ($compare == ComparisonCondition::EQUAL)
            {
                $equality_conditions = [];
                $equality_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(File::class, File::PROPERTY_FILESIZE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable($filesize_bytes * 0.9));
                $equality_conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(File::class, File::PROPERTY_FILESIZE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable($filesize_bytes * 1.1));
                $conditions[] = new AndCondition($equality_conditions);
            }
            else
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(File::class, File::PROPERTY_FILESIZE),
                    $compare, 
                    new StaticConditionVariable($filesize_bytes));
            }
        }
        
        if ($filter_data->has_filter_property(FilterData::FILTER_EXTENSION))
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(File::class, File::PROPERTY_FILENAME),
                '*.' . $filter_data->get_filter_property(FilterData::FILTER_EXTENSION));
        }
        elseif ($filter_data->has_filter_property(FilterData::FILTER_EXTENSION_TYPE))
        {
            $extension_type = $filter_data->get_filter_property(FilterData::FILTER_EXTENSION_TYPE);
            $extensions = FileType::get_type_extensions($extension_type);
            $extension_conditions = [];
            
            foreach ($extensions as $extension)
            {
                $extension_conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(File::class, File::PROPERTY_FILENAME),
                    '*.' . $extension);
            }
            
            if (count($extension_conditions))
            {
                $conditions[] = new OrCondition($extension_conditions);
            }
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }
}