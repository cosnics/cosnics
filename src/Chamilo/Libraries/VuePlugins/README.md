# vue-cosnics-plugins

## WARNING 

DO NOT RUN NPM INSTALL ON THIS FOLDER. ALL THE DEPENDENCIES ARE INSTALLED INTO THE MAIN FOLDER OF COSNICS. 
THE PACKAGE.JSON FILE IS ONLY HERE FOR NODE TO RESOLVE ITS DEPENDENCIES IN THE CODE. 

## Add additional dependencies

Always add these dependencies to the root packages.json and execute an npm install command there. This will make sure that
there is only one npm dependency tree throught cosnics. 

## Run (serve, build, ...)

* ../../../../node_modules/.bin/vue-cli-service serve
* ../../../../node_modules/.bin/vue-cli-service build --target lib --name cosnics-vue-plugins src/Plugins/plugins.ts

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).

## Plugin readme's are found in src/plugins/{plugin-name}/README.md
