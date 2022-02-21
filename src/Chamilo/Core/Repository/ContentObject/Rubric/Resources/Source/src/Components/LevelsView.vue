<i18n>
{
    "en": {
        "add-level": "Add Level",
        "cancel": "Cancel",
        "default": "Default",
        "default-info": "Optional choice field. The level assigned by default to a criterium.",
        "level": "Level",
        "levels": "Levels",
        "points": "Points",
        "remove": "Remove",
        "remove-level": "Remove level {item}",
        "weights-per-total": "Weights relative to total score",
        "with-scores": "With scores",
        "without-scores": "Without scores"
    },
    "fr": {
        "add-level": "Ajouter un niveau",
        "cancel": "Annuler",
        "default": "Norme",
        "default-info": "Contrôle de choix optionnel. Le niveau attribué par défaut à un critère.",
        "level": "Niveau",
        "levels": "Niveaux",
        "points": "Points",
        "remove": "Supprimer",
        "remove-level": "Supprimer le niveau {item}",
        "with-scores": "Avec scores",
        "without-scores": "Sans scores"
    },
    "nl": {
        "add-level": "Niveau toevoegen",
        "cancel": "Annuleer",
        "default": "Standaard",
        "default-info": "Optioneel keuzeveld. Het niveau dat standaard wordt toegekend aan een criterium.",
        "level": "Niveau",
        "levels": "Niveaus",
        "points": "Punten",
        "remove": "Verwijder",
        "remove-level": "Niveau {item} verwijderen",
        "weights-per-total": "Gewichten relatief tov. totaalscore",
        "with-scores": "Met scores",
        "without-scores": "Zonder scores"
    }
}
</i18n>

<template>
    <div class="levels-container">
        <h1 class="levels-title">{{ $t('levels') }}</h1>
        <div style="display: flex; gap: 1em; margin-left: .25em">
            <div><on-off-switch id="use-scores-check" class="levels-switch" :value="rubric.useScores" @input="onUseScoresChanged" :on-value="$t('with-scores')" :off-value="$t('without-scores')"></on-off-switch></div>
            <div v-if="rubric.useScores && !rubric.hasAbsoluteWeights" style="margin-left: 1.6em;">
                <button :aria-pressed="rubric.useRelativeWeights ? 'true' : 'false'" class="btn-check" :class="{ 'checked': rubric.useRelativeWeights }" @click="onUseRelativeWeightsChanged">
                    <span tabindex="-1" class="lbl-check"><i aria-hidden="true" class="btn-icon-check fa"></i>{{ $t('weights-per-total') }}</span>
                </button>
            </div>
        </div>
        <levels :rubric="rubric" :data-connector="dataConnector"></levels>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import DataConnector from '../Connector/DataConnector';
    import OnOffSwitch from './OnOffSwitch.vue';
    import Levels from './Levels.vue';

    @Component({
        name: 'levels-view',
        components: {
            OnOffSwitch, Levels
        },
    })
    export default class LevelsView extends Vue {
        private newLevel: Level|null = null;
        private selectedLevel: Level|null = null;
        private removingLevel: Level|null = null;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        /*@Prop({type: Boolean, default: false }) readonly showLevelDescriptions!: boolean;*/
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;

/*        constructor() {
            super();
            this.onLevelMove = debounce(this.onLevelMove, 750);
        }*/

        onUseScoresChanged(useScores: boolean) {
            this.rubric.useScores = useScores;
            this.dataConnector?.updateRubric(this.rubric);
            if (useScores && !this.rubric.useRelativeWeights) {
                this.rubric.hasAbsoluteWeights = Rubric.usesAbsoluteWeights(this.rubric);
            } else if (!useScores) {
                this.rubric.hasAbsoluteWeights = false;
            }
        }

        onUseRelativeWeightsChanged() {
            this.rubric.useRelativeWeights = !this.rubric.useRelativeWeights;
            this.dataConnector?.updateRubric(this.rubric);
        }

/*        createNewLevel() {
            this.selectLevel(null);
            this.newLevel = this.getDefaultLevel();
            this.$nextTick(() => {
                (document.querySelector(`#level-title-new`)! as HTMLElement).focus();
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
        }*/

        /*onLevelMove(level: Level) {
            const levels = this.rubric.getFilteredLevels(level);
            if (!levels) { return; }
            const index = levels.indexOf(level);
            this.dataConnector?.moveLevel(level, index);
        }

        moveLevelUp(level: Level) {
            this.rubric.moveLevelUp(level);
            this.onLevelMove(level);
            this.$nextTick(() => {
                let el : HTMLButtonElement|null = document.querySelector(`#btn-up-${level.id}`);
                if (el?.disabled) {
                    el = el?.nextSibling as HTMLButtonElement;
                }
                el?.focus();
            });
        }

        moveLevelDown(level: Level) {
            this.rubric.moveLevelDown(level);
            this.onLevelMove(level);
            this.$nextTick(() => {
                let el : HTMLButtonElement|null = document.querySelector(`#btn-down-${level.id}`);
                if (el?.disabled) {
                    el = el?.previousSibling as HTMLButtonElement;
                }
                el?.focus();
            });
        }*/

        /*onLevelChange(level: Level) {
            this.dataConnector?.updateLevel(level);
        }*/

        /*setDefault(defaultLevel: Level) {
            if (this.newLevel === defaultLevel) {
                this.newLevel.isDefault = !this.newLevel.isDefault;
            } else {
                this.rubric.rubricLevels.forEach(level => {
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

        get fields() {
            return [
                { key: 'title', sortable: false, variant: 'title' },
                this.rubric.useScores ? { key: 'score', sortable: false, variant: 'score' } : null,
                { key: 'is_default', sortable: false, variant: 'default' },
                { key: 'actions', sortable: false, label: '', variant: 'actions' }
            ];
        }

        rowClass(level: Level) : string {
            return `table-body-row level-row${level === this.selectedLevel ? ' is-selected' : ''}`;
        }

        onSelectLevel(level: Level, index: number = 0) {
            this.selectedLevel = level;
            (this.$refs['levels'] as unknown as any).selectRow(index);
        }

        onRowSelected(levels: Level[]) {
            this.selectLevel(levels[0] || null);
        }

        selectLevel(level: Level|null) {
            if (this.newLevel) { return false; }
            this.selectedLevel = level;
            return false;
        }

        @Watch('removingLevel')
        onRemoveItemChanged() {
            if (this.removingLevel) {
                this.$nextTick(() => {
                    (this.$refs['btn-remove-level'] as HTMLElement).focus();
                });
            }
        }*/
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
        margin-left: .25em;
        margin-top: .3em;
    }

    .levels-switch {
        width: 124px;
       /* margin-left: 1.6em;*/
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

        &::before {
            color: #406e8d;
            content: attr(item-index);
            display: block;
            font-size: 1.5rem;
            margin-right: .35em;
            padding: .15em 0;
            text-align: right;
        }

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
        background-color: hsla(190, 50%, 98%, 1);
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

            i {
                width: initial;
            }
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
        top: 3em;

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