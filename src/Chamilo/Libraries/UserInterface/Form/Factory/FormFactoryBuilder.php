<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\DependencyInjection\Service\DependencyInjectionContainerBuilder;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\DependencyInjection\DependencyInjectionExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormFactoryBuilder
{
    protected ChamiloRequest $request;

    public function __construct(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    public function createFormFactory(): FormFactoryInterface
    {
        $requestStack = new RequestStack([$this->getRequest()]);

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($requestStack);
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $formFactoryBuilder = Forms::createFormFactoryBuilder();

        $formFactoryBuilder->addExtension(new HttpFoundationExtension());
        $formFactoryBuilder->addExtension(new CsrfExtension($csrfManager));
        $formFactoryBuilder->addExtension(
            new DependencyInjectionExtension(
                DependencyInjectionContainerBuilder::getInstance()->createContainer(), [], []
            )
        );

        return $formFactoryBuilder->getFormFactory();
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }
}