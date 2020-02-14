<template>
    <div id="app">
        <div class="container-fluid rubric-container">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <transition name="fade">
                <div v-if="store.isSaving" class="float-left save-alert">
                    <div class="alert alert-info"
                         role="alert">
                        Processing {{store.queue.pending + store.queue.size}} saves
                    </div>
                </div>
                <div v-else class="alert alert-success save-alert" role="alert">
                    All changes saved!
                </div>
            </transition>

            <div v-if="!store.isLoading">
                <score-rubric-tree-builder class="w-100" v-if="!store.isLoading && !viewFullRubric"/>
                <score-rubric-builder class="w-100" v-if="!store.isLoading && viewFullRubric"></score-rubric-builder>
                <button class="btn btn-default pull-left full-rubric-btn" @click="viewFullRubric=!viewFullRubric">Bekijk
                    Volledige Rubric
                </button>
            </div>
            <div v-else>Spinner! Loading!</div>
        </div>
        </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import ScoreRubricTreeBuilder from "./Components/TreeBuilder/ScoreRubricTreeBuilder.vue";
    import ScoreRubricStore from "./ScoreRubricStore";

    @Component({
        components: {
            ScoreRubricTreeBuilder
        },
    })
    export default class App extends Vue {
        viewFullRubric: boolean = false;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        async mounted() {
            await this.store.fetchData();
        }
    }
</script>

<style>
    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }

    .save-alert {
        width: 20%;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity 1s;
    }

    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */
    {
        opacity: 0;
    }

    .rubric-container {
        display: flex;
        flex-direction: column;
    }

    .w-100 {
        width: 100%;
    }

    .full-rubric-btn {
        width: 200px;
    }

</style>
