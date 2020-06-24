<template>
    <div id="app" :class="{'builder-app': $route.name === 'Builder', 'builder-app-levels': $route.name === 'BuilderLevels', 'builder-full-app': $route.name === 'BuilderFull'}">
        <div class="app-header">
            <nav role="navigation">
                <ul class="app-header-menu">
                    <li class="app-header-item"><router-link :to="{ name: 'Builder' }"><span tabindex="-1">Rubric opbouwen</span></router-link></li>
                    <li class="app-header-item"><router-link :to="{ name: 'BuilderLevels' }"><span tabindex="-1">Niveaus opbouwen</span></router-link></li>
                    <li class="app-header-item"><router-link :to="{ name: 'BuilderFull' }"><span tabindex="-1">Snel invoeren</span></router-link></li>
                </ul>
            </nav>
            <!--<ul class="app-header-tools">
                <li class="app-header-item" v-if="$route.name === 'Builder'"><button id="btn-show-split-view" aria-hidden="true" class="btn-check" :class="{ checked: showSplitView }" @click.prevent="showSplitView = !showSplitView"><span tabindex="-1"><i class="check fa" aria-hidden="true" />Split View</span></button></li>
                <li class="app-header-item" v-else-if="$route.name === 'BuilderLevels'"><button class="btn-check" aria-label="Toon beschrijvingen" :aria-expanded="showLevelDescriptions ? 'true' : 'false'" :class="{ checked: showLevelDescriptions }" @click.prevent="showLevelDescriptions = !showLevelDescriptions"><span tabindex="-1"><i class="check fa" aria-hidden="true" />Beschrijvingen</span></button></li>-->
            <!--</ul>-->
            <save-area :data-connector="dataConnector"></save-area>
        </div>
        <div class="rubrics">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div v-if="rubric" class="rubrics-wrapper" :class="{ 'rubrics-wrapper-levels': $route.name === 'BuilderLevels' }">
                <router-view :rubric="rubric" :data-connector="dataConnector" :selected-criterium="selectedCriterium" :ui-state="$route.name === 'Builder' ? uiState : null" @criterium-selected="selectCriterium"></router-view>
            </div>
            <div v-else class="app-container-loading">
                <p>Loading Rubrics...</p>
                <div class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import ScoreRubricView from '../Components/ScoreRubricView.vue';
    import CriteriumDetailsView from '../Components/CriteriumDetailsView.vue';
    import Criterium from '../Domain/Criterium';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricBuilderFull from './RubricBuilderFull.vue';
    import SaveArea from '../Components/SaveArea.vue';
    import APIConfiguration from '../Connector/APIConfiguration';
    import DataConnector from '../Connector/DataConnector';

    @Component({
        components: {
            ScoreRubricView, CriteriumDetailsView, RubricBuilderFull, SaveArea
        },
    })
    export default class RubricBuilder extends Vue {
        private selectedCriterium: Criterium|null = null;
        //private showSplitView: boolean = false; // Set through uiState
        //private content: string = 'rubric'; // Set through uiState
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;
        @Prop({type: Object}) readonly uiState!: any;

        get showSplitView() {
            return this.uiState.showSplitView;
        }

        set showSplitView(showSplitView: boolean) {
            this.uiState.showSplitView = showSplitView;
        }

        get showLevelDescriptions() {
            return this.uiState.showLevelDescriptions;
        }

        set showLevelDescriptions(showLevelDescriptions: boolean) {
            this.uiState.showLevelDescriptions = showLevelDescriptions;
        }

        selectCriterium(criterium: Criterium|null) {
            this.selectedCriterium = criterium;
            this.uiState.selectedCriterium = criterium ? criterium.id : '';
        }

        get content() {
            return this.uiState.content;
        }

        set content(content: string) {
            this.uiState.content = content;
        }

        mounted() {
            if (this.rubricData) {
                this.rubric = Rubric.fromJSON(this.rubricData as RubricJsonObject);
                if (this.uiState.selectedCriterium) {
                    const selectedCriterium = this.rubric.getAllCriteria().find(criterium => criterium.id === this.uiState.selectedCriterium);
                    if (selectedCriterium) {
                        this.selectedCriterium = selectedCriterium;
                    } else {
                        this.uiState.selectedCriterium = '';
                    }
                }
                // todo: get rubric data id
                this.dataConnector = new DataConnector(this.apiConfig as APIConfiguration, 0, this.version!);
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
            font-weight: 400;
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
            outline: none;
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
        min-height: 34px;
        color: #333;
        border: 1px solid #c0c0c0;

        &:focus {
            outline: none;
            border-color: $input-color-focus;
        }

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

    .item-new button {
        border: 1px solid transparent;
        border-radius: $border-radius;

        &:focus {
            outline: none;
            border: 1px solid $input-color-focus;
        }

        &.btn-name-input:focus {
            border: 1px solid transparent;
            box-shadow: inset 0 0 0 1px white;
        }
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

        &.collapse-open {
            background-color: darken($cluster-color-selected, 5%);
            color: #fff;
            border-color: transparent;
        }

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
        transition: background-color 200ms, border-color 200ms, color 200ms;

        &:hover, &.show-menu {
            background-color: $cluster-color-hover;
            border-color: $cluster-color-hover;
            color: #222;
        }

        &.selected {
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

    .rb-cluster-list-item {
        outline: none;

        .rb-cluster:focus {
            outline: none;
        }

        &:focus .rb-cluster {
            box-shadow: inset 0 0 0 1px $input-color-focus;
            z-index: 10;

/*            &.selected {
                box-shadow: inset 0 0 0 2px white;
            }*/
        }
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

        .btn-color {
            display: inline-block;
            margin: 0 .5em;
            padding: 0;
            width: 14px;
            height: 14px;
            outline-width: 1px;
            border: none;
            box-shadow: 0px 0px 3px #999;
            cursor: pointer;

            &:focus {
                outline: none;
                box-shadow: 0px 0px 4px #333;
                border: 2px solid hsla(0, 0%, 100%, 0.8);
            }
        }
    }

    .rb-category-title {
        /*position: relative;*/
        display: flex;
        align-items: center;
        flex: 1;
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

        .title:focus {
            outline: none;
        }

        &:focus {
            outline: none;

            .title {
                outline: 1px solid $input-color-focus;
                outline-offset: 5px;
            }
        }
    }

    /** Rubric buttons **/
    .btn-cluster-add, .btn-category-add, .btn-criterium-add {
        background-color: transparent;
        border: 1px solid transparent;
        border-radius: $border-radius;
        margin-left: -1px;
        font-size: 1.25rem;
        color: darken($btn-color, 25%);
        transition: color 200ms;

        i {
            margin-right: .4em;
            font-size: 1.1rem;
            color: darken($btn-color, 15%);;
            transition: color 200ms;
        }

        &:hover, &:hover i {
            color: $btn-color-darkened;
        }

        &:focus {
            outline:none;
            border: 1px solid $input-color-focus;
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

    .criterium-details-wrapper {
        overflow-y: auto;
        overflow-x: hidden;
    }

    .criterium-details {
        position: relative;
        padding-top: 10px;
        padding-bottom: 10px;
        color: #333;
        width: 100%;

        i.fa-close {
            top: 20px;
        }

        .input-detail {
            &:focus {
                outline: none;
            }
        }
    }

    .criterium-details-header {
        display: flex;
        align-items: flex-start;
        margin-bottom: .75em;
    }

    .criterium-details-title {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        margin: 0 .25em .5em -.1em;

        label {
            font-size: 1.3rem;
            font-weight: 400;
            margin-bottom: 0;
            border: 1px solid transparent;
        }

        .input-detail {
            font-size: 1.6rem;
            height: 1.9em;
            padding-left: .25em;
            width: 100%;
            margin-left: -.3em;
        }
    }

    .criterium-path {
        font-size: 1.2rem;
        display: none;
    }

    .criterium-weight {
        margin-top: .7em;
        font-size: 1.4rem;

        label {
            font-weight: 400;
            font-size: 1.3rem;
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

        &:hover {
            background-color: rgba(255, 255, 255, 0.25);
        }

        &:focus {
            background-color: #fff;
            border: 1px solid $input-color-focus;
        }

        &.fixed-score {
            &, &:hover, &:focus {
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
        padding: 0;
        background: transparent;
        border: 1px solid transparent;
        border-radius: $border-radius;

        > .fa + .fa, &:hover > .fa, &:focus > .fa {
            display: none;
        }

        &:hover > .fa + .fa, &:focus > .fa + .fa {
            display: inherit;
        }

        &:focus {
            outline: none;
            border: 1px solid $input-color-focus;
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
        padding: 0;
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
    }

    @media only screen and (max-width: 899px) {
        .builder-app .app-header-tools {
            display: none;
        }

        .app-container.split-view #clusters-wrapper-view2 {
            display: none;
        }

        .clusters-collapse {
            overflow: hidden;
        }

        .clusters-view.collapse-closed {
            display: none;
        }

        .clusters-slide {
            &-enter-active, &-leave-active {
                transition: transform 300ms;
            }

            &-enter, &-leave-to {
                transform: translateY(-100%);
            }
        }

        .rb-cluster {
            border: 1px solid $cluster-color-border;
            border-top-width: 0;

            &.selected {
                border-color: $cluster-color-selected;
            }
        }

        .rb-cluster-list-item:first-child .rb-cluster {
            border-top-width: 1px;
            border-top-left-radius: $border-radius;
            border-top-right-radius: $border-radius;

            &:hover {
                border-top-color: $cluster-color-hover;
            }

            &.selected {
                border-top-color: $cluster-color-selected;
            }
        }

        .rb-cluster-list-item.selected {
            box-shadow: inset 0 0 0 2px white;
        }

        .rb-cluster-list-item:last-child .rb-cluster {
            border-bottom-left-radius: $border-radius;
            border-bottom-right-radius: $border-radius;
        }

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

            &:focus {
                box-shadow: inset 0 0 0 1px white;
            }
        }

        .clusters-view .actions {
            margin-bottom: .75em;
        }

        .criterium-details-wrapper {
            display: flex;
            position: absolute;
            background: change_color($modal-bg, $alpha: 0.66);
            height: 100vh;
            top: 0;
            left: 0;
            width: 100%;
            align-items: flex-start;
            justify-content: center;
            z-index: 20;
        }

        .criterium-details {
            background-color: $bg-color;
            border-radius: $border-radius;
            width: 95%;
            max-width: 50em;
            padding: 1.2em;
            margin-top: 1.5em;
        }
    }

    @media only screen and (min-width: 900px) {

        /** App **/

        #app.builder-app {
            /*position: absolute;
            top: 0; bottom: 0; left: 0; right: 0;*/
            overflow: hidden;
            padding-bottom: 0;
            height: calc(100vh - 166px);
        }

        .no-drop {
            opacity: 0.3;
        }

        /** Action Menu **/

        .item-actions {
            width: 1.8em;
            /*height: 1.8em;*/
            /*opacity: 0;*/
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

            &:focus {
                outline: none;
                border: 1px solid $input-color-focus;
                opacity: 1;
            }

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

        @media (any-hover: hover) {
            .item-actions {
                opacity: 0;
            }
        }

        .app-container:not(.dragging) {
            .rb-cluster-list-item:hover, .rb-criterium:hover, .category-header .item-header-bar:hover {
                .item-actions {
                    opacity: 1;
                }
            }
        }

        .rb-cluster {
            &.selected, &:not(.selected):hover, &.show-menu {
                .item-actions:focus {
                    /*border-color: white;*/
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
            min-width: 120px;
            background: #fff;
            z-index: 10;

            &.show-menu {
                display: flex;
            }
        }

        .action-menu-list {
            position: absolute;
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

            &:focus {
                background: $btn-color;

                i, span {
                    color: white;
                }
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

        .builder-app .rubrics-wrapper {
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
            margin-top: .5em;
            margin-left: -1em;
            border-bottom: 1px solid transparent;
            transition: border-bottom-color 200ms;
        }

        .clusters-view.mod-separator {
            border-bottom: 1px solid hsla(194, 15%, 77%, 1);
        }

        .rb-clusters {
            display: flex;
            margin-bottom: -1px;
            margin-left: 1em;
        }

        .rb-cluster-list-item {
            /* A border is needed here so the ghost item can show the dotted border without flicking the content below */
            border: 1px solid transparent;
            margin-right: .65em;

            &:first-child {
                /* Offsetting the transparent pixel border so the items line up */
                margin-left: -1px;
            }

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
            font-size: 1.35rem;
            background-color: hsla(160, 6%, 90%, 1);
            border: 1px solid hsla(197, 10%, 86%, 1);
            color: hsla(200, 10%, 38%, 1);
            border-radius: $border-radius;
            padding: .3em 0 .4em .5em;

            /*.title i {
                font-size: 1.2rem;
                margin-right: .5em;
                display: inline-block;
            }*/

            .item-actions {
                opacity: 1;

                &.show-menu, &:hover {
                    background: change_color($cluster-item-actions-bg-color, $alpha: 0.5);
                    color: white;
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
                /*box-shadow: 0px 2px 4px #999;*/
                /*border-color: $cluster-color-selected!important;*/
                border-color: hsla(194, 15%, 77%, 1);
                background: $bg-color;
                color: hsla(190, 45%, 38%, 1);
                font-weight: bold;

                /*.item-actions {
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
                }*/
            }
            &:hover {
                background: hsla(210, 30%, 75%, .3);
            }

        }

        .clusters-view.mod-separator .rb-cluster {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-color: transparent;

            &.selected {
                height: 3.6rem;
                margin-bottom: -1px;
                border-bottom-color: $bg-color;
            }
        }

        .cluster-selected {
            position: absolute;
            visibility: hidden;
        }

        /** Categories **/

        .selected-fade {
            &-leave-active, &-enter-active {
                transition: opacity 200ms ease-in-out;/*, transform 200ms;*/
            }

            &-enter, &-leave-to {
                opacity: 0;
                /*transform: scale(.95);*/
            }
        }

        .cluster-content {
            display: flex;
            margin-top: 1em;
            padding-top: .2em;
            margin-left: -.5em;
            padding-left: .5em;
            flex: 1;
            overflow: auto;
            @include scrollbar();
        }

        .criteria-empty-list {
            border-left: 1px solid hsla(193, 14%, 82%, 1);
            border-right: 1px solid hsla(193, 14%, 82%, 1);
            color: hsla(204, 45%, 25%, 0.6);
            padding: .5em .5em 0;
            font-style: italic;
        }

        .rb-categories {
            display: flex;
        }

        .rb-category {
            width: 19em;
            margin-right: 1.2em;

            .item-header-bar {
                cursor: pointer;
            }

            .title {
                font-size: 1.3rem;
            }

            .actions {
                border: 1px solid hsla(193, 14%, 82%, 1);
                border-top: none;
                border-bottom-left-radius: 3px;
                border-bottom-right-radius: 3px;
                padding: .238em 0;
            }

            .action-menu {
                width: 12em;
                top: 4.8em;
                right: -10.35em;
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

            .rb-category-title .title {
                font-size: 1.33rem;
                flex: 1;
                /*font-weight: bold;*/
                /*color: hsla(190, 50%, 35%, 1);*/
            }

            .category-header.null-category .rb-category-title .title {
                margin-left: .5em;
                opacity: 0.8;
                font-style: italic;
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
            border: 1px solid hsla(193, 14%, 82%, 1);
            border-top-left-radius: $border-radius;
            border-top-right-radius: $border-radius;
            border-bottom: none;
            background: linear-gradient(to bottom,
                    hsla(60, 2%, 91%, 0.6) 0px,
                    hsla(60, 2%, 91%, 0) 14px,
                    hsla(204, 38%, 40%, 0) 0px,
                    hsla(204, 38%, 40%, 0.12) 36px);

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

        .category-name-input {
            width: 18em;
            margin-top: .1em;
        }

        .btn-category-add {
            margin-top: .4em;
        }

        /** Criteria **/

        .rb-criteria {
        }

        .rb-criterium-list-item.ghost {
            background-color: $ghost-bg-color;
            border: 1px dotted $ghost-border;

            .title {
                color: transparent;
            }
        }

        .rb-criterium {
            font-size: 1.3rem;
            background: none;
            border-bottom: 1px solid hsla(193, 14%, 85%, 1);
            border-left: 1px solid hsla(193, 14%, 82%, 1);
            border-right: 1px solid hsla(193, 14%, 82%, 1);
            /*background: linear-gradient(to top, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0) 14px);*/

            &.selected {
                border: 1px solid hsla(193, 14%, 75%, 1);
                margin-top: -1px;
/*                border: 1px solid darken($bg-criterium-details, 10%);*/
                /*border-radius: $border-radius;*/
                background: $bg-criterium-details;
                /*margin: -1px;*/
            }

            .title {
                color: hsla(204, 45%, 25%, 1);
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
                width: 9em;
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
            display: flex;
            padding: 0;

            .title {
                flex: 1;
                padding: .7em .5em;
                min-height: 3.419rem;
            }

            &:focus .title {
                outline-offset: 0;
                z-index: 10;
            }
        }

        /** Vue Swatches **/

        .vue-swatches {
            margin-bottom: 0;
            width: 90%;
        }

        .vue-swatches__container {
            padding: 2px 5px 0!important;
        }

        .vue-swatches__wrapper {
            padding: 0 1px!important;
        }

        /** Criterium Details Editor **/

        .criterium-details-wrapper {
            @include scrollbar();
            background-color: $bg-criterium-details;
            border-top: 1px solid hsla(194, 15%, 77%, 1);
            border-left: 1px solid hsla(194, 15%, 77%, 1);
            border-top-left-radius: $border-radius;
        }

        .criterium-details {
            padding: 1em;
            width: 30em;
            overflow-x: hidden;
            overflow-y: auto;

            .input-detail {
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid transparent;
            }

            /*.criterium-weight .input-detail {
                border: 1px solid darken($input-color, 5%);

                &:focus {
                    border: 1px solid $input-color-focus;
                }
            }*/

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
</style>
