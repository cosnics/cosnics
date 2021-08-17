<template>
  <div id="app">
    <Main :api-config="apiConfig"></Main>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import APIConfig from './connect/APIConfig';
import Main from './components/Main.vue';

@Component({
  components: { Main }
})
export default class App extends Vue {
    readonly apiConfig = APIConfig.from({
        loadStudentsURL: `/api/students`,
        loadStatusDefaultsURL: `/api/status-defaults`,
        loadPresencesURL: `/api/presences`,
        updatePresencesURL: `/api/update-presences`
    });
}
</script>

<style>
body {
    color: #3f4045;
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 13px;
    line-height: 1.42857143;
    margin: 0;
    padding: 0;
    background-color: white;
}

button, input, optgroup, select, textarea {
    margin: 0;
    font: inherit;
    color: inherit;
}

button {
    overflow: visible;
}

button, select {
    text-transform: none;
}

button, html input[type="button"], input[type="reset"], input[type="submit"] {
    -webkit-appearance: button;
    cursor: pointer;
}

input, button, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}

input {
    line-height: normal;
}

.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .08);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .08);
    -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}

.form-control:focus {
    border-color: #66afe9;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .08), 0 0 8px rgba(102, 175, 233, .6);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .08), 0 0 8px rgba(102, 175, 233, .6);
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}

.btn-primary {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}

.btn:active, .btn.active {
    background-image: none;
    outline: 0;
    -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .13);
    box-shadow: inset 0 3px 5px rgba(0, 0, 0, .13);
}

.btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
}

.btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary {
    background-image: none;
}


.btn:focus, .btn:active:focus, .btn.active:focus, .btn.focus, .btn:active.focus, .btn.active.focus {
    outline: 5px auto -webkit-focus-ring-color;
    outline-offset: -2px;
}

.btn:hover, .btn:focus, .btn.focus {
    color: #333;
    text-decoration: none;
}

.btn-primary:focus, .btn-primary.focus {
    color: #fff;
    background-color: #286090;
    border-color: #122b40;
}

.btn:hover, .btn:focus, .btn.focus {
    color: #333;
    text-decoration: none;
}

.btn-primary:hover {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
}

.btn-default {
    color: #333;
    background-color: #f7f7f7;
    border-color: #ccc;
}

.btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {
    color: #333;
    background-color: #dedede;
    border-color: #adadad;
}

.btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {
    background-image: none;
}

.btn-default:focus, .btn-default.focus {
    color: #333;
    background-color: #dedede;
    border-color: #8c8c8c;
}

.btn-default:hover {
    color: #333;
    background-color: #dedede;
    border-color: #adadad;
}

.btn-sm, .btn-group-sm > .btn {
    padding: 5px 10px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}

button.btn {
    text-shadow: none;
}

</style>


