<template>
    <div>
        <div class="row">
        <div class="btn-group pull-left" role="group" aria-label="...">
            <button type="button" class="btn btn-default" v-on:click="addCriterium"
            ><i class="fa fa-plus" aria-hidden="true"
                                                             v-b-popover.hover.top="'Voeg criterium toe'">
            </i>Criterium
            </button>
            <button type="button" class="btn btn-default"><i class="fa fa-plus" aria-hidden="true"
                                                             v-b-popover.hover.top="'Voeg category toe'">
            </i>Categorie
            </button>
            <button type="button" class="btn btn-default" v-on:click="addCluster"><i class="fa fa-plus" aria-hidden="true"
                                                             v-b-popover.hover.top="'Voeg cluster toe'">
            </i>Cluster
            </button>
        </div>
        </div>
        <div class="row">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon0">Titel</span>
            <input class="form-control mb-2"
                   v-model="rubric.title"
                   placeholder="Vul hier het criterium in"/>
        </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import Cluster from "../../Domain/Cluster";
    import Criterium from "../../Domain/Criterium";
    import Rubric from "../../Domain/Rubric";
    import Category from "../../Domain/Category";

    @Component({
        components: {}
    })
    export default class RubricNodeBuilder extends Vue {
        @Prop()
        rubric!: Rubric;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        addCriterium(){
            this.store.rubric.addChild(new Criterium('een criterium'))
        }
        addCluster(){
            this.store.rubric.addCluster(new Cluster('een cluster'))
        }
        addCategory(){
            this.store.rubric.addChild(new Category('een categorie'))
        }
    }
</script>

<style scoped>
    .options {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        text-align: left;
    }

    .score-checkbox {
        margin-right: 2px;
    }

    .feedback-text {
        height: 150px;
    }
</style>
