<template>
    <b-container fluid>
        <div class="">
            <table class="rubric-table table table-condensed table-striped" v-for="(cluster) in store.rubric.clusters">
                <caption>
                    <collapse :collapsed="cluster.collapsed" v-on:toggle-collapse="cluster.toggleCollapsed()">
                        <slot>
                            <b-input
                                    v-model="cluster.title"
                                    placeholder="Vul hier de titel van je cluster in"></b-input>
                        </slot>
                    </collapse>
                </caption>
                <thead v-if="!cluster.collapsed">
                <tr class="">
                    <th scope="col">
                        Categorie
                    </th>
                    <th scope="col">
                        Criterium
                    </th>
                    <th th scope="col" v-for="level in store.rubric.levels">
                        <i v-if="level.description" class="fa fa-info-circle mr-2" aria-hidden="true"
                           v-b-popover.hover.top="level.description"></i>{{ level.title }}
                    </th>
                </tr>
                </thead>
                <tbody v-if="!cluster.collapsed" v-for="category in cluster.categories">
                <tr v-for="(criterium, index) in category.criteria" class="category-tr">
                    <td v-if="index === 0" :rowspan="category.criteria.length + 1" class="category-td" :style="categoryColor(category)">
                        <div class="spacer"></div>
                        <div class="category-row">
                            {{category.title}}
                        </div>
                        <div class="spacer"></div>
                    </td>
                    <td class="criteria">
                        <div class="">
                            <textarea class="form-control text-area-level-description mb-2 feedback-text"
                                      v-model="criterium.title"
                                      placeholder="Vul hier het criterium in"></textarea>
                            <b-input-group v-if="store.rubric.useScores" prepend="Gewicht: " append="%"
                                           class="weight-input-group weight">
                                <input type="number" name="Score" class="form-control "
                                       placeholder="Gewicht %" min="0" max="100" maxlength="3"
                                       v-model="criterium.weight">
                            </b-input-group>

                        </div>
                    </td>
                    <td v-for="level in store.rubric.levels" class="score">
                    <textarea class="form-control text-area-level-description mb-2 feedback-text"
                              v-model="store.rubric.getChoice(criterium, level).feedback"
                              placeholder="Vul aan"></textarea>
                        <div v-if="store.rubric.useScores">
                            {{store.rubric.getChoiceScore(criterium, level)}} punten
                            <!--b-button size="sm">Vaste score</b-button!-->
                        </div>
                        <!--b-checkbox>Melding in rapport</b-checkbox!-->
                    </td>
                </tr>
                <tr>
                    <td :colspan="store.rubric.levels.length + 1">
                        <button class="btn btn-sm btn-primary ml-1 pull-left"
                                v-on:click="category.addCriterium(getDefaultCriterium())">
                            <i class="fa fa-plus" aria-hidden="true"
                               v-b-popover.hover.top="'Voeg criterium toe'"></i> Voeg criterium toe
                        </button>
                    </td>
                </tr>
                </tbody>
                <tbody v-if="!cluster.collapsed">
                <tr>
                    <td>
                        <button class="btn btn-sm btn-primary pull-left"
                                v-on:click="cluster.addCategory(getDefaultCategory())">
                            <i class="fa fa-plus" aria-hidden="true"></i> Voeg Categorie toe
                        </button>
                    </td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td :colspan="store.rubric.levels.length + 2" class="cluster-score">
                        <p>Maximum score cluster: 10 punten</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="row mb-4 add-cluster-row">
            <div class="col-12">
                <button class="btn btn-sm btn-primary ml-1 pull-left"
                        v-on:click="store.rubric.addCluster(getDefaultCluster())">
                    <i class="fa fa-plus" aria-hidden="true"></i> Voeg nieuwe cluster toe
                </button>
            </div>
        </div>

        <div class="row mb-4 max-rubric-score-row">
            <div class="col-12">
                <p class="pull-left">Maximum score rubric: 20 punten </p>
            </div>
        </div>


    </b-container>
</template>

<script lang="ts">
    import {Component, Vue} from "vue-property-decorator";
    import LevelsTable from "./LevelsTable.vue";
    import ScoreRubricStore from "../ScoreRubricStore";
    import Configuration from "./Configuration.vue";
    import Collapse from "./Collapse.vue";
    import MoveDeleteBar from "./MoveDeleteBar.vue";
    import Cluster from "../Domain/Cluster";
    import Category from "../Domain/Category";
    import Criterium from "../Domain/Criterium";

    @Component({
        name: 'score-rubric-builder',
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

        getDefaultCluster() {
            let cluster = new Cluster("");
            cluster.addCategory(this.getDefaultCategory());

            return cluster;
        }

        getDefaultCategory() {
            let category = new Category("Categorie 1");
            category.color = "blue";
            category.addCriterium(this.getDefaultCriterium());

            return category;
        }

        getDefaultCriterium() {
            return new Criterium("");
        }

        categoryColor(category: Category) {
            return {
                'background-color': 'rgba(' + category.rgbColor(0.7) + ')',
                color: 'white'
            };
        }
    }
    //todo replace border with padding
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    .rubric-table {
        margin-bottom: 0px;
    }

    .spacer {
        height: 45%;
        width: 100%;
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

    .category-td {
        height: 100%;
        padding: 0;
        width: 100px;
        position: relative;
    }

    .category-row {
        text-align: center;
        height: 100%;
        width: 100%;
        color: white;
        font-size: 20px;
    }

    .category-title {
        padding: 0.75rem;
        height: 100%;
    }

    .category {
        display: flex;
        padding: 0;
        height: 100%;
        position: absolute;
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
        background-color: white;
    }

    .weight {
        width: 70%;
    }

    .feedback-text {
        height: 100px;
    }

    .w-100 {
        width: 100%;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .ml-1 {
        margin-left: 0.25rem;
    }

    .ml-2 {
        margin-left: 0.5rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }

    .rubric-table tr.cluster-header td {
        padding: 0.5rem;
    }

    .d-flex {
        display: flex;
    }

    .input-group-prepend,
    .input-group-append {
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #555;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .input-group-prepend, .input-group-btn,
    .input-group-append, .input-group-btn {
        width: 1%;
        white-space: nowrap;
        vertical-align: middle;
    }

    .input-group .form-control, .input-group-prepend, .input-group-btn,
    .input-group .form-control, .input-group-append, .input-group-btn {
        display: table-cell;
    }

    .input-group-prepend:first-child {
        border-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-append:last-child {
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-secondary:focus {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
    }

    .text-area-cluster-title {
        width: auto;
        flex-grow: 1;
    }

    .add-cluster-row {
        margin-left: 1px;
        margin-bottom: 8px;
    }

    .max-rubric-score-row {
        padding-top: 2px;
        margin-left: 4px;
        border-top: 1px solid #ddd;
    }
</style>
