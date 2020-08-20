<i18n>
{
    "en": {
        "add-level": "Add Level",
        "cancel": "Cancel",
        "levels": "Levels",
        "remove": "Remove",
        "remove-level": "Remove level {item}"
    },
    "fr": {
        "add-level": "Ajouter un niveau",
        "cancel": "Annuler",
        "levels": "Niveaux",
        "remove": "Supprimer",
        "remove-level": "Supprimer le niveau {item}"
    },
    "nl": {
        "add-level": "Niveau toevoegen",
        "cancel": "Annuleer",
        "levels": "Niveaus",
        "remove": "Verwijder",
        "remove-level": "Niveau {item} verwijderen"
    }
}
</i18n>

<template>
    <div @click="selectLevel(null)" @keydown.esc="hideRemoveLevelDialog">
        <div class="levels-container" :class="{ 'has-new': !!newLevel/*, 'show-description': showLevelDescriptions */}">
            <h1 class="levels-title">{{ $t('levels') }}</h1>
            <ul class="levels-list">
                <level-details v-for="(level, index) in rubric.levels" :has-new="!!newLevel" :selected-level="selectedLevel" :rubric="rubric" :level="level" tag="li" :key="`level_${index}`" @change="onLevelChange" @level-move-up="moveLevelUp" @level-move-down="moveLevelDown" @level-selected="selectLevel" @level-default="setDefault" @level-remove="showRemoveLevelDialog"></level-details>
                <li v-if="!newLevel" class="level-new">
                    <button class="btn-new" @click.stop="createNewLevel">{{ $t('add-level') }}</button>
                </li>
                <level-details v-else :selected-level="newLevel" :has-new="true" :is-new="true" :rubric="rubric" :level="newLevel" tag="li" :key="`level_${rubric.levels.length}`" @new-level-added="addLevel" @new-level-canceled="cancelLevel" @level-default="setDefault"></level-details>
            </ul>
        </div>
        <div class="modal-bg" v-if="removingLevel !== null" @click.stop="hideRemoveLevelDialog">
            <div class="modal-content" @click.stop="">
                <div class="modal-content-title">{{ $t('remove-level', {item: `'${removingLevel.title}'`}) }}?</div>
                <div>
                    <button class="btn-strong mod-confirm" ref="btn-remove-level" @click.stop="removeLevel(removingLevel)">{{ $t('remove') }}</button>
                    <button class="btn-strong" @click.stop="hideRemoveLevelDialog">{{ $t('cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import LevelDetails from './LevelDetails.vue';
    import DataConnector from '../Connector/DataConnector';

    @Component({
        name: 'levels-view',
        components: {
            LevelDetails
        },
    })
    export default class LevelsView extends Vue {
        private newLevel: Level|null = null;
        private selectedLevel: Level|null = null;
        private removingLevel: Level|null = null;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        /*@Prop({type: Boolean, default: false }) readonly showLevelDescriptions!: boolean;*/
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

        constructor() {
            super();
            this.onLevelMove = debounce(this.onLevelMove, 750);
        }

        createNewLevel() {
            this.selectLevel(null);
            this.newLevel = this.getDefaultLevel();
            this.$nextTick(() => {
                (document.querySelector(`#level_title_${this.rubric.levels.length}`)! as HTMLElement).focus();
            });
        }

        addLevel() {
            if (this.newLevel!.isDefault) {
                this.rubric.levels.forEach(level => {
                    level.isDefault = false;
                });
            }
            this.rubric.addLevel(this.newLevel!);
            this.dataConnector?.addLevel(this.newLevel!, this.rubric.levels.length);
            this.newLevel = null;
        }

        cancelLevel() {
            this.newLevel = null;
            this.selectLevel(null);
        }

        onLevelMove(level: Level) {
            const index = this.rubric.levels.indexOf(level);
            this.dataConnector?.moveLevel(level, index);
        }

        moveLevelUp() {
            if (!this.selectedLevel) { return; }
            this.rubric.moveLevelUp(this.selectedLevel);
            this.onLevelMove(this.selectedLevel);
            this.$nextTick(() => {
                (document.querySelector(`#level_move_up_${this.selectedLevel!.id}`)! as HTMLElement).focus();
            });
        }

        moveLevelDown() {
            if (!this.selectedLevel) { return; }
            this.rubric.moveLevelDown(this.selectedLevel);
            this.onLevelMove(this.selectedLevel);
            this.$nextTick(() => {
                (document.querySelector(`#level_move_down_${this.selectedLevel!.id}`)! as HTMLElement).focus();
            });
        }

        onLevelChange(level: Level) {
            this.dataConnector?.updateLevel(level);
        }

        selectLevel(level: Level|null) {
            if (this.newLevel) { return false; }
            this.selectedLevel = level;
            return false;
        }

        setDefault(defaultLevel: Level) {
            if (this.newLevel === defaultLevel) {
                this.newLevel.isDefault = !this.newLevel.isDefault;
            } else {
                this.rubric.levels.forEach(level => {
                    level.isDefault = (defaultLevel === level) ? !level.isDefault : false;
                });
            }
        }

        getDefaultLevel() {
            return new Level('');
        }

        showRemoveLevelDialog(level: Level|null) {
            this.removingLevel = level;
        }

        hideRemoveLevelDialog() {
            this.showRemoveLevelDialog(null);
        }

        removeLevel(level: Level) {
            this.removingLevel = null;
            this.rubric.removeLevel(level);
            this.dataConnector?.deleteLevel(level);
            this.selectLevel(null);
        }

        @Watch('removingLevel')
        onRemoveItemChanged() {
            if (this.removingLevel) {
                this.$nextTick(() => {
                    (this.$refs['btn-remove-level'] as HTMLElement).focus();
                });
            }
        }
    }
</script>
<style lang="scss">
    .rubrics-wrapper-levels {
        margin-left: -1.5em;
        max-width: 52em;
        width: 100%;
    }

    .levels-container {
        margin-left: .75em;
        position: relative;
    }

    .levels-title {
        color: #666;
        font-size: 2.2rem;
        margin-left: .5em;
        margin-top: .3em;
    }

    @media only screen and (min-width: 900px) {
        .rubrics-wrapper-levels {
            margin-left: 0;
        }

        .levels-container {
            width: 50em;
        }
    }

    .levels-list {
        list-style: none;
        margin: 2.5em 0 0 0;
        padding: 0;
    }

    .level-details {
        border: 1px solid transparent;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        padding: 7px 0 7px .5em;
        transition: background-color 200ms;
        width: 100%;

        /*.input-detail {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid transparent;
        }*/

        &.is-selected {
            background-color: #d4d8de;

            /*.input-detail {
                background: white;
            }*/

            + .level-details {
                border-top-color: transparent;
            }

            /*.btn-more:not(:hover) {
                color: #aaa;
            }

            .btn-more:hover {
                color: #888;
            }*/
        }

        &:not(:first-child) .label-hidden /*, .ld-description .label-hidden*/ {
            height: 1px;
            left: -10000px;
            overflow: hidden;
            position: absolute;
            top: auto;
            width: 1px;
        }
    }

    .level-details-text {
        flex: 1;
        margin-right: .5em;
    }

    .level-details-text-1 {
        display: flex;
    }

    .levels-container.has-new .level-details:not(.new-level) {
        pointer-events: none;
    }

    .input-detail {
        background-color: hsla(190, 50%, 96%, 1);
        border: 1px solid #d4d4d4;
        border-radius: $border-radius;
        padding: 2px 5px;

        &:hover, &:focus {
            background-color: #fff;
        }

        &:hover {
            border: 1px solid #aaa;
        }

        &:focus {
            border: 1px solid $input-color-focus;
            outline: none;
        }
    }

    .level-label {
        color: lighten($title-color, 10%);
    }

    .ld-title {
        display: flex;
        flex-direction: column;
        margin-right: .5em;
        width: 100%;

        .level-label {
            margin-left: 5px;
            position: absolute;
            transform: translateY(-2.3em);
        }

        .input-detail {
            /*background: lighten($btn-level-delete, 9%);*/
            color: #555;
            font-size: 1.4rem;
        }
    }

    .ld-description {
        display: none;
        flex-direction: column;
        margin-left: .5em;
        margin-top: .25em;
        position: relative;

        /*.level-label {
            color: #707070;
            font-size: 1.2rem;
            left: 5px;
            position: absolute;
        }*/

        .input-detail {
            margin-right: .5em;
            /*padding-top: 1.3em;*/
            resize: none;
        }
    }

    .is-selected .ld-description {
        margin-left: 0;
        margin-top: .5em;
    }

    .ld-description {
        display: flex;
    }

    .ld-score {
        display: flex;
        flex-direction: column;
        width: 5em;

        .level-label {
            position: absolute;
            transform: translate(.75em, -2.3em);
        }

        .input-detail {
            font-size: 2rem;
            text-align: right;
        }
    }

    .ld-default {
        align-items: center;
        display: flex;
        flex-direction: column;
        width: 6em;

        .level-label {
            position: absolute;
            transform: translateY(-2.3em);
        }

        .input-detail {
            display: block;
            opacity: 0;
            transform: translateY(2px);

            & + label::before {
                color: #bbb;
                content: '\f1db';
                cursor: pointer;
                display: block;
                font-size: 1.4rem;
                height: 0;
                transform: translateY(-10px);
            }

            &:focus + label::before, &:hover + label::before {
                color: #999;
            }

            + label.checked::before {
                color: lighten(#406e8d, 10%);
                content: '\f00c';
            }

            &:focus + label::before {
                color: #406e8d;
            }
        }
    }

    .level-actions-wrapper {
        display: none;
        position: absolute;
        right: 0;
        top: 0;

        &.is-active {
            display: block;
        }
    }

    .btn-level-action {
        background: transparent;
        border: 1px solid transparent;
        font-size: 1.9rem;

        &:hover i, &:focus i {
            color: $btn-color;
        }

        &.btn-delete:hover i, &.btn-delete:focus i {
            color: #d9534f;
        }

        &:focus {
            border: 1px solid $input-color-focus;
            border-radius: $border-radius;
            outline: none;
        }

        &:not(:last-child) {
            margin-right: 0;
        }

        &[disabled] {
            & i, &:hover i, &:focus i {
                color: #ccc;
            }
        }

        i {
            color: #aaa;
            transition: color 240ms;
        }
    }


    @media only screen and (min-width: 660px) {
        .level-actions-wrapper {
            height: 100%;
            right: -3em;
            top: 0;
        }

        .level-actions {
            display: flex;
            flex-direction: column;
            height: calc(100% - 3em);
            justify-content: center;
            margin-top: 3em;
        }
    }

    .level-new {
        padding-left: .5em;
    }

    .level-details .actions {
        margin-top: .5em;
        width: 100%;
    }

    @media only screen and (max-width: 659px) {
        .rubrics-wrapper-levels {
            max-width: initial;
            width: initial;
        }
    }
</style>