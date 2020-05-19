<template>
    <div id="app" class="builder-full-app">
        <div class="app-header">
            <ul class="app-header-menu">
                <!--<li class="app-header-item"><a @click.prevent="">Entry View</a></li>-->
            </ul>
            <ul class="app-header-tools">
                <li class="app-header-item" style="visibility:hidden"><a>...</a></li>
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
            <div class="rubric-builder-full-view">
                <div class="table-header-wrap">
                    <div class="table-header">
                        <div v-for="level in rubric.levels" class="table-header-title">
                            {{level.title}}
                        </div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{'show-default-feedback': true }">
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
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4>
                                                    </div>
                                                    <div v-for="level in criterium.choices" class="criterium-level">
                                                        <div class="criterium-level-header">
                                                            <div class="criterium-level-title">
                                                                {{level.title}}
                                                            </div>
                                                            <div class="score-number"><!--<i class="check fa"/>-->{{ level.score }}</div>
                                                        </div>
                                                        <div class="default-feedback">
                                                            <feedback-field :choice="level.choice" @input="updateHeight" @change="updateFeedback(level.choice)"></feedback-field>
                                                            <!--<textarea v-model="level.feedback" class="ta-feedback" @input="updateFeedback"></textarea>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import APIConfiguration from './Connector/APIConfiguration';
    import TreeNode from './Domain/TreeNode';
    import Rubric, {RubricJsonObject} from './Domain/Rubric';
    import Choice from './Domain/Choice';
    import Cluster from './Domain/Cluster';
    import Criterium from './Domain/Criterium';
    import FeedbackField from './Components/FeedbackField.vue';
    import DataConnector from './Connector/DataConnector';

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight}px`;
    }

    interface CriteriumExt {
        choices: any[];
        score: number|null;
    }

    @Component({
        components: {
            FeedbackField
        },
    })
    export default class RubricBuilderFull extends Vue {
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;
        //private showCustomFeedbackFields = false;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;

        updateHeight(e: InputEvent) {
            updateHeight(e.target as HTMLElement);
        }

        updateFeedback(choice: Choice) {
            this.dataConnector?.updateChoice(choice);
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
                rubric.levels.forEach(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    criteriumExt.choices.push({ title: level.title, choice, score});
                });
            });
        }

        mounted() {
            if (this.rubricData) {
                this.rubric = Rubric.fromJSON(this.rubricData as RubricJsonObject);
                this.initScores(this.rubric);
                // todo: get rubric data id
                this.dataConnector = new DataConnector(this.apiConfig as APIConfiguration, 0, this.version!);
                this.$nextTick(() => {
                    document.querySelectorAll('.ta-feedback').forEach(el => {
                        updateHeight(el as HTMLElement);
                    });
                });
            }
        }

        beforeDestroy() {
            if (this.rubric) {
                this.$emit('rubric-updated', this.rubric.toJSON());
            }
        }
    }
</script>
<style lang="scss">
    .builder-full-app {
        &#app {
            color: $text-color;
        }

        .table-header {
            margin-left: 19.8em;

            .table-header-title {
                flex: 1;
            }
        }

        .criterium-title-header {
            width: 18em;
            min-width: 18em;
        }

        .criterium .criterium-level {
            display: flex;
            flex-direction: column;
        }

        .criterium-level-header {
            cursor: text;
            border-color: transparent;
        }

        .default-feedback {
            padding: 0;
            flex: 1;
            border-radius: 3px;
        }

        .ta-feedback {
            padding: .3em;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 3px;
            resize: none;
            overflow: hidden;
            outline: none;
            min-height: 100%;
            background: transparent;

            &:hover, &:focus {
                border: 1px solid $score-dark;
                background: white;
            }
        }
    }
</style>
