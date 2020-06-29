<template>
    <div @click="selectLevel(null)" @keydown.esc="hideRemoveLevelDialog">
        <div class="levels-container" :class="{ 'has-new': !!newLevel/*, 'show-description': showLevelDescriptions */}">
            <h1>Niveaus</h1>
            <ul class="levels-list">
                <level-details v-for="(level, index) in rubric.levels" :has-new="!!newLevel" :selected-level="selectedLevel" :rubric="rubric" :level="level" tag="li" :key="`level_${index}`" @change="onLevelChange" @level-move-up="moveLevelUp" @level-move-down="moveLevelDown" @level-selected="selectLevel" @level-default="setDefault" @level-remove="showRemoveLevelDialog"></level-details>
                <li v-if="!newLevel" class="level-new">
                    <button class="btn-new" @click.stop="createNewLevel">Niveau toevoegen</button>
                </li>
                <level-details v-else :selected-level="newLevel" :has-new="true" :is-new="true" :rubric="rubric" :level="newLevel" tag="li" :key="`level_${rubric.levels.length}`" @new-level-added="addLevel" @new-level-canceled="cancelLevel" @level-default="setDefault"></level-details>
            </ul>
        </div>
        <div class="modal-bg" v-if="removingLevel !== null" @click.stop="hideRemoveLevelDialog">
            <div class="modal-content" @click.stop="">
                <div class="modal-content-title">Niveau '{{ removingLevel.title }}' verwijderen?</div>
                <div>
                    <button class="btn-strong mod-confirm" ref="btn-remove-level" @click.stop="removeLevel(removingLevel)">Verwijder</button>
                    <button class="btn-strong" @click.stop="hideRemoveLevelDialog">Annuleer</button>
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
    }

    .levels-container {
        position: relative;
        width: 100%;
        margin-left: .75em;

        h1 {
            font-size: 2.2rem;
            margin-left: .5em;
            margin-top: .3em;
            color: #666;
        }
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
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        justify-content:space-between;
        padding-left:.5em;
        padding-top: 7px;
        padding-bottom: 7px;
        border: 1px solid transparent;
        border-top-color: hsla(214, 20%, 85%, 1);
        transition: background-color 200ms;

        .input-detail {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid transparent;
        }

        &.selected {
            background-color: #d4d8de;
            border-color: hsla(214, 20%, 80%, 1);

            .input-detail {
                background: white;
            }

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

        &:not(:first-child) .label-hidden, .ld-description .label-hidden {
            position: absolute;
            left: -10000px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
    }

    .level-details-text {
        flex: 1;
        margin-right: .5em;
    }

    .level-details-text-1 {
        display: flex;
    }

    /*.btn-more {
        width: 1.4em;
        cursor: pointer;
        color: #bbb;
        background: none;
        border: 1px solid transparent;
        display: flex;
        justify-content: center;
        align-self: flex-end;margin-bottom:.3em;

        .check {
            transform: rotate(0deg);
            transition: transform 200ms;
        }

        .check:focus {
            outline: none;
        }

        &:focus {
            outline: none;

            .check {
                border: 1px solid $input-color-focus;
            }
        }

        .check::before {
            content: '\f078';
        }

        &:hover {
            color: #999;
        }
    }*/

    .levels-container.has-new .level-details:not(.new-level) {
        pointer-events: none;
    }

    /*.show-description .btn-more .check {
        transform: rotate(-180deg);
    }*/

    .input-detail {
        background: rgba(255,255,255,0.2);
        border: 1px solid transparent;
        border-radius: $border-radius;
        padding: 2px 5px;

        &:hover {
            background: white;
            border-color: lighten($input-color-focus, 15%);
        }

        &:focus {
            outline: none;
            background: white;
            border-color: $input-color-focus;
        }
    }

    .level-label {
        color: lighten($title-color, 10%);
    }

    .ld-title {
        display: flex;
        flex-direction: column;
        width: 100%;
        margin-right: .5em;

        .level-label {
            margin-left: 5px;
            position: absolute;
            transform: translateY(-2.3em);
        }

        .input-detail {
            font-size: 1.4rem;
            color: #555;
            /*background: lighten($btn-level-delete, 9%);*/
        }
    }

    .ld-description {
        display: none;
        flex-direction: column;
        margin-top: .25em;
        position: relative;
        margin-left: .5em;

        /*.level-label {
            position: absolute;
            font-size: 1.2rem;
            left: 5px;
            color: #707070;
        }*/

        .input-detail {
            resize: none;
            /*padding-top: 1.3em;*/
            margin-right: .5em;
        }
    }

    .selected .ld-description {
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
        display: flex;
        flex-direction: column;
        width: 6em;
        align-items: center;

        .level-label {
            position: absolute;
            transform: translateY(-2.3em);
        }

        .input-detail {
            display:block;
            opacity: 0;
            transform: translateY(2px);

            & + label::before {
                transform: translateY(-10px);
                font-size: 1.4rem;
                content: '\f1db';
                display: block;
                color: #bbb;
                height: 0;
                cursor: pointer;
            }

            &:focus + label::before, &:hover + label::before {
                color: #999;
            }

            + label.checked::before {
                content: '\f00c';
                color: lighten(#406e8d, 10%);
            }

            &:focus + label::before {
                color: #406e8d;
            }
        }
    }

    .level-actions {
        display: none;
        position: absolute;
        top: 0;
        right: 0;

        .btn-level-action {
            background: transparent;
            border: 1px solid transparent;
            font-size: 1.9rem;

            &:focus {
                outline: none;
                border: 1px solid $input-color-focus;
                border-radius: $border-radius;
            }

            &:not(:last-child) {
                margin-right: 0;
            }
        }
    }

    .selected:not(.new-level) .level-actions {
        display: block;

        .btn-level-action {
            i {
                color: #aaa;
                transition: color 240ms;
            }

            &:hover i, &:focus i {
                color: $btn-color;
            }

            &.btn-delete:hover i, &.btn-delete:focus i {
                color: #d9534f;
            }

            &[disabled] {
                & i, &:hover i, &:focus i {
                    color: #ccc;
                }
            }
        }
    }

    @media only screen and (min-width: 660px) {
        .level-actions {
            top: 0;
            right: -3em;
            height: 100%;

            > div {
                display: flex;
                flex-direction: column;
                margin-top: 3em;
                height: calc(100% - 3em);
                justify-content: center;
            }
        }
    }

    .level-new {
        padding-left: .5em;
    }

    .level-details .actions {
        margin-top: .5em;
    }
</style>