<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface EvaluationServiceBridgeInterface
{
    /**
     *
     * @return boolean
     */
    public function canEditEvaluation();
}