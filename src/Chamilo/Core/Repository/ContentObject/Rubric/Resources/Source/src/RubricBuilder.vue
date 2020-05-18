<template>
    <div id="app" class="builder-app">
        <div class="app-header">
            <ul class="app-header-menu">
                <li class="app-header-item"><a @click.prevent="content = 'rubric'">Edit Rubric</a></li>
                <li class="app-header-item"><a @click.prevent="content = 'levels'">Edit Niveaus</a></li>
            </ul>
            <ul class="app-header-tools">
                <li class="app-header-item" :class="{ checked: showSplitView }" v-if="content === 'rubric'"><a role="button" @click.prevent="showSplitView = !showSplitView"><i class="check fa" />Split View</a></li>
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
        <div class="rubrics">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div v-if="rubric" class="rubrics-wrapper" :class="{ 'rubrics-wrapper-levels': content === 'levels' }">
                <score-rubric-view v-if="content === 'rubric'" :rubric="rubric" :split="showSplitView" :selected-criterium="selectedCriterium" :data-connector="dataConnector" @criterium-selected="selectCriterium" />
                <levels-view v-else-if="content === 'levels'" :rubric="rubric" :data-connector="dataConnector"></levels-view>
            </div>
            <div v-else class="app-container-loading">
                <p>Loading Rubrics...</p>
                <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import ScoreRubricView from './Components/ScoreRubricView.vue';
    import CriteriumDetailsView from './Components/CriteriumDetailsView.vue';
    import Criterium from './Domain/Criterium';
    import LevelsView from './Components/LevelsView.vue';
    import APIConfiguration from './Connector/APIConfiguration';
    import Rubric, {RubricJsonObject} from './Domain/Rubric';
    import DataConnector from './Connector/DataConnector';

    @Component({
        components: {
            ScoreRubricView, CriteriumDetailsView, LevelsView
        },
    })
    export default class RubricBuilder extends Vue {
        private selectedCriterium: Criterium|null = null;
        private showSplitView: boolean = false;
        private content: string = 'rubric';
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;

        selectCriterium(criterium: Criterium|null) {
            this.selectedCriterium = criterium;
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

    /** Clusters collapse Menu **/

    .btn-collapse {
        background-color: darken($bg-color, 5%);
        border-color: transparent;
        border-radius: $border-radius;
        margin: 1em 0 .2em;
        font-size: 1.8rem;
        padding: 4px 6px;
        color: #777;
        transition: background-color 200ms, color 200ms;

        i {
            padding-left: 2px;
        }

        span {
            position: absolute;
            opacity: 0;
        }

        &:hover {
            background-color: $btn-color;
            color: #fff;
            border-color: transparent;
        }
    }

    .clusters-wrapper.open .btn-collapse {
        background-color: darken($cluster-color-selected, 5%);
        color: #fff;
        border-color: transparent;
    }

    .clusters-collapse {
        position: absolute;
        overflow: hidden;

        .clusters-view {
            transform: translateY(-100%);
            transition: transform 300ms;
        }
    }

    .clusters-wrapper.open .clusters-collapse {
        position: initial;

        .clusters-view, &.editing .clusters-view {
            transform: unset;
        }
    }

    /** Rubric Editor **/

    .rubrics {
        width: 92%;
        max-width: 580px;
        margin: 0 1.5em;

        * {
            outline-width: thin;
        }
    }

    .rubric-panes.criterium-selected {
        display: none;
    }

    .item-header-bar {
        display: flex;
        justify-content: space-between;
    }

    /** Clusters **/

    .rb-clusters {
        margin-bottom: .5em;
        list-style: none;
        padding: 0;
        @include user-select(none);
    }

    .rb-cluster {
        position: relative;
        padding: .5em;
        padding-right: 0;
        border: 1px solid $cluster-color-border;
        border-top-width: 0px;
        transition: background-color 200ms, border-color 200ms, color 200ms;
        /*.title i {
            display: none;
        }*/
        &:hover, &.show-menu {
            background-color: $cluster-color-hover;
            border-color: $cluster-color-hover;
            color: #222;
        }
        &.selected {
            border-color: $cluster-color-selected;
            background-color: $cluster-color-selected;
            color: #fff;

            .action-menu-list-item {
                color: #ccc;

                &:hover, &:focus {
                    color: #fff;
                }
            }
        }
    }

    .rb-cluster-list-item:first-child .rb-cluster {
        border-top: 1px solid $cluster-color-border;
        border-top-left-radius: $border-radius;
        border-top-right-radius: $border-radius;

        &.selected {
            border-top: 1px solid $cluster-color-selected;
        }

        &:not(.selected):hover {
            border-top: 1px solid $cluster-color-hover;
        }
    }

    .rb-cluster-list-item:last-child .rb-cluster {
        border-bottom-left-radius: $border-radius;
        border-bottom-right-radius: $border-radius;
    }

    .rb-cluster-title {
        flex: 1;
        cursor: pointer;
    }

    .cluster-selected {
        font-size: 2.3rem;
        border-bottom: 2px solid $cluster-color-border-selected;
        margin-top: 0.5em;
        padding: 0 0.15em 0.1em;
    }

    .clusters-view .actions {
        margin-bottom: .75em;
    }

    /** Categories **/

    .rb-categories {
        list-style: none;
        padding: 0;
        @include user-select(none);
    }

    .rb-category {
        margin-bottom: 1.5em;

        .title {
            font-size: 1.6rem;
            margin: 0;
            display: inline-block;
            line-height: 1.3em;
        }

        .actions {
            padding: .5em .1em;
        }
    }

    .category-header {
        position: relative;
        padding: .75em 0;
        background: linear-gradient(to bottom, $bg-color 0px, change-color($bg-color, $alpha: 0) 10px, change-color($bg-color-darkened, $alpha: 0) 19px, change-color($bg-color-darkened, $alpha: 0.25) 33px);

        a {
            display: inline-block;
            margin-left: .5em;
            margin-right: .5em;
            min-width: 14px;
            min-height: 14px;
            outline-width: 2px;
            box-shadow: 0px 0px 3px #999;
            cursor: pointer;
        }
    }

    .rb-category-title {
        /*position: relative;*/
        display: flex;
        align-items: center;
    }

    /** Criteria **/

    .rb-criteria {
        list-style: none;
        padding: 0;
        @include user-select(none);
    }

    .rb-criterium {
        position: relative;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0) 14px);

        .title {
            font-size: 1.4rem;
            font-weight: 400;
            margin: 0;
            padding: 0;
        }
    }

    .rb-criterium-title {
        padding: .75em .5em;
        flex: 1;
        cursor: pointer;
    }

    /** Rubric buttons **/
    .btn-cluster-add, .btn-category-add, .btn-criterium-add {
        background-color: transparent;
        border: none;
        font-size: 1.25rem;
        color: #777;
        transition: color 200ms;

        i {
            margin-right: .4em;
            font-size: 1.1rem;
            color: #999;
            transition: color 200ms;
        }

        &:hover, &:hover i {
            color: $btn-color-darkened;
        }
    }

    /** Vue Swatches **/

    .vue-swatches {
        animation-name: fade-in;
        animation-duration: 300ms;
        margin-bottom: -12px;
    }

    .vue-swatches__swatch:first-child .vue-swatches__check__wrapper{
        display: none;
    }

    .vue-swatches__swatch:first-child.vue-swatches__swatch--selected {
        border: 2px solid $input-color-focus;
    }

    @media only screen and (max-width: 500px) {
        .vue-swatches {
            max-width: 220px;
        }
    }

    /** Criterium Details Editor **/

    .criterium-details {
        position: relative;
        padding-top: 10px;
        padding-bottom: 10px;
        color: #333;
        width: 100%;
        @include scrollbar();

        i.fa-close {
            top: 20px;
        }
    }

    .criterium-details-header {
        display: flex;
        align-items: center;
        margin-bottom: .75em;
    }

    .criterium-details-title {
        display: flex;
        align-items: center;
        font-size: 1.6rem;
        margin-top: 0;
        width: 100%;
        margin-left: -.1em;
        margin-right: .25em;

        label {
            font-weight: 400;
            margin-bottom: 0;
            border: 1px solid transparent;
        }

        .input-detail {
            height: 1.9em;
            padding-left: .25em;
            width: 100%;
        }
    }

    .criterium-path {
        font-size: 1.2rem;
    }

    .criterium-weight {
        margin-top: .7em;
        font-size: 1.4rem;

        label {
            font-weight: 400;
        }

        .input-detail {
            width: 4em;
            padding: 0 .3em;
        }
    }

    .criterium-levels {
        list-style: none;
        margin-top: 1.5em;
        padding: 0;
    }

    .rb-criterium-level {
        margin-bottom: 1.5em;
    }

    .rb-criterium-level-title {
        font-weight: 700;
    }

    .criterium-level-description {
        color: #999;
        cursor: pointer;
        transition: color 200ms;

        &:hover {
            color: #666;
        }
    }

    .criterium-level-input {
        display: flex;
    }

    .criterium-level-score {
        position: relative;
        width: 3.8em;
        margin-left: .75em;

        .input-detail {
            text-align: right;
            width: 2.7em;
            font-size: 2.1rem;
        }
    }

    .criterium-level-feedback {
        width: 100%;
        padding: .25em .4em .35em;
        margin: 0 1em 0 -.4em;
        resize: none;
        overflow: hidden;
    }

    .criterium-details .input-detail {
        border: 1px solid $input-color;
        border-radius: $border-radius;

        &:hover, &:focus {
            background-color: #fff;
            border: 1px solid $input-color-focus;
        }

        &.fixed-score {
            background-color: $fixed-score-color;

            &:hover, &:focus {
                background-color: $fixed-score-color;
            }
        }
    }

    .remove-fixed {
        position: absolute;
        left: -1.4em;
        width: 1.4em;
        height: 2.1em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: $btn-color-darkened;

        > .fa + .fa, &:hover > .fa {
            display: none;
        }

        &:hover > .fa + .fa {
            display: inherit;
        }
    }

    .rubric-return {
        transition: color 200ms;

        &:hover {
            color: darken($btn-color-darkened, 15%);
            text-decoration: none;
        }
    }

    .btn-close {
        width: 2em;
        height: 2em;
        margin-left: .25em;
        background-color: darken($bg-color, 5%);
        color: #777;
        border: 1px solid transparent;
        border-radius: $border-radius;
        transition: background-color 200ms, color 200ms;

        &:hover {
            background-color: $btn-color;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #fff;
        }

        &:focus {
            border: 1px solid $input-color-focus;
        }

        span {
            position: absolute;
            width: 0;
            overflow: hidden;
        }
    }

    @media only screen and (max-width: 899px) {
        .rb-criterium-title {
            pointer-events: none;
        }

        .btn-category-add {
            background-color: darken($bg-color, 5%);
            border-color: transparent;
            width: 100%;
            border-radius: $border-radius;
            text-align: left;
            padding: .5em;
            transition: background-color 200ms, color 200ms;
            font-size: 1.4rem;
            color: #444;

            i {
                margin-right: .35em;
                font-size: 1.2rem;
                color: #666;
                transition: color 200ms;
            }

            &:hover, &:focus {
                background-color: $btn-color;
                border-color: transparent;
                font-weight: 600;
                color: #fff;

                i {
                    color: #fff;
                }
            }
        }
    }

    @media only screen and (min-width: 900px) {

        /** App **/

        #app.builder-app {
            position: absolute;
            top: 0; bottom: 0; left: 0; right: 0;
            overflow: hidden;
            padding-bottom: 0;
        }

        .no-drop {
            opacity: 0.3;
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

        .app-container:not(.dragging) {
            .rb-cluster-list-item:hover, .rb-criterium:hover, .category-header .item-header-bar:hover {
                .item-actions {
                    opacity: 1;
                }
            }
        }

        /*.app-container:not(.dragging) .criterium:hover .item-actions, .app-container:not(.dragging) .handle-area-category:hover .item-actions, .app-container:not(.dragging) .cluster-list-item:hover .item-actions {
            opacity: 1;
        }*/

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

            &:not(.cluster-new) {
                flex-direction: column;
                .name-input-actions {
                    margin-top: .4em;
                    margin-left: 0;
                }
            }

            &.cluster-new {
                margin-bottom: 0;
                width: 29em;
                height: 34px;
            }
        }

        /** Modal Name Input **/

        .edit-title .name-input {
            width: 104%;
            margin-left: -2%;
            margin-top: -1%;
            box-shadow: 0px 3px 10px #666;
        }

        /** Clusters collapse Menu **/

        .btn-collapse {
            display: none;
        }

        .clusters-collapse {
            position: initial;
            overflow: unset;

            .clusters-view {
                transform: unset;
            }
        }

        /** Rubric Editor **/

        .rubrics {
            width: initial;
            max-width: initial;
            margin: 0;
            flex: 1;
            position: relative;
        }

        .rubrics-wrapper {
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
        }

        .app-container {
            width: 100%;
            display: flex;
            height: 100%;

            .clusters-wrapper {
                height: 100%;
                display: flex;
                flex-direction: column;
                overflow-x: hidden;
                padding-left: 1em;

                &#clusters-wrapper-view2 {
                    border-top: 1px solid $panel-border-divider-color;
                }
            }

            &.split-view .clusters-wrapper {
                height: 50%;
            }

            &.dragging {
                * {
                    cursor: move;
                    cursor: grabbing;
                }

                &.not-allowed, &.not-allowed * {
                    cursor: not-allowed;
                }
            }
        }

        .rubric-panes-wrapper {
            position: relative;
            flex: 1;
        }

        .rubric-panes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;

            &.criterium-selected {
                display: block;
            }
        }

        .dragging .actions {
            opacity: 0;
            transition: opacity 300ms;
        }

        /** Clusters **/

        .clusters-view {
            display: flex;
            align-items: baseline;
            margin-top: 1em;

            .actions {
                margin-bottom: 0;
            }
        }

        .rb-clusters {
            display: flex;
        }

        .rb-cluster-list-item {
            border: 1px solid transparent;
            margin-right: .65em;

            &.ghost {
                background: $ghost-bg-color;
                border: 1px dotted $ghost-border;
                border-radius: $border-radius;

                &:after, > * {
                    visibility: hidden;
                }
            }

            &:hover .item-actions {
                color: #666;

                &.show-menu {
                    color: #fff;
                }
            }
        }

        .rb-cluster {
            /*padding: .6em 0 .6em .8em;*/
            font-size: 1.35rem;
            background-color: $cluster-color;

            /*.title i {
                font-size: 1.2rem;
                margin-right: .5em;
                display: inline-block;
            }*/

            .item-actions {
                opacity: 0.5;
                &.show-menu, &:hover {
                    background: $cluster-item-actions-bg-color;
                    color: #fff;
                }
            }

            .name-input {
                width: 18em;
            }

            .action-menu {
                top: 4.6em;
                right: -7.4em;
            }

            &.selected {
                box-shadow: 0px 2px 4px #999;

                .item-actions {
                    i {
                        color: #ddd;
                    }

                    &.show-menu, &:hover {
                        background: $cluster-item-actions-bg-color-selected;
                    }
                }

                &:hover .item-actions {
                    color: #fff;
                    i {
                        color: #fff;
                    }
                }
            }
        }

        .rb-cluster, .rb-cluster-list-item:first-child .rb-cluster {
            border: 1px solid transparent;
            border-radius: $border-radius;
        }

        .cluster-selected {
            position: absolute;
            visibility: hidden;
        }

        /** Categories **/

        .cluster-content {
            display: flex;
            margin-top: 1.3em;
            padding-top: .2em;
            margin-left: -.5em;
            padding-left: .5em;
            flex: 1;
            overflow: auto;
            @include scrollbar();
        }

        .rb-categories {
            display: flex;
        }

        .rb-category {
            width: 18em;
            margin-right: 1.2em;

            .item-header-bar {
                cursor: pointer;
            }

            .title {
                font-size: 1.3rem;
            }

            .actions {
                padding-top: .3em;
            }

            .action-menu {
                top: 4.8em;
                right: -7.35em;
            }

            &.ghost {
                background: $ghost-bg-color;
                border: 1px dotted $ghost-border;
                border-radius: $border-radius;
                height: 5.5em;

                > * {
                    visibility: hidden;
                }
            }

            &.null-category {
                .category-header {
                    opacity: .5;
                    transition: opacity 200ms;
                }

                &:hover .category-header {
                    opacity: 1;
                }

                .rb-category-title .title {
                    font-style: oblique;
                    margin-left: .5em;
                }
            }
        }

        .category-header {
            padding: 0;
            border: 1px solid #dedede;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            border-bottom: none;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.4) 0px, rgba(255, 255, 255, 0) 10px, rgba(0, 0, 0, 0) 19px, rgba(0, 0, 0, 0.08) 36px);

            .item-header-bar {
                padding: .65em 0;
                /*&:hover .item-actions {
                    opacity: 1;
                }*/

                .item-actions {
                    padding: 2px 0;

                    i {
                        padding-top: 2px;
                    }

                    &:hover, &.show-menu {
                        background: $item-actions-bg-color;
                    }
                }
            }
        }

        .no-category {
            width: 0;
            transition: all 1ms;
        }

        .category-dragging .no-category {
            width: 18em;
            margin-right: 0;
            height: 200px;
            flex: 0;
            background: transparent;
            transition-delay: 300ms;
        }

        /** Criteria **/

        .rb-criteria {
            border: 1px solid #dedede;
            border-top: none;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .rb-criterium-list-item.ghost {
            background-color: $ghost-bg-color;
            border: 1px dotted $ghost-border;
            color: transparent;
        }

        .rb-criterium {
            font-size: 1.3rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0) 14px);

            &.selected {
                border: 2px solid $criterium-color-border-selected;
                border-radius: $border-radius;
                margin: -2px;
            }

            .item-header-bar {
                width: 100%;
                align-items: center;

                /*&:hover .item-actions {
                    opacity: 1;
                }*/
                .item-actions {
                    padding: 2px 0;

                    i {
                        padding-top: 2px;
                    }

                    &:hover, &.show-menu {
                        background: $item-actions-bg-color;
                    }
                }
            }

            .action-menu {
                top: 4.8em;
                right: -7.35em;
            }

            .menu-list-item-details {
                display: none;
            }
        }

        .app-container.dragging .rb-criterium .item-actions {
            opacity: 0;
        }

        .rb-criterium-title {
            padding: .65em .5em;
        }

        /** Vue Swatches **/

        .vue-swatches {
            margin-bottom: 0;
        }

        .vue-swatches__container {
            padding: 2px 5px 0!important;
        }

        .vue-swatches__wrapper {
            padding: 0 1px!important;
        }

        /** Criterium Details Editor **/

        .criterium-details {
            padding: 1em;
            width: 30em;
            border-left: 1px solid $panel-border-divider-color;
            overflow-x: hidden;
            overflow-y: auto;
            background-color: $bg-criterium-details;

            .input-detail {
                background-color: transparent;
                border: 1px solid transparent;
            }

            .criterium-weight .input-detail {
                border: 1px solid darken($input-color, 5%);
            }

            .btn-close {
                background-color: darken($bg-criterium-details, 5%);

                &:hover {
                    background-color: $btn-color;
                }
            }

            .rubric-return {
                display: none;
            }
        }
    }

    /** Levels Editor **/

    .levels-container {
        flex: 1;
        display: flex;
        justify-content: flex-start;
        margin-left: .8em;

        .btn-ok {
            margin-right: .5em;
        }
    }

    .lc-btn {
        margin-bottom: 0;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        padding: .2em .4em;
        border-radius: $border-radius;
        outline-width: thin;
        @include user-select(none);

        &[disabled]:hover {
            background: initial;
            color: initial;

            i {
                color: grey;
            }
        }
    }

    .lc-levels {
        list-style: none;
        padding: 0;

        button {
            margin-top: .7em;
            margin-bottom: .25em;
        }
    }

    .level-updown {
        margin-left: .55em;
        align-self: center;
    }

    .btn-updown {
        font-size: 2rem;
        outline-width: thin;
        background: transparent;

        i {
            color: #999;
        }

        &:hover i {
            color: $btn-color;
        }

        &:disabled i, &:hover:disabled i {
            color: #bbb;
        }
    }

    .level-details {
        flex: 1;
        display: flex;
        flex-wrap: wrap;

        .ld-weight {
            margin: 0 10px;
        }
    }

    .lc-list {
        display: flex;
        height: fit-content;

        h1 {
            font-size: 2.2rem;
            margin-left: .5em;
            color: #666;
        }

        .input-detail {
            border: 1px solid transparent;
        }

        .ld-level .lc-label, .ld-description .lc-label {
            margin-left: .3em;
        }

        &.add-mode {
            .levels-list-item:not(.new-level) {
                pointer-events: none;
            }
        }
    }

    .ld-description {
        flex-basis: 100%;
        margin-top: 1.2em;
    }

    .ld-default .input-detail {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .level-title-input {
        width: 100%;
        height: 40px;
        padding: .3em;

        &::placeholder {
            opacity: 0;
        }
    }

    .lc-edit .level-title-input::placeholder {
        opacity: 1;
    }

    .lc-label {
        display: block;
        color: #406e8d;
    }

    .levels-container .input-detail {
        border-radius: $border-radius;
    }

    .ta-description {
        width: 100%;
        resize: none;
        padding: .3em;
    }

    .ld-level {
        flex: 5;
        display: flex;
        flex-direction: column;
    }

    .ld-weight {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .label-level-title, .label-level-description {
        padding-left: .25em;
    }

    .label-level-weight {
        text-align: center;
    }

    .ld-weight .input-detail {
        display: block;
        width: 70px;
        font-size: 24px;
        text-align: right;
        padding-right: 5px;
    }

    .ld-default {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;

        * {
            cursor: pointer;
        }

        .input-detail {
            margin-top: .45em;
        }
    }

    .btn-level-delete {
        width: 26px;
        height: 26px;
        padding: 0;
        background-color: transparent;
        color: transparent;
        border-radius: $border-radius;

        i {
            margin-left: 3px;
            padding: 0;
            transition: color 120ms;
        }

        &:focus {
            outline: none;
        }
    }

    .ld-delete:hover .btn-level-delete i, .btn-level-delete:focus i {
        color: #d9534f;
    }

    .ld-cover {
        width: 0;
        height: 0;
    }

    .lc-edit .ld-cover {
        display: none;
    }

    .ld-edit {
        display: none;
    }

    .btn-level-edit {
        background: transparent;

        i {
            color: #999;
        }
    }

    .levels-list-item {
        display: flex;
        flex-direction: column;
        padding: .6em .6em 0;
        width: 600px;
        border-bottom: 1px solid #ddd;

        &:not(.selected) .ld-description {
            margin-top: 0;
        }

        &:not(.selected) .level-weight-input {
            -moz-appearance: textfield;
            margin-top: 2px;

            &::-webkit-outer-spin-button, &::-webkit-inner-spin-button {
                -webkit-appearance: none;
            }
        }

        &.selected .level-weight-input {
            -moz-appearance: number-input;
            margin-top: 0;
            height: 40px;
        }

        .level-default-input {
            position: absolute;
            opacity: 0;
        }

        .label-level-default {
            opacity: 0;
            color: #aaa;
            margin-top: .6em;

            &.checked {
                color: #406e8d;

                &.old-default {
                    color: #aaa;

                    &::after {
                        content: '!';
                        color: red;
                    }
                }

            }
        }

        &.selected .label-level-default, .label-level-default.checked, &:hover .label-level-default, .ld-default .input-detail:focus + .label-level-default {
            opacity: 1;
        }

        .lc-label {
            transition: all 150ms;
        }

        &.labels-hide .label-maybe-hide, &:not(.selected):first-child .ld-description .label-maybe-hide {
            opacity: 0;
            margin: 0;
            padding: 0;
            height: 0;
        }

        .input-detail {
            border-color: transparent;
            background: transparent;
        }

        &.selected {
            background: $bg-level-selected;
            padding-bottom: .6em;

            .input-detail {
                background: #fff;

                &:not(:focus) {
                    background: rgba(255, 255, 255, 0.2);
                }
            }
        }

        .ta-description {
            background: rgba(0, 0, 0, .02);
        }

        .ld-description.empty .ta-description {
            display: none;
        }

        &.selected .ld-description.empty .ta-description {
            display: inline-block;
        }

        .input-detail:hover, .input-detail:focus + .label-level-default, .btn-level-delete:focus {
            border: 1px solid $input-color-focus;
        }

        &.selected .btn-level-delete {
            color: #999;
        }

        &:hover .btn-level-delete {
            background: transparent;
            color: #bbb;
        }

        .btn-level-delete {
            margin-top: .5em;
        }

        &:first-child .btn-level-delete, &.selected .btn-level-delete {
            margin-top: 2.6em;
        }
    }

    .lc-edit {
        margin-top: 1.4em;
        width: 100%;
        max-width: 580px;

        > div, > form > div {
            margin-bottom: 1.1em;
        }

        .level-details {
            flex-direction: column;
        }

        .input-detail {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid $input-color;

            &:focus {
                background: #fff;
            }

            &[type="text"] {
                width: 100%;
                max-width: 600px;
            }
        }

        .ld-description {
            margin-bottom: .3em;
        }

        .ld-weight {
            margin-bottom: .6em;
        }

        .ld-weight, .ld-default {
            margin-left: .3em;
            align-items: start;
        }

        .level-weight-input {
            width: 3em;
            margin-left: -.1em;
            padding-left: .2em;
            text-align: left;
        }

        .label-level-default {
            border: 1px solid transparent;
            color: #aaa;

            i {
                margin-right: .3em;
                margin-left: -.15em;
            }

            &.checked {
                color: #406e8d;
            }
        }

        .input-detail {
            &:hover, &:hover + .label-level-default, &focus + .label-level-default {
                border: 1px solid $input-color-focus;
            }
        }

        .ta-description {
            resize: vertical;
            max-width: 600px;
        }

        .lc-return a {
            transition: color 200ms;

            &:hover {
                color: darken($btn-color-darkened, 15%);
                text-decoration: none;
            }
        }

        .ld-delete {
            display: block;
        }

        .btn-level-delete {
            background-color: change_color($btn-level-delete, $alpha: .5);
            margin: .75em .3em .75em .2em;
            padding-right: .5em;
            width: unset;
            color: #777;

            &:hover {
                background-color: change_color($btn-level-delete, $alpha: .75);
                color: $btn-color-darkened;
            }

            i {
                margin-left: .05em;
                margin-right: .2em;
            }
        }
    }

    .btn-level-add {
        position: relative;
        background-color: transparent;
        border: none;
        font-size: 1.25rem;
        color: #777;
        margin-left: .5em;

        i {
            margin-right: .1em;
            font-size: 1.1rem;
            color: #999;
        }

        &:hover, &:hover i {
            color: $btn-color-darkened;
        }
    }

    .btn-level-add-cover {
        display: none;
        pointer-events: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    @media only screen and (min-width: 900px) {
        .rubrics-wrapper-levels {
            overflow-y: auto;
        }

        .lc-edit {
            margin-left: .5em;
        }

        .ld-delete-hide {
            visibility: hidden;
        }

        .lc-list .ld-description {
            order: 1;
        }

        .lc-list .levels-list-item:hover .level-title-input::placeholder,
        .lc-list .levels-list-item.selected .level-title-input::placeholder {
            opacity: 1;
        }
    }

    @media only screen and (max-width: 899px) {
        .levels-container {
            margin-left: 0;

            &.container-lc-list {
                margin-left: -.8em;
            }
        }

        .lc-levels {
            pointer-events: none;
        }

        .level-title-input {
            text-overflow: ellipsis;
        }

        .ld-delete {
            display: none;
        }

        .lc-list {
            flex: 1
        }

        .level-updown {
            margin-right: .55em;
        }

        .level-details .level-detail {
            position: relative;
        }

        .ld-default, .ld-edit {
            pointer-events: visible;
        }

        .ld-cover {
            position: absolute;
            width: 100%;
            height: 100%;
            background: transparent;
            pointer-events: visible;
        }

        .ld-edit {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: .8em;
        }

        .btn-level-add:active {
            pointer-events: none;
        }

        .btn-level-add-cover {
            display: block;
            pointer-events: all;
        }

        .levels-list-item {
            padding-top: .3em;
            padding-bottom: .3em;

            .label-level-default {
                opacity: 1;
                color: #c9c9c9;
            }

            &:hover .label-level-default:not(.checked) {
                color: #999;
            }

            .ld-description {
                display: none;
            }

            &.selected {
                padding-bottom: .3em;

                .label-level-default:not(.checked) {
                    color: #bbb;
                }

                &:hover .label-level-default:not(.checked) {
                    color: #999;
                }

                .level-weight-input {
                    margin-top: 2px;
                    height: initial;
                    -moz-appearance: textfield;

                    &::-webkit-outer-spin-button, &::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                    }
                }

                .input-detail {
                    &.level-title-input, &.level-weight-input {
                        background: transparent;
                    }
                }
            }

            &:not(:first-child).selected .lc-label:not(.label-level-default) {
                opacity: 0;
                margin: 0;
                padding: 0;
                height: 0;
            }

            .btn-level-edit {
                margin-top: .15em;

                &:hover i {
                    color: $btn-color;
                    transition: color 200ms;
                }
            }

            &:first-child .btn-level-edit {
                margin-top: 2.3em;
            }
        }
    }

    @media only screen and (max-width: 700px) {
        .level-details {
            width: 90vw;
        }

        .lc-list {
            flex-direction: column;
            align-items: flex-start;

            .levels-list-item {
                width: 100%;
            }
        }

        .btn-updown {
            display: inline-block;
        }
    }

</style>
