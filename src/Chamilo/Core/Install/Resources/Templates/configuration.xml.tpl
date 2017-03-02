<?xml version="1.0" encoding="utf-8"?>
<container
    xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <parameters>
        <!-- Database -->
        <parameter
            key="chamilo.configuration.database"
            type="collection">
            <parameter key="driver">{chamilo.configuration.database.driver}</parameter>
            <parameter key="username">{chamilo.configuration.database.username}</parameter>
            <parameter key="password">{chamilo.configuration.database.password}</parameter>
            <parameter key="host">{chamilo.configuration.database.host}</parameter>
            <parameter key="name">{chamilo.configuration.database.name}</parameter>
            <parameter key="charset">utf8</parameter>
        </parameter>

        <!-- Storage -->
        <parameter
            key="chamilo.configuration.storage"
            type="collection">
            <parameter key="archive_path">{chamilo.configuration.storage.archive_path}</parameter>
            <parameter key="cache_path">{chamilo.configuration.storage.cache_path}</parameter>
            <parameter key="garbage_path">{chamilo.configuration.storage.garbage_path}</parameter>
            <parameter key="hotpotatoes_path">{chamilo.configuration.storage.hotpotatoes_path}</parameter>
            <parameter key="logs_path">{chamilo.configuration.storage.logs_path}</parameter>
            <parameter key="repository_path">{chamilo.configuration.storage.repository_path}</parameter>
            <parameter key="scorm_path">{chamilo.configuration.storage.scorm_path}</parameter>
            <parameter key="temp_path">{chamilo.configuration.storage.temp_path}</parameter>
            <parameter key="userpictures_path">{chamilo.configuration.storage.userpictures_path}</parameter>
        </parameter>

        <parameter key="chamilo.configuration.general.security_key">{chamilo.configuration.general.security_key}</parameter>
        <parameter key="chamilo.configuration.general.hashing_algorithm">{chamilo.configuration.general.hashing_algorithm}</parameter>
        <parameter key="chamilo.configuration.general.install_date">{chamilo.configuration.general.install_date}</parameter>
        <parameter key="chamilo.configuration.general.language">en</parameter>
        <parameter key="chamilo.configuration.general.theme">Aqua</parameter>
        <parameter key="chamilo.configuration.debug.show_errors">{chamilo.configuration.debug.show_errors}</parameter>
        <parameter key="chamilo.configuration.debug.enable_query_cache">{chamilo.configuration.debug.enable_query_cache}</parameter>
        <parameter key="chamilo.configuration.session.session_handler">chamilo</parameter>
        <parameter key="chamilo.configuration.kernel.service">chamilo.libraries.architecture.bootstrap.kernel</parameter>
        <parameter key="chamilo.configuration.version">7</parameter>

        <parameter
            key="chamilo.configuration.error_handling"
            type="collection">
            <parameter
                key="exception_logger"
                type="collection">
                <parameter key="file">Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\FileExceptionLogger</parameter>
            </parameter>
            <parameter
                key="exception_logger_builder"
                type="collection">
                <parameter key="file">Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\FileExceptionLoggerBuilder</parameter>
            </parameter>
        </parameter>
    </parameters>
</container>
