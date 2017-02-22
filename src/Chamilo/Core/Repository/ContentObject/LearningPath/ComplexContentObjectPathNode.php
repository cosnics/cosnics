<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\learning_path
 */
class ComplexContentObjectPathNode extends \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
{
    const PROPERTY_DATA = 'data';

    /**
     *
     * @var boolean
     */
    private $is_completed;

    /**
     *
     * @var int
     */
    private $average_score;

    /**
     *
     * @var int
     */
    private $total_time;

    /**
     *
     * @return mixed
     */
    public function get_data()
    {
        return $this->get_property(self::PROPERTY_DATA);
    }

    public function set_data($data)
    {
        $this->set_property(self::PROPERTY_DATA, $data);
    }

    /**
     *
     * @return AbstractItemAttempt
     */
    public function get_current_attempt()
    {
        foreach ($this->get_data() as $attempt)
        {
            if ($attempt->get_status() != AbstractItemAttempt::STATUS_COMPLETED &&
                 $attempt->get_status() != AbstractItemAttempt::STATUS_PASSED)
            {
                return $attempt;
            }
        }
        
        return false;
    }

    /**
     *
     * @param AbstractItemAttempt $learning_path_item_attempt
     */
    public function set_current_attempt(AbstractItemAttempt $learning_path_item_attempt)
    {
        $data = $this->get_data();
        $data[] = $learning_path_item_attempt;
        $this->set_data($data);
    }

    /**
     * Cache busting for isCompleted variable
     */
    public function recalculateIsCompleted($recalculateParents = true)
    {
        unset($this->is_completed);

        if($recalculateParents)
        {
            $parents = $this->get_parents(false);
            foreach ($parents as $parent)
            {
                $parent->recalculateIsCompleted(false);
            }
        }
    }

    /**
     *
     * @return boolean
     */
    public function is_completed()
    {
        if (! isset($this->is_completed))
        {
            $this->is_completed = false;
            
            if ($this->get_content_object() instanceof LearningPath)
            {
                $descendants = $this->get_descendants();
                foreach ($descendants as $descendant)
                {
                    if (! $descendant->is_completed())
                    {
                        return false;
                    }
                }
            }
            
            foreach ($this->get_data() as $attempt)
            {
                if ($attempt->get_status() == AbstractItemAttempt::STATUS_COMPLETED ||
                     $attempt->get_status() == AbstractItemAttempt::STATUS_PASSED)
                {
                    $this->is_completed = true;
                    break;
                }
            }
        }
        
        return $this->is_completed;
    }

    /**
     *
     * @return int
     */
    public function get_average_score()
    {
        if (! isset($this->average_score))
        {
            $total_score = 0;
            
            foreach ($this->get_data() as $attempt)
            {
                $total_score += $attempt->get_score();
            }
            
            $this->average_score = round($total_score / count($this->get_data()));
        }
        
        return $this->average_score;
    }

    /**
     *
     * @return int
     */
    public function get_total_time()
    {
        if (! isset($this->total_time))
        {
            $this->total_time = 0;
            
            foreach ($this->get_data() as $attempt)
            {
                $this->total_time += $attempt->get_total_time();
            }
        }
        
        return $this->total_time;
    }
}
