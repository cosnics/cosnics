<?php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Chamilo\Libraries\Filesystem\Factory\HtmlPurifierFactory;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Session\Factory\PdoSessionHandlerFactory;
use Chamilo\Libraries\Protocol\Session\Factory\SessionFactory;
use Chamilo\Libraries\UserInterface\Translation\Factory\TranslatorFactory;
use HTMLPurifier;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Yaml\Parser;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public()->autowire()->autoconfigure();

    $services->set(ChamiloRequest::class)->factory([ChamiloRequest::class, 'createFromGlobals']);

    $services->set(Translator::class)->args(['%cosnics.libraries.userInterface.translation.default%'])->factory(
        [service(TranslatorFactory::class), 'createTranslator']
    );

    $services->alias(SessionInterface::class, Session::class);
    $services->set(Session::class)->factory([service(SessionFactory::class), 'getSession']);
    $services->set(NativeSessionStorage::class)->args(['$handler' => service(PdoSessionHandler::class)]);
    $services->set(PdoSessionHandler::class)->factory([service(PdoSessionHandlerFactory::class), 'getPdoSessionHandler']
    );

    $services->set(HTMLPurifier::class, HTMLPurifier::class)->factory(
        [service(HtmlPurifierFactory::class), 'buildHtmlPurifier']
    );

    $services->set(ValidatorBuilder::class);
    $services->set(Logger::class)->args(['Chamilo']);
    $services->set(Processor::class);

    $services->set(EventDispatcher::class);
    $services->alias(EventDispatcherInterface::class, EventDispatcher::class);

    $services->set(Parser::class);
    $services->set(Filesystem::class);
    $services->set(Finder::class);
    $services->set(ObjectNormalizer::class);

    $services->set(XmlEncoder::class);
    $services->set(JsonEncoder::class);
    $services->set(Serializer::class)->args([
        '$normalizers' => [service(ObjectNormalizer::class)],
        '$encoders' => [service(XmlEncoder::class), service(JsonEncoder::class)],
    ]);
};
