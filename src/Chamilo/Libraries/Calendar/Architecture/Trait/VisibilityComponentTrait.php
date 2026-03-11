<?php
namespace Chamilo\Libraries\Calendar\Architecture\Trait;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Calendar\Architecture\Interface\VisibilityServiceInterface;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Architecture\Trait
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait VisibilityComponentTrait
{
    public const string PARAM_SOURCE = 'source';

    public function run(?User $currentUser = null): Response
    {
        $source = $this->getRequest()->getFromQueryOrRequest(self::PARAM_SOURCE);

        if ($this->getVisibilityService()->changeVisibility($currentUser->getId(), $source)) {
            return JsonAjaxResult::success();
        }
        else {
            return JsonAjaxResult::error(
                500, $this->getTranslator()->trans(
                'VisibilityNotChanged', [], StringUtilities::LIBRARIES
            )
            );
        }
    }

    abstract public function getRequest(): ChamiloRequest;

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        $postParameters[] = self::PARAM_SOURCE;

        return $postParameters;
    }

    abstract public function getTranslator(): Translator;

    abstract public function getVisibilityService(): VisibilityServiceInterface;
}
