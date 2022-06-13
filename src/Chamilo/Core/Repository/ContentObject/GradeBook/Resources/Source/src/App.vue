<template>
  <div id="app">
    <Main :grade-book="gradeBook"></Main>
  </div>
</template>

<script lang="ts">
    import { Component, Vue } from 'vue-property-decorator';
    import Main from './components/Main.vue';
    import GradeBook from './domain/GradeBook';

    const gradeBookObject = {
        gradeItems: [
            { id: 'op1', name: 'Opdracht 1', breadcrumb: 'Leerpaden » Categorie 1 » Leerpad 2 » Hoofdstuk 1' },
            { id: 'op2', name: 'Opdracht 2', breadcrumb: 'Opdrachten » Categorie 1' },
            { id: 'op3', name: 'Opdracht 3', breadcrumb: 'Opdrachten » Categorie 2' },
            { id: 'oe1', name: 'Oefening 1', breadcrumb: 'Oefeningen' },
            { id: 'oe2', name: 'Oefening 2', breadcrumb: 'Leerpaden » Leerpad 5 » Hoofdstuk 4' },
            { id: 'ev1', name: 'Evaluatie 1', breadcrumb: 'Evaluaties' },
            { id: 'ev2', name: 'Evaluatie 2', breadcrumb: 'Leerpaden » Leerpad 3 » Hoofdstuk 2' }
        ],
        gradeColumns: [
            { id: 'gr1', type: 'group', name: 'Groepsscore', subItemIds: ['op1', 'op3'], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 'op2', type: 'item', name: null, weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 'oe1', type: 'item', name: null, weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 'oe2', type: 'item', name: null, weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 'ev1', type: 'item', name: 'Mondeling examen', weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 }
        ],
        categories: [
            { id: 1, color: '#caf1eb', name: 'Categorie 1', itemIds: ['gr1', 'op2', 'oe1'] },
            { id: 2, color: '#ebf2e8', name: 'Categorie 2', itemIds: ['oe2', 'ev1'] }
        ],
        nullCategory: { id: 0, color: 'none', name: '', itemIds: [] },
        resultsData: [
            { id: 1, 'student': 'Student 1', 'results': {'op1': null, 'op3': 20, 'op2': 60, 'oe1': 80, 'oe2': 50, 'ev1': 75, 'ev2': 50} },
            { id: 2, 'student': 'Student 2', 'results': {'op1': 30, 'op3': null, 'op2': 50, 'oe1': 40, 'oe2': 80, 'ev1': 65, 'ev2': 50} },
            { id: 3, 'student': 'Student 3', 'results': {'op1': null, 'op3': 50, 'op2': 30, 'oe1': 70, 'oe2': 80, 'ev1': 95, 'ev2': 50} },
            { id: 4, 'student': 'Student 4', 'results': {'op1': 80, 'op3': null, 'op2': 40, 'oe1': 40, 'oe2': 30, 'ev1': 75, 'ev2': 50} },
            { id: 5, 'student': 'Student 5', 'results': {'op1': null, 'op3': 60, 'op2': 10, 'oe1': 90, 'oe2': 40, 'ev1': 25, 'ev2': 50} }
        ]
    };

    @Component({
        components: { Main }
    })
    export default class App extends Vue {
        private gradeBook = GradeBook.from(gradeBookObject);
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


