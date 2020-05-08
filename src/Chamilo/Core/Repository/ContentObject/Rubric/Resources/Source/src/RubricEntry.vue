<template>
    <div id="app">
        <div class="app-header">
            <ul class="app-header-menu">
                <li class="app-header-item"><a @click.prevent="">Entry View</a></li>
                <!--<li class="app-header-item"><a @click.prevent="content = 'rubric'">Edit Rubric</a></li>
                <li class="app-header-item"><a @click.prevent="content = 'levels'">Edit Niveaus</a></li>-->
            </ul>
            <ul class="app-header-tools">
                <li class="app-header-item" @click.prevent="showDefaultFeedbackFields = !showDefaultFeedbackFields"><a>DF</a></li>
                <li class="app-header-item" @click.prevent="showCustomFeedbackFields = !showCustomFeedbackFields"><a>CF</a></li>
                <!--<li class="app-header-item" :class="{ checked: showSplitView }" v-if="content === 'rubric'"><a role="button" @click.prevent="showSplitView = !showSplitView"><i class="fa fa-check-circle" />Split View</a></li>-->
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
            <div :style="`--num-levels: ${ rubric.levels.length }`">
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{showDefaultFeedbackFields, showCustomFeedbackFields}">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title}}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${category.color}`">
                                    <div class="category">
                                        <div class="category-title category-indicator">{{ category.title }}</div>
                                        <ul class="criteria">
                                            <li v-for="(criterium, index) in category.criteria" class="criterium-list-item">
                                                <div class="criterium">
                                                    <div class="criterium-header"><h4 class="criterium-title category-indicator">{{ criterium.title }}</h4></div>
                                                    <div class="default-feedback"></div>
                                                    <template v-for="level in rubric.levels" class="score">
                                                        <div class="score-header" tabindex="0" @keyup.enter.space.stop="setScore(criterium, level)" @click="setScore(criterium, level)" :class="{ selected:getCriteriumSelectedLevel(criterium)===level }">
                                                            <div class="score-number">{{ rubric.getChoiceScore(criterium, level) }}</div>
                                                            <div class="level-title">
                                                                {{level.title}}
                                                            </div>
                                                        </div>
                                                        <div class="default-feedback">
                                                            {{ rubric.getChoice(criterium, level).feedback }}
                                                        </div>
                                                    </template>
                                                    <div class="final-score-header">
                                                        <div class="score-number">{{ getCriteriumScore(criterium) }}</div>
                                                    </div>
                                                    <div class="default-feedback"></div>
                                                </div>
                                                <div class="custom-feedback" style="">
                                                    <textarea placeholder="Geef Feedback"></textarea>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="subtotal category-total">
                                        <div class="category-indicator">Totaal {{ category.title }}:</div><div class="score-number">{{ getCategoryScore(category) }}</div>
                                    </div>
                                </li>
                            </ul>
                            <div class="subtotal cluster-total">
                                <div class="cluster-total-title">Totaal {{ cluster.title }}:</div><div class="score-number">{{ getClusterScore(cluster) }}</div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="subtotal rubric-total">
                    <div class="rubric-total-title">Totaal Rubric:</div><div class="score-number">{{ getRubricScore() }}</div>
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
    import Level from './Domain/Level';
    import Cluster from './Domain/Cluster';
    import Category from './Domain/Category';
    import Criterium from './Domain/Criterium';
    import DataConnector from './Connector/DataConnector';

    @Component({
        components: {
        },
    })
    export default class RubricEntry extends Vue {
        private content: string = 'rubric';
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;
        private scores: Map<Criterium, Level> = new Map<Criterium, Level>();
        private showDefaultFeedbackFields = true;
        private showCustomFeedbackFields = true;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;

        getCriteriumSelectedLevel(criterium: Criterium) : Level|undefined {
            return this.scores.get(criterium);
        }

        getCriteriumScore(criterium: Criterium) : number {
            const level = this.scores.get(criterium);
            if (!(this.rubric && level)) { return 0; }
            return this.rubric.getChoiceScore(criterium, level);
        }

        getCategoryScore(category: Category) : number {
            return category.criteria.map(criterium => this.getCriteriumScore(criterium)).reduce((v1, v2) => v1 + v2, 0);
        }

        getClusterScore(cluster: Cluster) : number {
            return cluster.categories.map(category => this.getCategoryScore(category)).reduce((v1, v2) => v1 + v2, 0);
        }

        getRubricScore() : number {
            return this.rubric.clusters.map(cluster => this.getClusterScore(cluster)).reduce((v1, v2) => v1 + v2, 0);
        }

        setScore(criterium: Criterium, level: Level) {
            this.scores.set(criterium, level);
            this.$forceUpdate();
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

        mounted() {
            if (this.rubricData) {
                this.rubric = Rubric.fromJSON(this.rubricData as RubricJsonObject);
                // todo: get rubric data id
                this.dataConnector = new DataConnector(this.apiConfig as APIConfiguration, 0, this.version!);
            }
        }
    }
</script>
<style lang="scss">
    $bg-color: hsla(165, 5%, 90%, 1);
    * {
        outline-width: thin;
    }

    #app {
        color: #555;
    }

    .rubric {
        margin: 1em 1.5em;
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
        margin: 1em 0 0 0;
        color: hsla(203, 38%, 33%, .7);
        font-weight: bold;
        /*padding-bottom: .3em;*/
        /*border-bottom: 2px solid hsla(190, 40%, 40%, 1);*/
        /*display: none;*/
    }

    .category {
        margin-top: 1em;
        margin-bottom: 1em;
    }

    .category-indicator:before {
        content: '';
        display: inline-block;
        background: var(--category-color);
        margin-right: 0.35em;
    }

    .category-title {
        font-size: 1.5rem;
        margin: .25em 0 .5em 0;
        color: hsla(203, 38%, 33%, 1);

        &.category-indicator:before {
            width: .8em;
            height: .8em;
        }
    }

    .category-total {
        display: flex;
        justify-content: flex-end;
        font-size: 1.3rem;
        margin-right: 1.5em;

        .category-indicator:before {
            width: .6em;
            height: .7em;
        }
    }

    .cluster-total {
        display: flex;
        justify-content: flex-end;
        font-size: 1.3rem;
        margin-top: 1em;
        margin-right: 1.5em;
    }

    .cluster-total-title {
        font-weight: 700;
        color: hsla(203, 38%, 33%, 1);
    }

    .rubric-total {
        display: flex;
        justify-content: flex-end;
        font-size: 1.3rem;
        margin-top: 1em;
        margin-right: 1.5em;
        padding-top: .3em;
        padding-bottom: .3em;
        border-top: 1px solid hsla(190, 20%, 78%, .5);
    }

    .rubric-total-title {
        font-weight: 700;
        color: hsla(190, 40%, 35%, 1);
    }

    .criterium {
        margin-bottom: 1em;
        margin-left: 1.3em;
        display: grid;
        grid-template-columns: 18em repeat(var(--num-levels), 1fr) 2.4em;
        grid-auto-flow: column;
        grid-gap: .5em;
        color: #666;
        margin-right: 1.5em;
    }

    .showDefaultFeedbackFields .criterium {
        grid-template-rows: repeat(2, auto);
    }

    .criterium-header {
        width: 100%;
        align-content: flex-end;
        border-radius: 3px;
        padding-top: .3em;
        padding-bottom: .35em;
        display: flex;
    }

    .showDefaultFeedbackFields .criterium-header {
        border-bottom: 1px solid hsla(190, 20%, 78%, .5);
    }

    .final-score-header {
        line-height:1.4em;
        margin-bottom: 0;
        display:flex;
        padding-bottom: .35em;
        /*border: 1px solid hsla(190, 20%, 78%, .5);*/
        border-radius: 3px;
        background: hsla(190, 20%, 78%, .2);

    }

    .criterium-title {
        font-size: 1.4rem;
        margin-bottom: 0em;
        margin-top: 0.5em;
        color: black;
        align-self: flex-end;
        padding-right: 1em;

    }

    .criterium-title.category-indicator:before {
        width: .6em;
        height: .7em;
    }

    .score {
        cursor: pointer;
        align-self: start;

        > div {
            margin-left: .3em;
            margin-top: .3em;

            > div {
                color: #333;
            }
            &:first-child {
                > div:first-child {
                    width: 1.5em;
                }
            }
        }
    }

    .score-header {
        white-space: nowrap;
        overflow: hidden;
        line-height:1.4em;
        margin-bottom: 0;
        display:flex;
        align-items:center;
        border: 1px solid transparent;
        border-radius: 3px;
        color: #555;
        padding-bottom: .35em;
        cursor: pointer;
        transition: background 200ms;

        &:hover, &:focus, &.selected {
            outline: none;
            border: 1px solid hsla(204, 38%, 55%, 1);
        }

        &.selected {
            &, &:focus {
                background: hsla(204, 38%, 55%, 1);
                color: white;
            }
            &:hover, &:focus {
                border-color: hsla(204, 65%, 35%, 1);
            }
        }
    }

    .showDefaultFeedbackFields .score-header {
        &:not(:hover) {
            border-bottom: 1px solid hsla(190, 20%, 78%, .5);
        }
        &:focus {
            border-bottom: 1px solid hsla(204, 38%, 55%, 1);
        }
        &.selected:focus {
            border-bottom: 1px solid hsla(204, 65%, 35%, 1);
        }
    }

    .score-number {
        width: 1.7em;
        min-width: 1.7em;
        font-size: 1.8rem;
        padding-right: .35em;
        text-align: right;
        border-right: 1px solid #bbb;
        align-self: flex-end;
    }

    .score-header.selected {
        &, &:focus {
            .score-number {
                border-right-color: white;
            }
        }
    }

    .final-score-header .score-number {
        border-right: none;
    }

    .subtotal .score-number {
        border-right: none;
        padding-right: 0.4em;
        margin-left: .6em;
        border-radius: 3px;
    }

    .category-total .score-number {
        background: hsla(190, 20%, 78%, .5);
    }

    .cluster-total .score-number {
        background: hsla(190, 40%, 45%, .75);
        color: white;
    }

    .rubric-total .score-number {
        background: hsla(190, 40%, 35%, 1);
        color: white;
    }

    .level-title {
        margin-left: .5em;
        font-size:1.4rem;
        align-self: flex-end;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .default-feedback {
        padding-left: 2.9em;
        display: none;
    }

    .showDefaultFeedbackFields .default-feedback {
        display: block;
    }

    .custom-feedback {
        margin-left: 2.5em;
        margin-bottom: 1em;
        display: none;

        textarea {
            width: 40em;
            height: 2.2em;
            max-width: 100%;
            background: transparent;
            border: 1px solid #d0d0d0;
            border-radius: 3px;
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
    .showCustomFeedbackFields .custom-feedback {
        display: block;
    }
    .showDefaultFeedbackFields .custom-feedback {
        margin-left: 22.5em;
    }
</style>
<style lang="scss">

    /** Mixins **/

    @mixin user-select($property) {
        -webkit-touch-callout: $property;
        -webkit-user-select: $property;
        -moz-user-select: $property;
        -ms-user-select: $property;
        user-select: $property;
    }

    @mixin scrollbar() {
        &::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        &::-webkit-scrollbar-track {
            box-shadow: inset 0 0 2px grey;
            background-color: hsla(200, 50%, 40%, .05);
            border-radius: 10px;
        }
        &::-webkit-scrollbar-thumb {
            background-color: hsla(200, 50%, 40%, .15);
            border-radius: 10px;
        }
        &::-webkit-scrollbar-thumb:hover {
            background-color: hsla(220, 70%, 40%, .20);
        }
    }

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

    /** Loader **/

    .app-container-loading {
        width: 100%;
        flex: 1;
        background-color: $bg-color;
        padding: 0;
        display: flex;
        flex-direction: column;
        margin: 0 auto;

        p {
            margin: 1.5em 1.5em 0;
        }

        .lds-ellipsis {
            margin-left: .9em;
        }
    }

    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;

        div {
            position: absolute;
            top: 13px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: hsla(190, 40%, 45%, 1);
            animation-timing-function: cubic-bezier(0, 1, 1, 0);

            &:nth-child(1) {
                left: 8px;
                animation: lds-ellipsis1 0.6s infinite;
            }

            &:nth-child(2) {
                left: 8px;
                animation: lds-ellipsis2 0.6s infinite;
            }

            &:nth-child(3) {
                left: 32px;
                animation: lds-ellipsis2 0.6s infinite;
            }

            &:nth-child(4) {
                left: 56px;
                animation: lds-ellipsis3 0.6s infinite;
            }
        }
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

    /** Action Menu **/

    .item-actions {
        pointer-events: all;
        color: #999;
        display: none;
        margin: 0 .25em 0 .75em;
        cursor: pointer;
    }

    .action-menu {
        display: flex;
        align-items: center;
    }

    .action-menu-list {
        display: flex;
        list-style: none;
        padding: 0;
        margin-right: 4px;

        span {
            position: absolute;
            width: 0;
            overflow: hidden;
        }
    }

    .action-menu-list-item {
        background-color: transparent;
        border-radius: $border-radius;
        padding: 0 5px;
        color: #999;
        transition: background-color 200ms, color 200ms;
        cursor: pointer;

        &:hover, &:focus {
            background-color: $btn-color;
            color: #fff;
        }
    }

    /** Name Input **/

    .name-input {
        margin-top: .5em;
        display: flex;

        .btn-clear {
            padding: 4px;
        }
    }

    .name-input-actions {
        margin-left: 5px;
        display: flex;
    }

    .name-input-title {
        flex: 1;
    }

    .name-input-field {
        padding: 2px 18px 2px 4px;
        min-height: 36px;
        color: #333;

        &::placeholder {
            color: #777;
            opacity: 1;
        }
    }

    .btn-name-input {
        border-radius: $border-radius;
        transition: background-color 0.2s ease-in, color 0.1s ease-in;
        border: 1px solid transparent;
        color: #666;
        font-weight: 400;

        &:hover, &:focus {
            background-color: $btn-color;
            color: #fff;
            border: 1px solid transparent;
        }

        &[disabled] {
            background-color: transparent;
            border: 1px solid #cfcfcf;
            color: #999;
            cursor: not-allowed;
        }
    }

    .btn-ok {
        background-color: $btn-color-lightened;
        color: #fff;
    }

    .btn-cancel {
        background-color: transparent;
        border: 1px solid #cdcdcd;
    }

    /** Modal Content **/

    .modal-bg {
        position: fixed;
        background-color: $modal-bg;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10;
        animation-name: fade-in;
        animation-duration: 300ms;
    }

    .modal-content {
        background-color: $bg-color;
        max-width: 90%;
        width: 420px;
        height: 150px;
        margin: 120px auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: $border-radius;
        box-shadow: 0px 6px 12px #666;
    }

    .modal-content-title {
        padding-bottom: 16px;
        margin-bottom: 10px;
        border-bottom: 1px solid $panel-border-color;
        width: 100%;
        text-align: center;
    }

    /** Modal Name Input **/

    .edit-title {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 10;

        .cover {
            position: fixed;
            top: 0;
            left: 0;
            background-color: $modal-bg;
            width: 100%;
            height: 100%;
            animation-name: fade-in;
            animation-duration: 300ms;
        }

        .name-input {
            position: absolute;
            background-color: $bg-color;
            margin: 0;
            padding: 4px;
            width: 100%;
            border-radius: $border-radius;
        }
    }

    /** Modal Remove Dialog **/

    .btn-dialog-remove {
        border-radius: $border-radius;
        transition: background-color 0.2s ease-in, color 0.1s ease-in;
        border: 1px solid transparent;
        color: #666;
        font-weight: 400;

        &:hover, &:focus {
            background-color: $btn-color;
            color: #fff;
            border: 1px solid transparent;
        }

        &.btn-ok {
            margin-right: 8px;
            color: #fff;
        }
    }

    /**  **/

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

        /** Action Menu **/

        .item-actions {
            width: 1.8em;
            /*height: 1.8em;*/
            opacity: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: transparent;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #777;
            transition: all 200ms;
            cursor: pointer;
            font-size: 1.1rem;

            &.show-menu {
                opacity: 1;
            }

            i {
                padding-top: 2px;
                pointer-events: none;

                &.show-menu {
                    padding-top: 0;
                }
            }
        }

        .action-menu {
            display: none;
            width: 9em;
            position: absolute;
            min-width: 100px;
            background: #fff;
            z-index: 10;

            &.show-menu {
                display: flex;
            }
        }

        .action-menu-list {
            position: fixed;
            background-color: #fff;
            flex-direction: column;
            list-style: none;
            border-radius: $border-radius;
            box-shadow: 0px 0px 3px #999;
            overflow: hidden;
        }

        .action-menu-list-item {
            margin-right: 0;
            font-size: 1.3rem;
            padding: .25em .5em;
            cursor: pointer;
            pointer-events: all;
            border-radius: 0;

            &:hover {
                background: #ddd;
            }

            i {
                margin-right: .3em;
                color: #666;
            }

            span {
                position: initial;
                width: initial;
                color: #333;
                opacity: 1;
            }
        }

        /** Name Input **/

        .name-input {
            margin-top: 0;
            margin-bottom: 1em;
        }

        /** Modal Name Input **/

        .edit-title .name-input {
            width: 104%;
            margin-left: -2%;
            margin-top: -1%;
            box-shadow: 0px 3px 10px #666;
        }
    }

    @keyframes fade-in {
        from { opacity: 0 }
        to { opacity: 1 }
    }
</style>
