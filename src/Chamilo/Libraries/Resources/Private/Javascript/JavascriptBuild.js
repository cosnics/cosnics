#!/usr/bin/env node
const path = require('path');
const gulp = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');

const arguments = process.argv;
const configPath = arguments[2];

const root = path.resolve(__dirname, '../../../../../../');

const config = require(path.resolve(root, configPath));

const bowerVendorPath = path.resolve(root, 'vendor/bower_components');
const packagePath = path.resolve(root, 'src', config.package);
const webPackagePath = path.resolve(root, 'web', config.package);

/**
 * Parses the javascript path and replaces the path with a full path based on the variables in the path
 *
 * @param relativePath
 *
 * @returns string
 */
const getFullPath = function (relativePath) {
    relativePath = relativePath.replace('{{ VendorPath }}', bowerVendorPath);
    relativePath = relativePath.replace('{{ PackagePath }}', packagePath);

    return path.resolve(relativePath);
};

/**
 * Parses a configuration and returns the source paths from the configuration for a given package
 *
 * @param configuration
 *
 * @returns {Array}
 */
const getSourcePathsFromConfiguration = function (configuration) {
    var sourcePaths = [];

    if (typeof configuration.input === 'string') {
        sourcePaths.push(getFullPath(configuration.input, packagePath));
    }
    else {
        configuration.input.forEach(
            function (sourceFile) {
                sourcePaths.push(getFullPath(sourceFile, packagePath));
            }
        );
    }

    return sourcePaths;
};

if (config.javascript) {
    config.javascript.forEach(function (javascriptConfiguration) {
        const sourcePaths = getSourcePathsFromConfiguration(javascriptConfiguration);

        const destinationPath = path.resolve(packagePath, javascriptConfiguration.output.path);
        const webDestinationPath = path.resolve(webPackagePath, javascriptConfiguration.output.path);

        console.log(webDestinationPath);
        gulp.src(sourcePaths)
            .pipe(concat(javascriptConfiguration.output.filename))
            .pipe(uglify())
            .on('error', function (err) {
                console.log(err);
            })
            .pipe(gulp.dest(destinationPath))
            .pipe(gulp.dest(webDestinationPath));
    });
}


