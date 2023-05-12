<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Chat\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Chat\Manager;
use Symfony\Component\HttpFoundation\Response;

class ViewerComponent extends Manager
{

    public function run()
    {

        $html = [];

        $html[] = $this->render_header();
        $html[] = '<div class="alert alert-warning">' .
            $this->getTranslator()->trans('NoLongerSupported', [], Manager::CONTEXT) . '</div>';
        $html[] = $this->render_footer();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
