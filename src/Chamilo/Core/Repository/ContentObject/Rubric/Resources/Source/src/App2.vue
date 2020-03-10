<template>
    <div id="app">
        <div>
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!--            <transition name="fade">
                <div v-if="store.isSaving" class="float-left save-alert">
                    <div class="alert alert-info"
                         role="alert">
                        Processing {{store.queue.pending + store.queue.size}} saves
                    </div>
                </div>
                <div v-else class="alert alert-success save-alert" role="alert">
                    All changes saved!
                </div>
            </transition> -->

            <div v-if="!store.isLoading">
                <score-rubric-view v-if="!viewFullRubric"/>
                <criterium-details-view v-if="store.selectedCriterium !== null"></criterium-details-view>
            </div>
            <div v-else class="container">
                <p>Loading Rubrics...</p>
                <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import ScoreRubricView from "./Components/View/ScoreRubricView.vue";
    import CriteriumDetailsView from "./Components/View/CriteriumDetailsView.vue";
    import ScoreRubricStore from "./ScoreRubricStore";

    @Component({
        components: {
            ScoreRubricView, CriteriumDetailsView
        },
    })
    export default class App extends Vue {
        viewFullRubric: boolean = false;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
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

    .save-alert {
/*        width: 20%; */
    }

    .fade-enter-active, .fade-leave-active {
/*        transition: opacity 1s; */
    }

    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */
    {
/*        opacity: 0; */
    }

    .rubric-container {
/*        display: flex;
        flex-direction: column; */
    }

    .w-100 {
/*        width: 100%; */
    }

    .full-rubric-btn {
/*        width: 200px; */
    }

</style>
<style>
    #app > div > div {
        display: flex;
        height: 100vh;
        overflow: hidden;
    }
</style>
<style scoped>
    /* Reset elements */
    .container {
        width: 100%;
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
