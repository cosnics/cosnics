<?php

use Chamilo\Libraries\Authentication\Ldap\LdapTest;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

$autoloader = require __DIR__ . '/../vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$ldapTest = new LdapTest();
$ldapTest->run();