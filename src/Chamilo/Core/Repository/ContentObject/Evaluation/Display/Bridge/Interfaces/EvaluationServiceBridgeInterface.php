<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces;

use Chamilo\Libraries\Architecture\ContextIdentifier;

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

    /**
     *
     * @return integer
     */
    public function getCurrentEntityType();

    /**
     *
     * @return ContextIdentifier
     */
    public function getContextIdentifier();
}