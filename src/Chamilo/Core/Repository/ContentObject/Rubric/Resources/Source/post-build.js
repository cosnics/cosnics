#!/usr/bin/env node

const path = require('path');
const fs = require('fs');

let rubricCssFile = path.resolve(__dirname, '../Javascript/Builder/cosnics-rubric.css');
let rubricJavascriptFolder = path.resolve(__dirname, '../Javascript/Builder/');
let rubricJavascriptFiles = ['cosnics-rubric.common.js', 'cosnics-rubric.umd.js', 'cosnics-rubric.umd.min.js'];

let rubricCssDesiredFile = path.resolve(__dirname, '../Css/Aqua/cosnics-rubric.css');

let rubricDesiredWebFolder = path.resolve('__dirname', '../../../../../../../../../', 'web/Chamilo/Core/Repository/ContentObject/Rubric/Resources');
let rubricDesiredJavascriptFolder = path.resolve(rubricDesiredWebFolder, 'Javascript/Builder/');
let rubricDesiredCssWebFile = path.resolve(rubricDesiredWebFolder, 'Css/Aqua/cosnics-rubric.css');

if(fs.existsSync(rubricCssFile))
{
    // if(fs.existsSync(rubricCssDesiredFile))
    // {
    //     console.log(rubricCssDesiredFile);
    //     fs.unlink(rubricCssDesiredFile, function() {});
    // }

    fs.rename(rubricCssFile, rubricCssDesiredFile, function(err) {
        console.log('Rubric css moved');
        if(err !== null) {
            console.log(err);
        }
    });

    fs.copyFile(rubricCssDesiredFile, rubricDesiredCssWebFile, function(err) {
        console.log('Rubric css moved to web folder');
        if(err !== null) {
            console.log(err);
        }
    })
}

rubricJavascriptFiles.forEach(function(rubricJavascriptFile) {
    let javascriptFile = path.resolve(rubricJavascriptFolder, rubricJavascriptFile);

    if(fs.existsSync(javascriptFile))
    {
        let javascriptWebFile = path.resolve(rubricDesiredJavascriptFolder, rubricJavascriptFile);
        fs.copyFile(javascriptFile, javascriptWebFile, function(err) {
            console.log(rubricJavascriptFile + ' moved to web folder');
            if(err !== null) {
                console.log(err);
            }
        });
    }
});






