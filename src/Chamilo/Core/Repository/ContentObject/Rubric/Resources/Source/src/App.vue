<template>
    <div id="app">
        <div class="container-fluid">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <transition name="fade">
                <div v-if="store.isSaving" class="float-left save-alert">
                    <div v-if="store.queue.pending !== 0 || store.queue.size !==0" class="alert alert-info"
                         role="alert">
                            Processing {{store.queue.pending + store.queue.size}} saves
                    </div>
                    <div v-else class="alert alert-success" role="alert">
                            All changes saved!
                    </div>
                </div>
            </transition>
            <score-rubric-tree-builder v-if="!store.isLoading"/>
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

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        async mounted() {
            await this.store.fetchData();
            await this.store.save();
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
</style>
