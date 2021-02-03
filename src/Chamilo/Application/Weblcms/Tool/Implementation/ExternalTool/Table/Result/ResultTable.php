<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultTable extends RecordTable
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTableParameters
     */
    protected $resultTableParameters;

    /**
     * ResultTable constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTableParameters $resultTableParameters
     *
     * @throws \Exception
     */
    public function __construct(Application $component, ResultTableParameters $resultTableParameters)
    {
        parent::__construct($component);

        $this->resultTableParameters = $resultTableParameters;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTableParameters
     */
    public function getResultTableParameters()
    {
        return $this->resultTableParameters;
    }

}