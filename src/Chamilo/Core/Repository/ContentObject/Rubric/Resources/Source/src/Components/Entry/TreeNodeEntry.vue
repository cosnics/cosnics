<i18n>
{
    "en": {
        "adjusted-score": "Adjusted score",
        "no-description": "No description",
        "points": "points"
    },
    "fr": {
        "adjusted-score": "Score personnalisé",
        "no-description": "Pas de description",
        "points": "points"
    },
    "nl": {
        "adjusted-score": "Aangepaste score",
        "no-description": "Geen omschrijving",
        "points": "punten"
    }
}
</i18n>

<template>
    <div class="treenode-choices">
        <div class="treenode-choice" style="position: relative" :class="{'mod-show-description': showDescription, 'mod-empty-description': showDescription && !description}" v-for="({ title, description, score, markdown, level, isSelected }, index) in entryChoices">
            <component :is="preview ? 'div' : 'button'" class="treenode-level" :class="{'mod-fixed-levels': !rubric.hasCustomLevels, 'is-selected': isSelected, 'mod-btn': !preview, 'mod-error': hasLevelError(level) }" @click.stop="onSelect(level)" :disabled="hasLevelError(level)">
                <span class="treenode-level-title" :class="{'mod-fixed-levels': !rubric.hasCustomLevels}">{{ title }}</span>
                <span v-if="useScores" class="treenode-level-score">
                    <template v-if="level.useRangeScore">{{ level.minimumScore|formatNum }} <i class="fa fa-caret-right"></i></template>
                    {{ score|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span v-else class="sr-only">{{ $t('points') }}</span></span>
                <span v-else><i class="treenode-level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }"></i></span>
            </component>
            <div v-if="useScores && currentEvaluation && evaluation === currentEvaluation && level === currentEvaluation.level && level.useRangeScore"
                style="position: relative;height:44px;">
                <div @click.stop="" class="score-range" :class="{'mod-first': index === 0}" style="width: 220px;left:calc(50% - 110px);gap:.75rem;justify-content: center">
                    <label for="adjusted-score" style="font-weight: 400;margin-bottom:0">{{ $t('adjusted-score') }}:</label>
                    <input id="adjusted-score" ref="range-input" type="number" v-model.number="currentEvaluation.score" :min="level.minimumScore" :max="level.score" required step="1" @input="onUpdateRangeScore(level)" class="input-detail mod-range">
                </div>
                <!--<div @click.stop="" class="score-range" :class="{'mod-first': index === 0}" style="width: 260px;left:calc(50% - 130px);gap:.5rem;justify-content: center" v-if="level.score - level.minimumScore <= 6">
                    <button v-for="(_, i) in (level.score - level.minimumScore + 1)"
                            @click.stop="() => {currentEvaluation.score = level.minimumScore + i; onUpdateRangeScore(level);}"
                            class="btn-range" :class="{'is-selected': currentEvaluation.score === level.minimumScore + i}">{{ level.minimumScore + i }}</button>
                </div>
                <div v-else @click.stop="" class="score-range" :class="{'mod-first': index === 0}">
                    <input type="range" v-model.number="currentEvaluation.score" :min="level.minimumScore" :max="level.score" @input="onUpdateRangeScore(level)">
                </div>-->
            </div>
            <template v-if="showDescription">
                <div v-if="description" class="treenode-level-description is-feedback-visible" v-html="markdown"></div>
                <div v-else class="treenode-level-description mod-no-default-feedback is-feedback-visible"><em>{{ $t('no-description') }}</em></div>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import {TreeNodeEvaluation, TreeNodeExt} from '../Util/interfaces';
    import Rubric from '../Domain/Rubric';
    import RubricEvaluation from '../Domain/RubricEvaluation';
    import Level from '../Domain/Level';
    import {LevelEntryChoice, ChoiceEntryChoice} from '../Domain/EntryChoice';

    @Component({
        filters: {
            formatNum: function (v: number|null) {
                if (v === null) { return ''; }
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeEntry extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Object, required: true}) readonly ext!: TreeNodeExt;
        @Prop({type: Object, default: null}) readonly evaluation!: TreeNodeEvaluation|null;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showDefaultFeedbackFields!: boolean;
        @Prop({type: Object, default: null}) readonly currentEvaluation!: TreeNodeEvaluation|null;

        get hasLevels(): boolean {
            return !!this.ext.levels.length;
        }

        get useScores(): boolean {
            return this.rubric.useScores;
        }

        get showDescription(): boolean {
            return this.showDefaultFeedbackFields || this.ext.showDefaultFeedback;
        }

        get chosenLevel() {
            if (this.preview) { return null; }
            return this.evaluation?.level || null;
        }

        get entryChoices() {
            if (this.hasLevels) {
                return this.ext.levels.map(level => new LevelEntryChoice(this.rubric, level, this.chosenLevel));
            }
            return this.ext.choices.map(choiceObject => new ChoiceEntryChoice(this.rubric, choiceObject, this.chosenLevel));
        }

        get hasRangeError() {
            return RubricEvaluation.isInvalidEvaluation(this.evaluation);
        }

        hasLevelError(level: Level) {
            if (!level.useRangeScore) { return false; }
            if (level.minimumScore === null) { return true; }
            if (level.minimumScore >= level.score) { return true; }
            return false;
        }

        onSelect(level: Level) {
            if (!this.preview) {
                this.$emit('select', this.evaluation, level);
                this.$nextTick(() => {
                    if (!this.$refs['range-input']) { return; }
                    const input = this.$refs['range-input'] as HTMLInputElement[];
                    input[0]?.focus();
                });
            }
        }

        onUpdateRangeScore(level: Level) {
            if (!this.preview) {
                this.$emit('range-level-score', this.evaluation, level);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .treenode-choice.mod-show-description {
        background: #fafafa;
        border-bottom: 1px solid #e0e0e0;
        border-radius: $border-radius;
        margin-bottom: .7rem;

        &.mod-empty-description {
            border-bottom: 1px solid #f0f0f0;
        }
    }

    .treenode-level {
        align-items: flex-start;
        background: #e6e6e6;
        border: 1px solid transparent;
        border-bottom-color: $score-light;
        border-radius: $border-radius;
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        padding: .1rem .6rem;
        transition: 200ms background;
        width: 100%;

        .fa-percent {
            font-size: 1rem;
            margin-left: 0;
            opacity: .6;
        }

        &.is-selected {
            background: $level-selected-color;
            color: #fff;

            .fa-percent {
                opacity: .75;
            }
        }

        &.mod-btn {
            cursor: pointer;
            outline: none;

            &:hover, &:focus {
                border: 1px solid $level-selected-color;

                .treenode-level-icon-check {
                    opacity: .5;
                }
            }

            &.is-selected {
                &:hover, &:focus {
                    box-shadow: inset 0 0 0 1px white;

                    .treenode-level-icon-check {
                        opacity: 1;
                    }
                }
            }
        }

        &.mod-error {
            background: hsl(0, 96%, 91%);
            cursor: not-allowed;

            &:hover {
                border-color: transparent;
            }
        }
    }

    .treenode-level-title {
        display: block;
        font-size: 1.3rem;
        line-height: 1.8rem;
        padding: .25rem 0;
    }

    .treenode-level-score {
        font-size: 1.8rem;
        white-space: nowrap;
    }

    .fa-caret-right {
        font-size: 1.5rem;
        margin: 0 -.25rem;
    }

    .treenode-level:not(.is-selected) .treenode-level-score {
        color: #36717d;

        .fa-caret-right {
            color: hsla(190, 18%, 59%, .75);
        }
    }

    .treenode-level:not(.is-selected), .treenode-level {
        &.mod-error .treenode-level-score {
            color: #e21414;

            .fa-caret-right {
                color: #e21414;
            }
        }
    }

    .score-range {
        align-items: center;
        background: #fff;
        border-radius: 3px;
        box-shadow: hsla(204, 38%, 34%, .4) 0 5px 15px;
        display: flex;
        gap: 1rem;
        height: 44px;
        left: calc(50% - 120px);
        padding: 1rem;
        position: absolute;
        top: 3px;
        /*top: -50px;*/
        width: 240px;
        z-index: 1;

        @media only screen and (min-width: 680px) and (max-width: 899px) {
            &.mod-first {
                left: -4rem;
            }
        }
    }

    .btn-range {
        border: 1px solid #ddd;
        border-radius: 3px;
        color: #36717d;
        flex: 1;

        &:hover {
            border-color: #6195b8;
        }

        &.is-selected {
            background-color: #6195b8;
            border-color: #6195b8;
            color: #fff;
        }
    }

    .input-detail:invalid {
        border: 1px solid #e10505;
        color: #e10505;

        &:focus {
            box-shadow: none;
        }
    }

    @media only screen and (min-width: 680px) {
        .treenode-level.mod-fixed-levels {
            height: 2.7rem;
            justify-content: center;
        }

        .treenode-level-title.mod-fixed-levels {
            display: none;
        }
    }
</style>