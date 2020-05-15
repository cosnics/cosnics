<template>
    <div id="app">
        <div class="app-header">
            <ul class="app-header-menu">
                <!--<li class="app-header-item"><a @click.prevent="">Entry View</a></li>-->
            </ul>
            <ul class="app-header-tools">
                <li class="app-header-item" @click.prevent="toggleDefaultFeedbackFields"><a>DF</a></li>
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
                <div class="levels-header-wrap">
                    <div class="levels-header">
                        <div v-for="level in rubric.levels" class="criterium-level-title">
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

    /** Colors **/

    $text-color: #555;
    $bg-color: hsla(165, 5%, 90%, 1);
    /*$bg-color: rgba(220, 224, 227, 1);*/
    $level-header-color: hsla(190, 30%, 55%, 1);
    $title-color: hsla(203, 38%, 33%, 1);
    $score-lighter: hsla(190, 20%, 78%, .2);
    $score-light: hsla(190, 20%, 78%, .5);
    $score-dark: hsla(190, 40%, 45%, .75);
    $score-darker: hsla(190, 40%, 35%, 1);
    $level-selected-color: hsla(204, 38%, 55%, 1);
    $level-selected-color-dark: hsla(204, 65%, 35%, 1);
    /** Border **/

    $border-radius: 3px;

    * {
        outline-width: thin;
    }

    #app {
        color: $text-color;
        /*min-width: 850px;*/
    }

    .rubric {
        margin: 0em 1.5em;
    }

    .rubric-entry-view {
        position: relative;
    }

    .levels-header-wrap {
        background: $bg-color;
        background: linear-gradient(to bottom, $bg-color 0, $bg-color 50%, change_color($bg-color, $alpha: 0) 100%);
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        padding-top: .8em;
        padding-bottom: .4em;
        z-index: 10;
    }

    .levels-header {
        display: flex;
        margin-left: 19.8em;
        align-items: stretch;
        align-content: center;

        .criterium-level-title {
            flex: 1;
            background-color: $level-header-color;
            padding: .4em .5em;
            margin-right: .5em;
            font-size: 1.4rem;
            line-height: 1.4em;
            text-overflow: ellipsis;
            overflow: hidden;
            color: white;
            border-radius: $border-radius;
            box-shadow: 0px 1px 2px #999;
            /*min-width: 8.57em;*/
        }
    }

    #rubric-entry-wrapper .levels-header {
        margin-right: 3.5em;
    }

    .rubric-title {
        font-size: 2.4rem;
        margin: 0 0 1em 0;
        display: none;
    }

    .clusters, .categories, .criteria {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .cluster-title {
        font-size: 1.8rem;
        margin: .25em 0 0 0;
        color: change-color($title-color, $alpha: 0.7);
        font-weight: bold;
    }

    .category-indicator:before {
        content: '';
        display: inline-block;
        background: var(--category-color);
        margin-right: 0.35em;
    }

    .category {
        margin: .5em 0 .35em 0;
    }

    .category-title {
        font-size: 1.5rem;
        margin: .25em 0 0 0;
        color: $title-color;

        &.category-indicator:before {
            width: .8em;
            height: .8em;
        }
    }

    .criterium {
        margin-left: 1.3em;
        margin-bottom: .5em;
        display: flex;
    }

    .show-custom-feedback .criterium {
        margin-bottom: .75em;
    }

    .show-default-feedback .criterium {
        margin-bottom: .75em;
    }

    .criterium-title-header {
        margin-right: 0.5em;
        position: relative;

        .btn-more {
            position: absolute;
            top: .3em;
            right: 0;
            width: 1.4em;
            text-align: center;
            cursor: pointer;
            color: #bbb;

            .check::before {
                content: '\f078';
            }
            &:hover {
                color: #999;
            }
        }
    }

    #rubric-entry-wrapper .criterium-title-header {
        width: 18em;
        min-width: 18em;
    }

    .show-default-feedback .btn-more {
        /*top: unset;
        bottom: -2.5em;*/
        .check::before {
            content: '\f077';
        }
    }

    .criterium-title {
        font-size: 1.4rem;
        line-height: 1.4em;
        margin-top: 0.5em;
        margin-bottom: 0;
        display: flex;

        &.category-indicator:before {
            margin-top: .3em;
            min-width: .6em;
            height: .7em;
        }
    }

    .criterium-level {
        flex: 1;
        margin-right: 0.5em;
        /*min-width: 9.23em;*/
    }

    .criterium-level-header {
        background: #ddd;
        border: 1px solid transparent;
        border-radius: $border-radius;
        border-bottom-color: $score-light;
        text-align: center;
        cursor: pointer;
        transition: 200ms background;

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

    .criterium .criterium-level-title {
        display: none;
    }

    .score-number {
        font-size: 1.8rem;
        line-height: 1.6em;
        color: #666;
        border-radius: $border-radius;
    }

    .criterium-level-header {
        .score-number .check {
            font-size: 1.3rem;
            margin-right:.4em;
            vertical-align: center;
            color: #c9c9c9;
            transition: 200ms color;

            &::before {
                content: '\f1db'
                /*content: '\f111'*/
            }
        }

        &:hover .score-number .check {
            color: #777;
        }

        &.selected .score-number {
            &, .check {
                color: white;
            }
        }

        &:hover .score-number, &.selected .score-number {
            .check::before {
                content: '\f058'
            }
        }
    }

    .subtotal {
        .score-wrap {
            margin-left: .5em;
        }

        .score-number {
            padding-right: .3em;
            line-height: 1.2em;
            text-align: right;
            border: 1px solid transparent;
        }
    }

    .rubric-entry-view .subtotal {
        .score-wrap {
            width: 3.5em;
        }
    }

    .rubric-entry-view .criterium-total {
        min-width: 3.5em;
    }

    .criterium-total .score-number {
        line-height: 1.6em;
        background: $score-lighter;
    }

    .category-total {
        display: flex;
        justify-content: flex-end;
        font-size: 1.3rem;
        /*margin-right: 1.5em;*/

        .category-indicator:before {
            width: .6em;
            height: .7em;
        }

        .score-number {
            background: $score-light;
        }
    }

    .cluster-total {
        display: flex;
        justify-content: flex-end;
        font-size: 1.3rem;
        margin-top: .5em;
        /*margin-right: 1.5em;*/

        .score-number {
            background: $score-dark;
            color: white;
        }
    }

    .cluster-total-title {
        font-weight: 700;
        color: darken($score-dark, 20%);
    }

    .rubric-total {
        display: flex;
        justify-content: flex-end;
        font-size: 1.3rem;
        margin-top: .25em;
        /*margin-right: 1.5em;*/
        padding-top: .3em;
        padding-bottom: .3em;
        border-top: 1px solid $score-light;

        .score-number {
            background: $score-darker;
            color: white;
        }
    }

    .rubric-total-title {
        font-weight: 700;
        color: $score-darker;
    }

    .default-feedback {
        padding: .3em .5em;
        line-height: 1.4em;
        display: none;
    }

    .show-default-feedback .default-feedback {
        display: block;
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
<style lang="scss">

    /** Colors **/

    $bg-color: hsla(165, 5%, 90%, 1);
    $bg-color-darkened: darken($bg-color, 10%);
    $panel-border-color: hsla(199, 39%, 73%, 1);
    $panel-border-divider-color: hsla(200, 25%, 80%, 1);

    $bg-criterium-details: hsla(200, 13%, 87%, 1);
    $bg-level-selected: hsla(215, 20%, 85%, 1);

    $cluster-color: hsla(200, 10%, 80%, 0.5);
    $cluster-color-hover: hsla(190, 20%, 75%, 1);
    $cluster-color-selected: hsla(190, 40%, 45%, 1);
    $cluster-color-border: #ccc;
    $cluster-color-border-selected: hsla(190, 30%, 70%, 0.55);
    $fixed-score-color: hsla(100, 55%, 75%, 1);

    $criterium-color-border-selected: hsla(240, 15%, 80%, 1);

    $btn-color: hsla(200, 100%, 48%, 1);
    $btn-color-lightened: hsla(200, 100%, 57%, 1);
    $btn-color-darkened: hsla(200, 100%, 40%, 1);
    $input-color: #d6d6d6;
    $input-color-focus: hsla(200, 50%, 50%, 0.8);

    $btn-level-delete: hsla(165, 5%, 80%, 1);

    $item-actions-bg-color: rgba(0, 0, 0, .1);
    $cluster-item-actions-bg-color: hsla(190, 20%, 60%, 1);
    $cluster-item-actions-bg-color-selected: hsla(190, 40%, 40%, 1);

    $ghost-bg-color: rgba(255, 255, 255, 0.45);
    $ghost-border: rgba(28, 110, 164, 0.65);

    $modal-bg: rgba(0, 0, 0, 0.31);

    /** Border **/

    $border-radius: 3px;

    /** Override elements **/

    .container-breadcrumb {
        margin-bottom: 0;

        + .container-fluid {
            padding-left: 0;
            padding-right: 0;
            margin-top: -20px;
            margin-bottom: -20px;
            width: 100%;
            position: relative;
        }
    }

    /** App **/

    #app {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 1.3rem;
        line-height: 2.3rem;
        display: flex;
        flex-direction: column;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        background-color: $bg-color;
        padding-bottom: 1.5em;
    }

    .app-header {
        display: flex;
        border: 1px solid $panel-border-color;
        border-width: 1px 0;
        align-items: center;
        justify-content: space-between;
    }

    .app-header-menu, .app-header-tools {
        list-style: none;
        display: flex;
        margin-bottom: 0;
        padding: 0.6em 0;
    }

    .app-header-menu {
        margin-left: 1.5em;
    }

    .app-header-tools {
        display: none;
        i {
            margin-right: 0.3em;
        }
        :not(.checked) a {
            color: #999;
        }
        a:hover {
            color: #666;
        }
        :focus a, .checked a {
            color: #224e8b;
        }
    }

    .app-header-item {
        margin-right: 1em;
        cursor: pointer;

        a, a:hover {
            text-decoration: none;
            transition: color 120ms;
        }
    }

    .save-state {
        margin-right: 1.5em;
        color: #337ab7;
        width: 144px;
        text-align: right;
        transition: opacity 200ms;

        .saved {
            opacity: 0.6;
        }
    }

    @media only screen and (max-width: 899px) {

    }

    @media only screen and (min-width: 900px) {

        /** Override elements **/

        body {
            /*    display: flex;
                flex-direction: column;
                max-height: 100vh;*/
        }

        .container-breadcrumb + .container-fluid {
            /*height: 100vh;*/
        }

        /** App **/

        #app {
            /*position: absolute;
            top: 0; bottom: 0; left: 0; right: 0;
            overflow: hidden;
            padding-bottom: 0;*/
        }

        .app-header-tools {
            display: flex;
        }
    }

    @keyframes fade-in {
        from { opacity: 0 }
        to { opacity: 1 }
    }
</style>
