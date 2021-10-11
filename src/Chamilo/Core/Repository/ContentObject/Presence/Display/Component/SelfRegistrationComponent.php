<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class SelfRegistrationComponent extends Manager
{
    public function run()
    {
        return $this->getTwig()->render(
            Manager::context() . ':SelfRegistration.html.twig', $this->getTemplateProperties()
        );
    }

    /**
     * @return array
     */
    protected function getTemplateProperties(): array
    {
        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'LANGUAGE' => $this->getTranslator()->getLocale()
        ];
    }
}