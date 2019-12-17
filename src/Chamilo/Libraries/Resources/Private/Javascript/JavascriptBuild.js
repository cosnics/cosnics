#!/usr/bin/env node

/**
 * Chamilo script to concatenate and minimize javascript and css files based on a given config. Resources should be processed during development using this script after which
 * they will be placed in the resources folder and automatically be moved to the web folder once the process:resources script is being run in production, thus eliminating
 * the need to "build" the resources while in production.
 *
 * Since this script works by configuration this gives us a lot more power over which resources we combine together.
 *
 * Color Reference
 *
 * Reset = "\x1b[0m"
 * Bright = "\x1b[1m"
 * Dim = "\x1b[2m"
 * Underscore = "\x1b[4m"
 * Blink = "\x1b[5m"
 * Reverse = "\x1b[7m"
 * Hidden = "\x1b[8m"
 *
 * FgBlack = "\x1b[30m"
 * FgRed = "\x1b[31m"
 * FgGreen = "\x1b[32m"
 * FgYellow = "\x1b[33m"
 * FgBlue = "\x1b[34m"
 * FgMagenta = "\x1b[35m"
 * FgCyan = "\x1b[36m"
 * FgWhite = "\x1b[37m"
 *
 * BgBlack = "\x1b[40m"
 * BgRed = "\x1b[41m"
 * BgGreen = "\x1b[42m"
 * BgYellow = "\x1b[43m"
 * BgBlue = "\x1b[44m"
 * BgMagenta = "\x1b[45m"
 * BgCyan = "\x1b[46m"
 * BgWhite = "\x1b[47m"
 */

const path = require('path');
const gulp = require('gulp');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const fs = require('fs');

const arguments = process.argv;
const configPath = arguments[2];

if(!configPath || !fs.existsSync(configPath))
{
    console.log('\x1b[41m%s\x1b[0m', 'No valid config file selected', '\n');
    process.exit();
}

const root = path.resolve(__dirname, '../../../../../../');
const config = require(path.resolve(root, configPath));

const bowerVendorPath = path.resolve(root, 'vendor/bower_components');
const nodeModulesPath = path.resolve(root, 'node_modules');
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
    relativePath = relativePath.replace('{{ NodeModulesPath }}', nodeModulesPath);

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
    const sourcePaths = [];

    if (typeof configuration.input === 'string') {
        sourcePaths.push(getFullPath(configuration.input));
    }
    else {
        configuration.input.forEach(
            function (sourceFile) {
                sourcePaths.push(getFullPath(sourceFile));
            }
        );
    }

    return sourcePaths;
};

const buildJavascript = function() {
    return new Promise(function(resolve, reject)
    {
        if (!config.javascript) {
            resolve();
        }

        const javascriptCount = config.javascript.length;
        let finishedJavascriptFiles = 0;

        console.log('\x1b[33m%s\x1b[0m', 'Processing Javascript');
        config.javascript.forEach(function (javascriptConfiguration) {
            const sourcePaths = getSourcePathsFromConfiguration(javascriptConfiguration);

            const destinationPath = path.resolve(packagePath, javascriptConfiguration.output.path);
            const webDestinationPath = path.resolve(webPackagePath, javascriptConfiguration.output.path);

            console.log('\x1b[34m%s\x1b[0m', 'Processing ' + javascriptConfiguration.output.filename);
            gulp.src(sourcePaths)
                .pipe(concat(javascriptConfiguration.output.filename))
                .pipe(uglify())
                .pipe(gulp.dest(destinationPath))
                .pipe(gulp.dest(webDestinationPath))
                .on('error', function (err) {
                    console.log(err);
                    reject();
                })
                .on('end', function () {
                    console.log('\x1b[32m%s\x1b[0m', '[DONE] ' + javascriptConfiguration.output.filename);
                    finishedJavascriptFiles++;

                    if(finishedJavascriptFiles >= javascriptCount) {
                        console.log('');
                        resolve();
                    }
                });
        });
    });
};

const buildCss = function() {
    return new Promise(function(resolve, reject) {
        if (!config.css) {
            resolve();
        }

        console.log('\x1b[33m%s\x1b[0m', 'Processing CSS');

        const cssCount = config.css.length;
        let finishedCssFilesCount = 0;

        config.css.forEach(function (cssConfiguration) {
            const sourcePaths = getSourcePathsFromConfiguration(cssConfiguration);

            const destinationPath = path.resolve(packagePath, cssConfiguration.output.path);
            const webDestinationPath = path.resolve(webPackagePath, cssConfiguration.output.path);

            console.log('\x1b[34m%s\x1b[0m', 'Processing ' + cssConfiguration.output.filename);

            gulp.src(sourcePaths)
                .pipe(concat(cssConfiguration.output.filename))
                .pipe(cleanCSS())
                .pipe(gulp.dest(destinationPath))
                .pipe(gulp.dest(webDestinationPath))
                .on('error', function (err) {
                    console.log(err);
                    reject();
                })
                .on('end', function () {
                    console.log('\x1b[32m%s\x1b[0m', '[DONE] ' + cssConfiguration.output.filename);
                    finishedCssFilesCount++;

                    if(finishedCssFilesCount >= cssCount) {
                        console.log('');
                        resolve();
                    }
                });
        });
    });
};

buildJavascript().then(function() {
    buildCss();
});
