<i18n>
{
    "en": {
        "builder": "Rubric",
        "levels": "Levels",
        "builderfull": "Quick Feedback",
        "error-conflict": "The server responded with an error due to a conflict. Probably someone else is working on the same rubric at this time. Please refresh the page and try again.",
        "error-forbidden": "The server responded with an error. Possibly your last change(s) haven't been saved correctly. Please refresh the page and try again.",
        "error-notfound": "The server responded with an error. Possibly your last change(s) haven't been saved correctly. Please refresh the page and try again.",
        "error-timeout": "The server is taking too long to respond. Possibly your last change(s) haven't been saved correctly. Please refresh the page and try again.",
        "error-unknown": "An unknown error happened. Possibly your last change(s) haven't been saved. Please refresh the page and try again."
    },
    "fr": {
        "builder": "Rubrique",
        "levels": "Niveaux",
        "builderfull": "Feed-back Rapide"
    },
    "nl": {
        "builder": "Rubric",
        "levels": "Niveaus",
        "builderfull": "Snelle Feedback",
        "error-conflict": "Serverfout vanwege een conflict. Misschien werkt iemand aan dezelfde rubric op dit ogenblik. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-forbidden": "Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-notfound": "Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-timeout": "De server doet er te lang over om te antwoorden. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-unknown": "Je laatste wijzigingen werden mogelijk niet opgeslagen vanwege een onbekende fout. Gelieve de pagina te herladen en opnieuw te proberen."
    }
}
</i18n>
<template>
    <div id="app" :class="{'builder-app': $route.name === 'Builder', 'builder-app-levels': $route.name === 'BuilderLevels'}">
        <div class="app-header">
            <nav role="navigation">
                <ul class="app-header-nav">
                    <li class="app-nav-item"><router-link class="app-link" :to="{ name: 'Builder' }"><span class="link-text" tabindex="-1">{{ $t('builder') }}</span></router-link></li>
                    <li class="app-nav-item"><router-link class="app-link" :to="{ name: 'BuilderLevels' }"><span class="link-text" tabindex="-1">{{ $t('levels') }}</span></router-link></li>
                    <li class="app-nav-item"><router-link class="app-link" :to="{ name: 'BuilderFull' }"><span class="link-text" tabindex="-1">{{ $t('builderfull') }}</span></router-link></li>
                </ul>
            </nav>
            <save-area :data-connector="dataConnector" :error="errorCode ? $t(`error-${errorCode}`) : null"></save-area>
        </div>
        <div class="rubrics">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div v-if="rubric" :class="{ 'rubrics-wrapper': $route.name === 'Builder', 'rubrics-wrapper-levels': $route.name === 'BuilderLevels' }">
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
    import DataConnector, {DataConnectorErrorListener} from '../Connector/DataConnector';

    @Component({
        components: {
            ScoreRubricView, CriteriumDetailsView, RubricBuilderFull, SaveArea
        },
    })
    export default class RubricBuilder extends Vue {
        private selectedCriterium: Criterium|null = null;
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;
        private errorCode: string|null = null;

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

        setError(code: string) : void {
            this.errorCode = code;
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
                this.dataConnector = new DataConnector(this.rubric, this.apiConfig as APIConfiguration, (this.rubricData as any).rubric_data_id, this.version);
                this.dataConnector.addErrorListener(this as DataConnectorErrorListener);
            }
        }

        //@Watch('rubric', {deep: true}) // Unfortunately, changes in choices are not being picked up.
        //rubricChanged() {
        beforeDestroy() {
            if (this.rubric) {
                this.$emit('rubric-updated', this.rubric);
            }
        }
    }
</script>

<style lang="scss">

    /** Buttons **/

    .btn-strong {
        background-color: transparent;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: #666;
        font-weight: 400;
        transition: background-color 0.2s ease-in, color 0.1s ease-in;

        &.mod-confirm {
            background-color: $btn-color-lightened;
            color: #fff;
            margin-right: .4em;
        }

        &:hover, &:focus {
            background-color: $btn-color;
            border: 1px solid transparent;
            color: #fff;
        }

        &:focus {
            box-shadow: inset 0 0 0 1px #fff;
            outline: none;
        }

        &[disabled] {
            background-color: transparent;
            border: 1px solid #cfcfcf;
            color: #999;
            cursor: not-allowed;
        }
    }

    .btn-clear-text {
        background: none;
        border: 1px solid transparent;
        color: darkgrey;
        opacity: 1;
        transition: opacity 200ms, color 200ms;

        &.mod-textfield {
            height: 100%;
            padding: .4em .3em;
            position: absolute;
            right: 0;
            top: 0;
        }

        &.is-empty-field {
            opacity: 0;
        }

        &:hover {
            color: #666;
        }

        &:focus {
            outline: 1px solid $input-color-focus;
        }

        &::before {
            font-family: FontAwesome;
            font-size: 1.4rem;
            content: '\f057'
        }
    }

    /** Action Menu **/

    .btn-toggle-menu {
        color: #999;
        cursor: pointer;
        display: none;
        margin: 0 .25em 0 .75em;
        pointer-events: all;
    }

    .action-menu {
        align-items: center;
        display: flex;
    }

    .action-menu-list {
        display: flex;
        list-style: none;
        margin-right: 4px;
        padding: 0;
    }

    .action-menu-list-item {
        background-color: transparent;
        border-radius: $border-radius;
        color: #999;
        cursor: pointer;
        padding: 0 5px;
        transition: background-color 200ms, color 200ms;

        &:hover, &:focus {
            background-color: $btn-color;
            color: #fff;
            outline: none;
        }
    }

    .action-menu-text {
        font-weight: 400;
        overflow: hidden;
        position: absolute;
        width: 0;
    }


    /** Name Input **/

    .name-input {
        display: flex;
        margin-top: .5em;

        &.mod-edit {
            background-color: $bg-color;
            border-radius: $border-radius;
            margin: 0;
            padding: 4px;
            position: absolute;
            width: 100%;
        }
    }

    .name-input-actions {
        display: flex;
        margin-left: 5px;
    }

    .name-input-title {
        flex: 1;
        /*position: relative;*/
    }

    .name-input-field {
        border: 1px solid #c0c0c0;
        color: #333;
        min-height: 34px;
        padding: 2px 18px 2px 4px;

        &.mod-textarea {
            display: block;
            line-height: 1.3em;
            overflow: hidden;
            resize: none;
            width: 100%;
        }

        &.mod-textfield {
            padding-right: 1.6em;
            width: 100%;
        }

        &:focus {
            border-color: $input-color-focus;
            outline: none;
        }

        &::placeholder {
            color: #777;
            opacity: 1;
        }
    }

    .edit-title {
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 10;
    }

    /** Modal Content **/

    .modal-bg {
        animation-duration: 300ms;
        animation-name: fade-in;
        background-color: $modal-bg;
        bottom: 0;
        left: 0;
        position: fixed;
        right: 0;
        top: 0;
    }

    .modal-content {
        align-items: center;
        background-color: $bg-color;
        border-radius: $border-radius;
        box-shadow: 0px 6px 12px #666;
        display: flex;
        flex-direction: column;
        height: 150px;
        justify-content: center;
        margin: 120px auto;
        max-width: 90%;
        padding: 20px;
        width: 420px;
    }

    .modal-content-title {
        border-bottom: 1px solid $panel-border-color;
        margin-bottom: 10px;
        padding-bottom: 16px;
        text-align: center;
        width: 100%;
    }


    /** Clusters collapse Menu **/

    .btn-collapse {
        background-color: darken($bg-color, 5%);
        border-color: transparent;
        border-radius: $border-radius;
        color: #777;
        font-size: 1.8rem;
        margin: 1em 0 .2em;
        padding: 4px 6px;
        transition: background-color 200ms, color 200ms;

        &.is-open {
            background-color: darken($cluster-color-selected, 5%);
            border-color: transparent;
            color: #fff;
        }

        i {
            padding-left: 2px;
        }

        span {
            opacity: 0;
            position: absolute;
        }

        &:hover {
            background-color: $btn-color;
            border-color: transparent;
            color: #fff;
        }
    }


    /** Rubric Editor **/

    .rubrics {
        margin: 0 1.5em;
        max-width: 580px;
        width: 92%;

        * {
            outline-width: thin;
        }
    }

    .rubric-panes.is-criterium-selected {
        display: none;
    }

    .item-header-bar {
        display: flex;
        justify-content: space-between;
    }

    /** Clusters **/

    .b-clusters {
        list-style: none;
        margin-bottom: .5em;
        padding: 0;
        @include user-select(none);
    }

    .b-clusters .name-input {
        position: fixed;
    }

    .b-cluster-list-item {
        outline: none;
    }

    .b-cluster {
        padding: .5em;
        padding-right: 0;
        position: relative;
        transition: background-color 200ms, border-color 200ms, color 200ms;

        &:hover, &.is-menu-visible {
            background-color: $cluster-color-hover;
            border-color: $cluster-color-hover;
            border-bottom-color: transparent;
            color: #222;
        }

        &:focus {
          outline: none;
        }
    }

    .b-cluster-list-item:focus .b-cluster {
        box-shadow: inset 0 0 0 1px $input-color-focus;
        z-index: 10;
    }

    .b-cluster-title {
        cursor: pointer;
        flex: 1;
    }

    .b-cluster-selected-title {
        border-bottom: 2px solid $cluster-color-border-selected;
        font-size: 2.3rem;
        margin-top: 0.5em;
        padding: 0 0.15em 0.1em;
    }


    /** Categories **/

    .b-categories {
        list-style: none;
        padding: 0;
        @include user-select(none);
    }

    .b-category {
        margin-bottom: 1.5em;
    }

    .b-category-title {
        display: inline-block;
        font-size: 1.6rem;
        line-height: 1.3em;
        margin: 0;
    }

    .b-category-list-item {
        background: linear-gradient(to bottom, $bg-color 0px, change-color($bg-color, $alpha: 0) 10px, change-color($bg-color-darkened, $alpha: 0) 19px, change-color($bg-color-darkened, $alpha: 0.25) 33px);
        padding: .75em 0;
        position: relative;
    }

    .btn-category-color {
        background: hsla(207, 21%, 92%, 1);
        border: none;
        box-shadow: 0 0 3px #999;
        cursor: pointer;
        display: inline-block;
        height: 14px;
        margin: 0 .5em;
        outline-width: 1px;
        padding: 0;
        width: 14px;

        &:focus {
            border: 2px solid $input-color-focus;
            box-shadow: 0px 0px 4px #333;
            outline: none;
        }
    }

    .b-category-header-wrapper {
        align-items: center;
        display: flex;
        flex: 1;
        flex-direction: column;
        /*position: relative;*/
    }

    /** Criteria **/

    .b-criteria {
        list-style: none;
        padding: 0;
        @include user-select(none);
    }

    .b-criterium {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0) 14px);
        position: relative;
    }

    .b-criterium-title-wrapper {
        cursor: pointer;
        flex: 1;
        padding: .75em .5em;

        &:focus {
            outline: none;

            .b-criterium-title {
                outline: 1px solid $input-color-focus;
                outline-offset: 5px;
            }
        }
    }

    .b-criterium-title {
        font-size: 1.3rem;
        font-weight: 400;
        line-height: 1.3em;
        margin: 0;
        padding: 0;

        &:focus {
            outline: none;
        }
    }

    /** Rubric buttons **/

    .btn-new {
        background-color: transparent;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: darken($btn-color, 25%);
        font-size: 1.25rem;
        transition: color 200ms;
        white-space: nowrap;

        &::before {
            color: darken($btn-color, 15%);
            content: '\f067';
            font-family: FontAwesome;
            font-size: 1.1rem;
            margin-right: .4em;
        }

        &:hover, &:hover::before {
            color: $btn-color-darkened;
        }

        &:focus {
            border-color: $input-color-focus;
            outline: none;
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
        color: #333;
        position: relative;
        padding-bottom: 10px;
        padding-top: 10px;
        width: 100%;

        .input-detail:focus {
            outline: none;
        }
    }

    .criterium-details-header {
        margin-bottom: .75em;
        position: relative;
    }

    .criterium-details-title {
        width: 100%;

        label {
            border: 1px solid transparent;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .input-detail {
            font-size: 1.5rem;
            height: 1.9em;
            line-height: 1.3em;
            margin-left: -.35em;
            padding-left: .25em;
            resize: none;
            width: calc(100% + .3em);
        }
    }

    .criterium-path {
        display: none;
        font-size: 1.2rem;
    }

    .criterium-weight {
        font-size: 1.4rem;
        margin-top: .7em;

        label {
            font-size: 1.3rem;
            font-weight: 400;
        }

        .input-detail {
            padding: 0 .3em;
            width: 4em;
        }
    }

    .criterium-levels {
        list-style: none;
        margin-top: 1.5em;
        padding: 0;
    }

    .b-criterium-level {
        margin-bottom: 1.5em;
    }

    .b-criterium-level-title {
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
        margin-right: .75em;
        position: relative;
        width: 3.8em;

        .input-detail {
            font-size: 2.1rem;
            text-align: right;
            width: 2.7em;
        }
    }

    .criterium-level-feedback {
        margin: 0 1em 0 -.4em;
        overflow: hidden;
        padding: .25em .4em .35em;
        resize: none;
        width: 100%;
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
        align-items: center;
        background: transparent;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: $btn-color-darkened;
        cursor: pointer;
        display: flex;
        height: 2.1em;
        justify-content: center;
        left: -1.4em;
        padding: 0;
        position: absolute;
        width: 1.4em;

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
        align-items: center;
        background-color: $bg-criterium-details;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: #777;
        display: flex;
        height: 1.6em;
        justify-content: center;
        padding: 0;
        position: absolute;
        right: 2px;
        transition: background-color 200ms, color 200ms;
        width: 1.6em;

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
        #clusters-wrapper-view2 {
            display: none;
        }

        .clusters-collapse {
            overflow: hidden;
        }

        .clusters-view.is-closed {
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

        .b-cluster {
            border: 1px solid $cluster-color-border;
            border-top-width: 0;

            &.is-selected {
                background-color: $cluster-color-selected;
                border-color: $cluster-color-selected;
                color: #fff;
            }
        }

        .b-cluster-list-item:first-child .b-cluster {
            border-top-left-radius: $border-radius;
            border-top-right-radius: $border-radius;
            border-top-width: 1px;

            &:hover {
                border-top-color: $cluster-color-hover;
            }

            &.is-selected {
                border-top-color: $cluster-color-selected;
            }
        }

        .b-cluster-list-item:last-child .b-cluster {
            border-bottom-left-radius: $border-radius;
            border-bottom-right-radius: $border-radius;
        }

        .b-cluster-list-item:focus .b-cluster.is-selected {
            box-shadow: inset 0 0 0 2px #fff;
        }

        .action-menu-list-item.is-cluster-selected {
            color: #ccc;

            &:hover, &:focus {
                color: #fff;
            }
        }

        .b-criterium-title-wrapper {
            pointer-events: none;
        }

        .btn-new.mod-category-add {
            background-color: darken($bg-color, 5%);
            border-color: transparent;
            border-radius: $border-radius;
            color: #444;
            font-size: 1.4rem;
            padding: .5em;
            text-align: left;
            transition: background-color 200ms, color 200ms;
            width: 100%;

            &:hover, &:focus {
                background-color: $btn-color;
                border-color: transparent;
                color: #fff;
                font-weight: 600;

                &::before {
                    color: #fff;
                }
            }

            &:focus {
                box-shadow: inset 0 0 0 1px white;
            }

            &::before {
              color: #666;
              font-size: 1.2rem;
              margin-right: .35em;
              transition: color 200ms;
            }
        }

        .actions.rubric-actions {
            margin-bottom: .75em;
        }

        .actions.category-actions {
            padding: .5em .1em;
        }

        .criterium-details-wrapper {
            align-items: flex-start;
            background: change_color($modal-bg, $alpha: 0.66);
            display: flex;
            height: 100vh;
            justify-content: center;
            left: 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 20;
        }

        .criterium-details {
            background-color: $bg-color;
            border-radius: $border-radius;
            margin-top: 1.5em;
            max-width: 50em;
            padding: 1.2em;
            width: 95%;

            label {
                color: hsla(206, 25%, 30%, 1);
            }
        }
    }

    @media only screen and (min-width: 900px) {

        /** App **/

        #app.builder-app {
            height: calc(100vh - 166px);
            overflow: hidden;
            padding-bottom: 0;
        }

        .no-drop {
            opacity: 0.3;
        }

        /** Action Menu **/

        .btn-toggle-menu {
            align-items: center;
            background: transparent;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #777;
            cursor: pointer;
            display: flex;
            font-size: 1.1rem;
            height: 1.8em;
            justify-content: center;
            transition: all 200ms;
            width: 1.8em;

            &.is-menu-visible {
                opacity: 1;
            }

            &:focus {
                border: 1px solid $input-color-focus;
                outline: none;
            }

            i {
                padding-top: 2px;
                pointer-events: none;

                &.is-menu-visible {
                    padding-top: 0;
                }
            }
        }

        @media (any-hover: hover) {
            .btn-toggle-menu.mod-category, .btn-toggle-menu.mod-criterium {
                opacity: 0;

                &:focus {
                    opacity: 1;
                }
            }
        }

        .app-container:not(.is-dragging) .item-header-bar:hover .btn-toggle-menu {
            opacity: 1;
        }

        .action-menu {
            background: #fff;
            display: none;
            min-width: 120px;
            position: absolute;
            width: 9em;
            z-index: 10;

            &.is-menu-visible {
                display: flex;
            }

            &.mod-menu-fixed {
                position: fixed;
            }
        }

        .action-menu-list {
            background-color: #fff;
            border-radius: $border-radius;
            box-shadow: 0 0 3px #999;
            flex-direction: column;
            list-style: none;
            overflow: hidden;
            position: absolute;
        }

        .action-menu-list-item {
            border-radius: 0;
            cursor: pointer;
            font-size: 1.3rem;
            margin-right: 0;
            padding: .25em .5em;
            pointer-events: all;

            &:hover {
                background: #ddd;
            }

            &:focus {
                background: $btn-color;

                .action-menu-icon, .action-menu-text {
                    color: white;
                }
            }
        }

        .action-menu-icon {
            color: #666;
            margin-right: .3em;
        }

        .action-menu-text {
            color: #333;
            opacity: 1;
            position: initial;
            width: initial;
        }


        /** Name Input **/

        .name-input {
            flex-direction: column;
            font-weight: 400;
            margin-bottom: 1em;
            margin-top: 0;

            &.cluster-new {
                background: #fff;
                box-shadow: 0 2px 10px #999;
                margin-bottom: 0;
                padding: 1px .25em .4em .25em;
                position: fixed;
                width: 18em;

                .name-input-field.mod-textarea {
                    height: 35px;
                    margin-top: .1em;
                    padding-top: .6em;
                }
            }

            &.mod-edit {
                box-shadow: 0 3px 10px #666;
                margin-left: -2%;
                margin-top: -.25em;
                width: 104%;
            }
        }

        .name-input-actions {
            margin-left: 0;
            margin-top: .4em;
        }

        .rubric-actions {
            position: relative;

            &.is-open {
                align-self: flex-start;
            }
        }

        /** Clusters collapse Menu **/

        .btn-collapse {
            display: none;
        }

        .clusters-collapse {
            overflow: hidden;
            position: initial;
            @include scrollbar();

            &::-webkit-scrollbar {
                height: 6px;
            }
        }

        /** Rubric Editor **/

        .rubrics {
            flex: 1;
            margin: 0;
            max-width: initial;
            position: relative;
            width: initial;
        }

        .rubrics-wrapper {
            bottom: 0;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
        }

        .app-container {
            display: flex;
            height: 100%;
            width: 100%;

            &.is-dragging {
                * {
                    cursor: move;
                    cursor: grabbing;
                }

                &.is-not-allowed, &.is-not-allowed * {
                    cursor: not-allowed;
                }
            }
        }

        .clusters-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow-x: hidden;
            padding-left: 1em;

            &.is-split {
                height: 50%;
            }
        }

        #clusters-wrapper-view2 {
            border-top: 1px solid $panel-border-divider-color;
        }

        .rubric-panes-wrapper {
            flex: 1;
            position: relative;
        }

        .rubric-panes {
            bottom: 0;
            left: 0;
            overflow: hidden;
            position: absolute;
            right: 0;
            top: 0;

            &.is-criterium-selected {
                display: block;
            }
        }

        .app-container.is-dragging .actions {
            opacity: 0;
            transition: opacity 300ms;
        }

        /** Clusters **/

        .item-header-bar {
            cursor: pointer;
        }

        .clusters-view {
            align-items: baseline;
            border-bottom: 1px solid transparent;
            display: flex;
            margin-left: -1em;
            margin-top: .5em;
            transition: border-bottom-color 200ms;
        }

        .clusters-view {
            position: relative;

            &::before {
                background: hsla(194, 15%, 77%, 1);
                bottom: 0;
                content: '';
                display: block;
                height: 1px;
                position: absolute;
                width: 100%;
            }
        }

        .b-clusters {
            display: flex;
            margin-bottom: -1px;
            margin-left: 1em;
            max-width: calc(100% - 23em);
        }

        .b-cluster-list-item {
            /* A border is needed here so the ghost item can show the dotted border without flicking the content below */
            border: 1px solid transparent;
            flex: 1;
            margin-right: .65em;
            overflow: hidden;

            &.ghost {
                background: $ghost-bg-color;
                border: 1px dotted $ghost-border;
                border-radius: $border-radius;

                &:after, > * {
                    visibility: hidden;
                }
            }

            &:hover .btn-toggle-menu {
                color: #666;

                &.is-menu-visible {
                    color: #fff;
                }
            }

            &:focus .b-cluster {
                border-color: transparent;
            }
        }

        .b-cluster {
            background-color: hsla(160, 6%, 90%, 1);
            border: 1px solid hsla(197, 10%, 86%, 1);
            border-radius: $border-radius;
            color: hsla(200, 10%, 38%, 1);
            font-size: 1.35rem;
            padding: .3em 0 .4em .5em;
            white-space: nowrap;

            &.is-selected {
                background: $bg-color;
                border-color: hsla(194, 15%, 77%, 1);
                color: hsla(190, 45%, 38%, 1);
                font-weight: bold;
            }

            &:hover, &.is-menu-visible {
                background: hsla(210, 30%, 75%, .3);
            }

            .name-input {
                width: 18em;
            }
        }

        .action-menu.mod-cluster { /* Position dynamically set currently so this has no effect */
            right: -7.4em;
            top: 4.6em;
        }

        .btn-toggle-menu.mod-cluster {
            &:hover, &.is-menu-visible {
                background: change_color($cluster-item-actions-bg-color, $alpha: 0.5);
                color: #fff;
            }
        }

        .b-cluster-title {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .b-cluster {
            border-bottom-color: transparent;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;

            &.is-selected {
                border-bottom-color: $bg-color;
                height: 3.6rem;
                margin-bottom: 0;
            }
        }

        .b-cluster-selected-title {
            position: absolute;
            visibility: hidden;
        }

        /** Categories **/

        .selected-fade {
            &-leave-active, &-enter-active {
                transition: opacity 200ms ease-in-out;/*, transform 200ms;*/
            }

            &-enter-active .criterium-details {
                border: 2px solid $btn-color;
                height: 100%;
            }

            &-enter, &-leave-to {
                opacity: 0;
            }
        }

        .border-flash {
            &-enter-active, &-leave-active {
                animation-duration: 200ms;
                height: 100%;
                transition-duration: 200ms;

                > div {
                    transition: opacity 200ms ease-in-out;
                }
            }

            &-enter, &-leave-to {
                > div {
                    opacity: 0;
                }
            }

            &-enter-active {
                animation-name: bf-in;
            }

            &-leave-active {
                animation-name: bf-out;
            }
        }

        @keyframes bf-in {
            0% { border-color: $btn-color; }
            100% { border-color: transparent; }
        }

        @keyframes bf-out {
            0% { border-color: transparent; }
            100% { border-color: $btn-color; }
        }

        .cluster-content {
            display: flex;
            flex: 1;
            margin-left: calc(-.5em - 7px);
            margin-top: 1em;
            overflow: auto;
            padding-left: calc(.5em + 7px);
            padding-top: .2em;
            @include scrollbar();
        }

        .criteria-empty-list {
            border-left: 1px solid hsla(193, 14%, 82%, 1);
            border-right: 1px solid hsla(193, 14%, 82%, 1);
            color: hsla(204, 45%, 25%, 0.6);
            font-style: italic;
            padding: .5em .5em 0;
            text-align: center;
        }

        .b-categories {
            display: flex;
        }

        .b-category {
            margin-right: 1.2em;
            width: 19em;

            &.ghost {
                background: $ghost-bg-color;
                border: 1px dotted $ghost-border;
                border-radius: $border-radius;
                height: 5.5em;

                > * {
                    visibility: hidden;
                }
            }

            .name-input .name-input-field.mod-textarea {
                font-size: 1.33rem;
            }
        }

        .action-menu.mod-category {
            right: -10.35em;
            top: 4.8em;
            width: 12em;
        }

        .b-category-list-item.mod-null-category-drag {
            opacity: .5;
            transition: opacity 200ms;
        }

        .b-category:hover .b-category-list-item {
            opacity: 1;
        }

        .b-category-header-wrapper {
            align-items: stretch;
            padding-left: 1em;
        }

        .b-category-title {
            color: hsla(190, 50%, 29%, 1);
            flex: 1;
            font-size: 1.33rem;

            &.mod-null-category {
                font-style: oblique;
                opacity: 0.8;
            }

            &.mod-null-category-drag {
                font-style: oblique;
                margin-left: .5em;
            }
        }

        .actions.category-actions {
            border: 1px solid hsla(193, 14%, 82%, 1);
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
            border-top: none;
            padding: .238em 0 .238em .5em;
        }

        .b-category-list-item {
            background: linear-gradient(to bottom,
                hsla(60, 2%, 91%, 0.6) 0px,
                hsla(60, 2%, 91%, 0) 14px,
                hsla(204, 38%, 40%, 0) 0px,
                hsla(204, 38%, 40%, 0.12) 36px);
            background: hsla(204, 38%, 40%, 0.12);
            border: 1px solid hsla(193, 14%, 82%, 1);
            border-top-left-radius: $border-radius;
            border-top-right-radius: $border-radius;
            padding: 0;
        }

        .item-header-bar.mod-category {
            padding: .65em 0;
            position: relative;
        }

        .btn-toggle-menu.mod-category {
            margin-left: .5em;
            padding: 2px 0;

            &:hover, &.is-menu-visible {
                background: $item-actions-bg-color;
            }
        }

        .btn-category-color {
            margin: 0;
            margin-left: -7px;
            position: absolute;
        }

        /* Without the filler it's very hard to drag a category into an empty cluster */
        .b-category-drag-filler {
            transition: all 1ms;
            width: 0;
        }

        .category-dragging .b-category-drag-filler {
            background: transparent;
            flex: 0;
            height: 200px;
            margin-right: 0;
            transition-delay: 300ms;
            width: 18em;
        }

        .category-name-input {
            margin-top: .1em;
            width: 18em;
        }

        .actions.cluster-actions {
            padding-top: .4em;
        }


        /** Criteria **/

        .b-criterium-list-item.ghost {
            background-color: $ghost-bg-color;
            border: 1px dotted $ghost-border;

            .b-criterium-title {
                color: transparent;
            }
        }

        .b-criterium {
            background: none;
            /*background: linear-gradient(to top, rgba(0, 0, 0, 0.05) 0px, rgba(0, 0, 0, 0) 14px);*/
            border-bottom: 1px solid hsla(193, 14%, 85%, 1);
            border-left: 1px solid hsla(193, 14%, 82%, 1);
            border-right: 1px solid hsla(193, 14%, 82%, 1);
            font-size: 1.3rem;

            &.is-selected {
                background: $bg-criterium-details;
                border: 2px solid $btn-color;
                margin-top: -1px;
            }

            .menu-list-item-details {
                display: none; /* Don't remember why this is here */
            }
        }

        .action-menu.mod-criterium {
            right: -7.35em;
            top: 4.8em;
            width: 9em;
        }

        .b-criterium-title {
            color: hsla(204, 45%, 25%, 1);
        }

        .item-header-bar.mod-criterium {
            align-items: baseline;
            width: 100%;
        }

        .btn-toggle-menu.mod-criterium {
            margin-left: .5em;
            padding: 2px 0;

            &:hover, &.is-menu-visible {
                background: $item-actions-bg-color;
            }
        }

        .b-criterium-title-wrapper {
            display: flex;
            padding: 0;

            &:focus .b-criterium-title {
                outline-offset: 0;
                z-index: 10;
            }
        }

        .b-criterium-title {
            flex: 1;
            min-height: 3.419rem;
            padding: .7em 0 .7em 1em;
        }

        .btn-new.mod-category-add {
            padding-left: 0;
        }


        /** Vue Swatches **/

        .vue-swatches {
            margin-bottom: 0;
            width: 100%;
        }

        .vue-swatches__container {
            padding: 0!important;
        }

        .vue-swatches__wrapper {
            padding: 0!important;
        }

        /** Criterium Details Editor **/

        .criterium-details-wrapper {
            @include scrollbar();
            background-color: $bg-criterium-details;
            border-left: 1px solid hsla(194, 15%, 77%, 1);
            border-top: 1px solid hsla(194, 15%, 77%, 1);
            border-top-left-radius: $border-radius;
        }

        .criterium-details {
            border: 2px solid transparent;
            overflow-x: hidden;
            overflow-y: auto;
            padding: calc(1em - 2px);
            width: 30em;

            .input-detail {
                background-color: rgba(255, 255, 255, 0.35);
                border: 1px solid #ccc;

                &:hover {
                    background-color: rgba(255, 255, 255, 1);
                    border: 1px solid #aaa;
                }

                &:focus {
                    border: 1px solid $input-color-focus;
                }
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
</style>
