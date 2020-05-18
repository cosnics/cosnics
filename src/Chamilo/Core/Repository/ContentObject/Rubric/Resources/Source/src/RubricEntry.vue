<template>
    <div id="app" class="entry-app">
        <div class="app-header">
            <ul class="app-header-menu">
                <!--<li class="app-header-item"><a @click.prevent="">Entry View</a></li>-->
            </ul>
            <ul class="app-header-tools">
                <li class="app-header-item" :class="{ checked: showDefaultFeedbackFields }"><a role="button" @click.prevent="toggleDefaultFeedbackFields"><i class="check fa" />Feedback</a></li>
                <!--<li class="app-header-item" @click.prevent="showCustomFeedbackFields = !showCustomFeedbackFields"><a>CF</a></li>-->
            </ul>
            <div class="save-state">
                <div v-if="dataConnector && dataConnector.isSaving" class="saving">
                    Processing {{dataConnector.processingSize}} saves...
                </div>
                <div v-else-if="dataConnector" class="saved" role="alert">
                    All changes saved
                </div>
            </div>
        </div>
        <div v-if="rubric" class="rubric">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-entry-view">
                <div class="table-header-wrap">
                    <div class="table-header">
                        <div v-for="level in rubric.levels" class="table-header-title">
                            {{level.title}}
                        </div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{'show-default-feedback': showDefaultFeedbackFields, 'show-custom-feedback': showDefaultFeedbackFields}">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${category.color}`">
                                    <div class="category">
                                        <div class="category-title category-indicator">{{ category.title }}</div>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" class="criterium-list-item" :class="{'show-default-feedback': criterium.showDefaultFeedback, 'show-custom-feedback': criterium.showDefaultFeedback}">
                                                <div class="criterium">
                                                    <div class="criterium-title-header">
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4><div v-if="!showDefaultFeedbackFields" class="btn-more" @click.prevent="criterium.showDefaultFeedback = !criterium.showDefaultFeedback"><i class="check fa"/></div>
                                                    </div>
                                                    <div v-for="level in criterium.choices" class="criterium-level">
                                                        <div class="criterium-level-header" tabindex="0" @keyup.enter.space.stop="selectLevel(criterium, level)" @click="selectLevel(criterium, level)" :class="{ selected: level.isSelected }">
                                                            <div class="criterium-level-title">
                                                                {{level.title}}
                                                            </div>
                                                            <div class="score-number"><!--<i class="check fa"/>-->{{ level.score }}</div>
                                                        </div>
                                                        <div class="default-feedback">
                                                            {{ level.feedback }}
                                                        </div>
                                                    </div>
                                                    <div class="subtotal criterium-total">
                                                        <div class="score-number">{{ criterium.score || 0 }}</div>
                                                    </div>
                                                </div>
                                                <div class="custom-feedback">
                                                    <textarea placeholder="Geef Feedback" v-model="criterium.customFeedback" @input="setFeedback(criterium)"></textarea>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="subtotal category-total">
                                        <div class="category-indicator">Totaal {{ category.title }}:</div><div class="score-wrap"><div class="score-number">{{ getCategoryScore(category) }}</div></div>
                                    </div>
                                </li>
                            </ul>
                            <div class="subtotal cluster-total">
                                <div class="cluster-total-title">Totaal {{ cluster.title }}:</div><div class="score-wrap"><div class="score-number">{{ getClusterScore(cluster) }}</div></div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="subtotal rubric-total">
                    <div class="rubric-total-title">Totaal Rubric:</div><div class="score-wrap"><div class="score-number">{{ getRubricScore() }}</div></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import APIConfiguration from './Connector/APIConfiguration';
    import TreeNode from './Domain/TreeNode';
    import Rubric, {RubricJsonObject} from './Domain/Rubric';
    import Cluster from './Domain/Cluster';
    import Category from './Domain/Category';
    import Criterium from './Domain/Criterium';
    import DataConnector from './Connector/DataConnector';

    interface CriteriumExt {
        choices: any[];
        score: number|null;
        customFeedback: string;
        showDefaultFeedback: false;
    }

    @Component({
        components: {
        },
    })
    export default class RubricEntry extends Vue {
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;
        private showDefaultFeedbackFields = false;
        //private showCustomFeedbackFields = false;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;
        @Prop({type: Object, required: true}) readonly rubricResults!: any;

        toggleDefaultFeedbackFields() {
            this.showDefaultFeedbackFields = !this.showDefaultFeedbackFields;
            if (!this.showDefaultFeedbackFields) {
                this.rubric!.getAllCriteria().forEach(criterium => {
                    const criteriumExt = criterium as unknown as CriteriumExt;
                    criteriumExt.showDefaultFeedback = false;
                });
            }
        }

        ensureCriteriumData(criterium: Criterium) {
            if (!this.rubricResults[criterium.id]) {
                this.rubricResults[criterium.id] = { choice: null, feedback: '' };
            }
        }

        setFeedback(criterium: Criterium) {
            this.ensureCriteriumData(criterium);
            const criteriumExt = criterium as unknown as CriteriumExt;
            this.rubricResults[criterium.id].feedback = criteriumExt.customFeedback;
            console.log(this.rubricResults);
        }

        selectLevel(criterium: Criterium, level: any) {
            this.ensureCriteriumData(criterium);
            const criteriumExt = criterium as unknown as CriteriumExt;
            criteriumExt.score = level.score;
            this.rubricResults[criterium.id].choice = level.choice; // todo: choice has no id yet.
            this.rubricResults[criterium.id].level = level.level.id;
            console.log(this.rubricResults);
            criteriumExt.choices.forEach(choice => {
                choice.isSelected = choice === level;
            });
        }

        getCriteriumScore(criterium: Criterium) : number {
            return (criterium as unknown as CriteriumExt).score || 0;
        }

        getCategoryScore(category: Category) : number {
            return category.criteria.map(criterium => this.getCriteriumScore(criterium)).reduce((v1, v2) => v1 + v2, 0);
        }

        getClusterScore(cluster: Cluster) : number {
            return cluster.categories.map(category => this.getCategoryScore(category)).reduce((v1, v2) => v1 + v2, 0);
        }

        getRubricScore() : number {
            if (!this.rubric) { return 0; }
            return this.rubric.clusters.map(cluster => this.getClusterScore(cluster)).reduce((v1, v2) => v1 + v2, 0);
        }

        private getCriteriaRecursive(treeNode: TreeNode, criteria: Criterium[]) {
            treeNode.children.filter(child => (child instanceof Criterium)).forEach(
                criterium => criteria.push(criterium as Criterium)
            );

            treeNode.children.filter(child => child.hasChildren()).forEach(
                child => this.getCriteriaRecursive(child, criteria)
            )
        }

        get populatedClusters() {
            return this.rubric!.clusters.filter((cluster: Cluster) => {
                const criteria: Criterium[] = [];
                this.getCriteriaRecursive(cluster, criteria);
                return criteria.length !== 0;
            });
        }

        private initScores(rubric: Rubric) {
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt = criterium as unknown as CriteriumExt;
                criteriumExt.choices = [];
                Vue.set(criteriumExt, 'score', null);
                Vue.set(criteriumExt, 'showDefaultFeedback', false);
                Vue.set(criteriumExt, 'customFeedback', '');
                rubric.levels.forEach(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    const isSelected = level.isDefault;
                    if (isSelected) {
                        criteriumExt.score = score;
                    }
                    criteriumExt.choices.push({ title: level.title, feedback: choice?.feedback || '', score, isSelected, choice, level});
                });
            });
        }

        mounted() {
            if (this.rubricData) {
                this.rubric = Rubric.fromJSON(this.rubricData as RubricJsonObject);
                this.initScores(this.rubric);
                // todo: get rubric data id
                this.dataConnector = new DataConnector(this.apiConfig as APIConfiguration, 0, this.version!);
            }
        }
    }
</script>
<style lang="scss">
    .entry-app {
        &#app {
            color: $text-color;
        }

        .rubric-entry-view {
            position: relative;
        }

        .table-header {
            margin-left: 19.8em;
            margin-right: 3.5em;

            .table-header-title {
                flex: 1;
            }
        }

        .criterium-title-header {
            width: 18em;
            min-width: 18em;
        }

        .criterium-level-header {
            &:hover, &:focus, &.selected {
                outline: none;
                border: 1px solid $level-selected-color;
            }

            &.selected {
                &, &:focus {
                    background: $level-selected-color;
                }
                &:hover, &:focus {
                    border-color: $level-selected-color-dark;
                }
            }
        }

        .subtotal {
            .score-wrap {
                width: 3.5em;
            }
        }

        .criterium-total {
            min-width: 3.5em;
        }
    }

    .custom-feedback {
        margin-left: 20em;
        margin-bottom: 1em;
        display: none;

        textarea {
            padding: .2em .4em 0;
            width: 40em;
            height: 2.2em;
            max-width: 100%;
            background: transparent;
            border: 1px solid #d0d0d0;
            border-radius: $border-radius;
            resize: none;

            &:hover, &:focus {
                border: 1px solid #aaa;
                background: white;
                resize: both;

                &::placeholder {
                    color: #666;
                }
            }

            &::placeholder {
                opacity: 1;
                color: #aaa;
            }
        }
    }
    .show-custom-feedback .custom-feedback {
        display: block;
    }
    .show-default-feedback .custom-feedback {
        margin-left: 20em;
    }
</style>
