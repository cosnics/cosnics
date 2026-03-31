<?php
namespace Chamilo\Libraries\Service\Utilities;

use Chamilo\Libraries\Architecture\Domain\ActionResult;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Service\Utilities
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionResultRenderer
{
    public function __construct(protected Translator $translator)
    {
    }

    public function getMessage(ActionResult $actionResult): string
    {
        $parameters = [];
        $parameters['ACTION'] = $this->translator->trans('ActionResultAction' . $actionResult->getActionType(), [],
            $actionResult->getContext());

        if ($actionResult->isSingleAction()) {
            $parameters['%Object%'] = $this->translator->trans(
                'ActionResultSingleEntity' . $actionResult->getEntityType(), [], $actionResult->getContext()
            );

            if ($actionResult->hasFailed()) {
                return $this->translator->trans('ActionResultSingleFailureMessage', $parameters);
            }
            else {
                return $this->translator->trans('ActionResultSingleSuccessMessage', $parameters);
            }
        }
        else {
            $parameters['%Object%'] = $this->translator->trans(
                'ActionResultMultipleEntity' . $actionResult->getEntityType(), [], $actionResult->getContext()
            );

            if ($actionResult->hasSucceeded()) {
                return $this->translator->trans('ActionResultMultipleSuccessMessage', $parameters);
            }
            elseif ($actionResult->hasFailedCompletely()) {
                return $this->translator->trans('ActionResultMultipleFailureMessage', $parameters);
            }
            else {
                return $this->translator->trans('ActionResultSomeFailureMessage', $parameters);
            }
        }
    }
}