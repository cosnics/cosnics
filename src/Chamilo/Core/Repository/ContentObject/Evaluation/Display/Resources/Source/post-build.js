#!/usr/bin/env node

const path = require('path');
const fs = require('fs');

let evaluationJavascriptFolder = path.resolve(__dirname, '../../../Resources/Javascript/');
let evaluationJavascriptFiles = ['cosnics_evaluation.common.js', 'cosnics_evaluation.umd.js', 'cosnics_evaluation.umd.min.js', 'cosnics_evaluation.common.js.map', 'cosnics_evaluation.umd.js.map', 'cosnics_evaluation.umd.min.js.map'];

let evaluationDesiredWebFolder = path.resolve('__dirname', '../../../../../../../../../../', 'web/Chamilo/Core/Repository/ContentObject/Evaluation/Resources');
let evaluationDesiredJavascriptFolder = path.resolve(evaluationDesiredWebFolder, 'Javascript/');

evaluationJavascriptFiles.forEach(function(evaluationJavascriptFile) {
    let javascriptFile = path.resolve(evaluationJavascriptFolder, evaluationJavascriptFile);

    if (fs.existsSync(javascriptFile)) {
        let javascriptWebFile = path.resolve(evaluationDesiredJavascriptFolder, evaluationJavascriptFile);
        fs.copyFile(javascriptFile, javascriptWebFile, function(err) {
            if (err !== null) {
                console.log(err);
            } else {
                console.log(evaluationJavascriptFile + ' moved to web folder');
            }
        });
    }
});






