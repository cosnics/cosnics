<template>
    <div id="app">
        <div v-if="showHeaderFooter" class="chamilo-header"><div class="start"></div><div class="middle"></div><div class="end"></div></div>
        <div class="header">
            <ul class="menu">
                <li><a @click.prevent="content = 'rubric'">Edit Rubric</a></li>
                <li><a @click.prevent="content = 'levels'">Edit Niveaus</a></li>
            </ul>
            <div class="save-state">
                <div v-if="store.isSaving" class="saving">
                    Processing {{store.queue.pending + store.queue.size}} saves...
                </div>
                <div v-else class="saved" role="alert">
                    All changes saved
                </div>
            </div>
        </div>
        <div class="rubrics">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div v-if="!store.isLoading">
                <levels-view v-if="content === 'levels'"></levels-view>
                <score-rubric-view v-if="content === 'rubric'" :selected-criterium="selectedCriterium" @criterium-selected="selectCriterium" />
                <criterium-details-view v-if="content === 'rubric'" :criterium="selectedCriterium" @close="selectCriterium(null)"></criterium-details-view>
            </div>
            <div v-else class="container">
                <p>Loading Rubrics...</p>
                <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
            </div>
        </div>
        <div v-if="showHeaderFooter" class="chamilo-footer"><div class="start"></div><div class="end"></div></div>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import ScoreRubricView from "./Components/View/ScoreRubricView.vue";
    import CriteriumDetailsView from "./Components/View/CriteriumDetailsView.vue";
    import ScoreRubricStore from "./ScoreRubricStore";
    import Criterium from "./Domain/Criterium";
    import LevelsView from "./Components/View/LevelsView.vue";

    @Component({
        components: {
            ScoreRubricView, CriteriumDetailsView, LevelsView
        },
    })
    export default class App extends Vue {
        private selectedCriterium: Criterium|null = null;
        private showHeaderFooter: boolean = true;
        private content: string = 'rubric';

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        selectCriterium(criterium: Criterium|null) {
            this.selectedCriterium = criterium;
        }

        async created() {
            await this.store.fetchData();
        }
    }
</script>

<style>
    #app {
/*        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px; */
    }
</style>
<style>
    #app {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    #app ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    #app ::-webkit-scrollbar-track {
        box-shadow: inset 0 0 2px grey;
        background-color: hsla(200, 50%, 40%, .05);
        border-radius: 10px;
    }
    #app ::-webkit-scrollbar-thumb {
        background-color: hsla(200, 50%, 40%, .15);
        border-radius: 10px;
    }
    #app ::-webkit-scrollbar-thumb:hover {
        background-color: hsla(220, 70%, 40%, .20);
    }
</style>
<style scoped>
    .chamilo-header {
        height: 112px;
        display: flex;
        flex-direction: row!important;
        overflow: hidden;
    }
    .chamilo-header .start {
        background-image: url("/images/chamilo-start.png");
        width: 227px;
    }
    .chamilo-header .middle {
        background-image: url("/images/chamilo-middle.png");
        flex: 1;
    }
    .chamilo-header .end {
        background-image: url("/images/chamilo-end.png");
        width: 713px;
    }
    .chamilo-footer {
        height: 48px;
        display: flex;
        flex-direction: row!important;
    }
    .chamilo-footer .start {
        background-image: url("/images/chamilo-footer-start.png");
        height: 48px!important;
        flex: 1;
    }
    .chamilo-footer .end {
        background-image: url("/images/chamilo-footer-end.png");
        background-repeat: no-repeat;
        height: 48px!important;
        width: 685px;
    }
    .header {
        display: flex;
        background-color: hsla(165, 5%, 90%, 1);
        border-bottom: 1px solid hsl(199, 39%, 73%);
        border-top: 1px solid hsl(199, 39%, 73%);
        align-items: center;
        justify-content: space-between;
    }
    ul.menu {
        list-style: none; display: flex;
        padding: 8px; margin-bottom: 0;
    }
    ul.menu li {
        margin-left: 8px; margin-right: 4px;
        cursor: pointer;
    }
    ul.menu li:first-child {
        margin-left: 20px;
    }
    ul.menu li a {
        text-decoration: none;
    }
    .save-state {
        margin-right: 20px;
        color: #337ab7;
        width: 144px;
    }
    .save-state .saved {
        opacity: 0.6;
    }
    .rubrics {
        flex: 1;
        display: flex;
    }
    .rubrics > div {
        width: 100%;
    }
    /* Reset elements */
    .container {
        width: 100%;
        flex: 1;
        padding-top: 20px;
        background-color: hsla(165, 5%, 90%, 1);
        display: flex;
        flex-direction: column;
        margin: 0 auto;
    }
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }
    .lds-ellipsis div {
        position: absolute;
        top: 13px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: hsla(190, 40%, 45%, 1);
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }
    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }
    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }
    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }
    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }
    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }
        100% {
            transform: scale(1);
        }
    }
    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }
        100% {
            transform: scale(0);
        }
    }
    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(24px, 0);
        }
    }
    </style>
