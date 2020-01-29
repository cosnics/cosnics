<template>
    <b-container fluid>
        <h1>Configureer </h1>
        <Configuration class="configuration"></Configuration>
        <h1>Bepaal niveau's</h1>
        <LevelsTable/>

        <br/>
        <h1>Rubric</h1>
        <table class="table table-bordered rubric-table" v-for="(cluster, clusterIndex) in store.rubric.clusters">
            <tr class="cluster-header">
                <td colspan="2" class="cluster-title">
                    <collapse :collapsed="cluster.collapsed" v-on:toggle-collapse="cluster.toggleCollapsed()">
                        <slot>
                            <div class="d-flex cluster-title-slot  w-100">
                                <div class="d-flex w-100 cluster-title-slot-item">
                                <textarea class="form-control text-area-level-description font-weight-bold ml-2"
                                          v-model="cluster.title"
                                          placeholder="Vul aan"></textarea>
                                    <MoveDeleteBar :index="clusterIndex" :max-index="store.rubric.clusters.length - 1"
                                                   v-on:move-up="store.rubric.moveClusterUp(cluster)"
                                                   v-on:move-down="store.rubric.moveClusterDown(cluster)"
                                                   v-on:remove="store.rubric.removeCluster(cluster)">
                                    </MoveDeleteBar>
                                </div>

                                <b-button variant="primary" class="ml-2 mt-1">Koppel leerdoelstelling</b-button>
                            </div>
                        </slot>
                    </collapse>

                </td>
                <td v-for="level in store.rubric.levels" class="score-title">
                    <i v-if="level.description" class="fa fa-info-circle mr-2" aria-hidden="true"
                       v-b-popover.hover.top="level.description"></i>{{ level.title | capitalize }}
                </td>
            </tr>

            <tbody v-if="!cluster.collapsed" v-for="category in cluster.categories">
            <tr v-for="(criterium, index) in category.criteria" class="category-tr">
                <td v-if="index === 0" :rowspan="category.criteria.length + 1" class="category-td p-0">
                    <div class="category">
                        <div :class="'category-' + category.color"></div>
                        <div class="category-title">{{ category.title }}</div>
                    </div>
                </td>
                <td class="criteria">
                    <div class="criterium-title-container">
                        {{ criterium.title }}
                        <b-input-group v-if="store.rubric.useScores" prepend="Gewicht: " append="%" class="weight-input-group weight">
                            <input type="number" name="Score" class="form-control "
                                   placeholder="Gewicht %" min="0" max="100" maxlength="3"
                                   v-model="criterium.weight">
                        </b-input-group>
                        <b-button variant="primary" class="ml-2 mt-1">Koppel leerdoelstelling</b-button>

                    </div>
                </td>
                <td v-for="level in store.rubric.levels" class="score">
                    <textarea class="form-control text-area-level-description mb-2 feedback-text"
                              v-model="store.rubric.getChoice(criterium, level).feedback"
                              placeholder="Vul aan"></textarea>
                    <div v-if="store.rubric.useScores">
                        {{store.rubric.getChoiceScore(criterium, level)}} punten
                        <b-button size="sm">Vaste score</b-button>
                    </div>
                    <b-checkbox>Melding in rapport</b-checkbox>
                </td>


            </tr>
            <tr>
                <td :colspan="store.rubric.levels.length + 1">
                    <b-button variant="primary" class="w-100">Voeg vrij criterium of leerdoelstelling toe</b-button>
                </td>
            </tr>
            </tbody>
            <tbody  v-if="!cluster.collapsed">
            <tr>
                <td :colspan="2 + store.rubric.levels.length">
                    <b-button  variant="primary" class="w-100">Voeg Categorie toe</b-button>
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td :colspan="store.rubric.levels.length + 2" class="cluster-score">
                    <h5 class="">Cluster rapport</h5>
                    <p>Maxmimum score: </p>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="row mb-4">
            <div class="col-12">

                <b-button variant="primary" size="lg" class="pull-left w-100">Voeg nieuwe cluster toe</b-button>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Rubric Rapport</h5></div>
                    <div class="card-body">
                        <p class="pull-left">Maximum score: </p>
                    </div>
                </div>
            </div>
        </div>
    </b-container>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import LevelsTable from "@/Plugins/ScoreRubric/Components/LevelsTable.vue";
    import ScoreRubricStore from "@/Plugins/ScoreRubric/ScoreRubricStore";
    import Configuration from "@/Plugins/ScoreRubric/Components/Configuration.vue";
    import Collapse from "@/Plugins/ScoreRubric/Components/Collapse.vue";
    import MoveDeleteBar from "@/Plugins/ScoreRubric/Components/MoveDeleteBar.vue";

    @Component({
        components: {MoveDeleteBar, Collapse, Configuration, LevelsTable},
        filters: {
            capitalize(value: string) {
                if (!value) {
                    return "";
                }

                return value.toUpperCase();
            }
        }
    })
    export default class ScoreRubricBuilder extends Vue {
        public store: ScoreRubricStore = this.$root.$data.store;
    }
    //todo replace border with padding
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    .rubric-table {
        margin-bottom: 30px;
    }

    .cluster-header {
        background-color: #1e2940;
        color: white;
        font-weight: bold;
    }

    .cluster-title {
        height: 100%;
        width: 30%;
    }

    .cluster-title-slot {
        flex-direction: column;
        justify-content: space-between;
    }

    .cluster-title-slot-item {
        flex-basis: content;
        align-self: flex-start;
    }

    .category-tr {
        height: 100%;
    }

    .category-td {
        height: 100%;
    }

    .category-red {
        background: red;
        width: 10px;
    }

    .category-green {
        background: green;
        width: 10px;

    }

    .category-title {
        padding: 0.75rem;
        height: 100%;
    }

    .category {
        display: flex;
        justify-content: stretch;
        padding: 0;
        height: 100%;
    }

    .score-title, .score {
        text-align: center;
    }

    .criteria {
        font-weight: bold;
    }

    .criterium-title-container {
        display: flex;
        flex-direction: column;
    }

    .configuration {
        width: 50%;
        margin-bottom: 10px;
    }

    .cluster-score {
        text-align: left;
    }

    .weight {
        width: 70%;
    }

    .feedback-text {
        height: 200px;
    }
</style>
