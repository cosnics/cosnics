<i18n>
{
    "en": {
        "adjusted-score": "Adjusted score",
        "deselect": "Deselect",
        "no-description": "No description",
        "points": "points"
    },
    "fr": {
        "adjusted-score": "Score personnalisé",
        "deselect": "Désélectionner",
        "no-description": "Pas de description",
        "points": "points"
    },
    "nl": {
        "adjusted-score": "Aangepaste score",
        "deselect": "Deselecteer",
        "no-description": "Geen omschrijving",
        "points": "punten"
    }
}
</i18n>

<template>
    <div class="treenode-choices">
        <div class="treenode-choice" :class="{'mod-show-description': showDescription, 'mod-empty-description': showDescription && !description}" v-for="({ title, description, score, markdown, level, isSelected }, index) in entryChoices">
            <component :is="preview ? 'div' : 'button'" class="treenode-level" :class="{'mod-fixed-levels': !rubric.hasCustomLevels, 'is-selected': isSelected, 'mod-btn': !preview, 'mod-error': hasLevelError(level) }" @click.stop="onSelect(level)" :disabled="hasLevelError(level)">
                <span class="treenode-level-title" :class="{'mod-fixed-levels': !rubric.hasCustomLevels}">{{ title }}</span>
                <span v-if="useScores" class="treenode-level-score">
                    <template v-if="level.useRangeScore">{{ level.minimumScore|formatNum }} <i class="fa fa-caret-right"></i></template>
                    {{ score|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span v-else class="sr-only">{{ $t('points') }}</span></span>
                <span v-else><i class="treenode-level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }"></i></span>
            </component>
            <div v-if="!preview && useScores && level.useRangeScore && isSelected" class="manual-score-wrap" :class="{'mod-desc': showDescription}">
                <div @click.stop="" class="manual-score">
                    <label for="adjusted-score">{{ $t('adjusted-score') }}:</label>
                    <input id="adjusted-score" ref="manual-score" type="number" v-model.number="evaluation.score" :min="level.minimumScore" :max="level.score" required step="1" @input="onUpdateRangeScore(level)" class="input-detail mod-range">
                </div>
            </div>
            <template v-if="showDescription">
                <div v-if="description" class="treenode-level-description is-feedback-visible" v-html="markdown"></div>
                <div v-else class="treenode-level-description mod-no-default-feedback is-feedback-visible"><em>{{ $t('no-description') }}</em></div>
            </template>
        </div>
        <button v-if="!preview" class="btn-deselect" :title="$t('deselect')" :disabled="!hasSelection" :class="{'is-active': hasSelection}" @click.stop="onDeselect"><i class="gg-backspace" aria-hidden="true"></i><span class="sr-only">{{ $t('deselect') }}</span></button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import {TreeNodeEvaluation, TreeNodeExt} from '../../Util/interfaces';
    import Rubric from '../../Domain/Rubric';
    import RubricEvaluation from '../../Domain/RubricEvaluation';
    import Level from '../../Domain/Level';
    import {LevelEntryChoice, ChoiceEntryChoice} from '../../Domain/EntryChoice';

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

        get hasSelection() {
            return (this.entryChoices as any[]).find(c => c?.isSelected);
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
                    if (!this.$refs['manual-score']) { return; }
                    const input = this.$refs['manual-score'] as HTMLInputElement[];
                    input[0]?.focus();
                    input[0]?.select();
                });
            }
        }

        onDeselect() {
            if (!this.preview) {
                this.$emit('deselect', this.evaluation);
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
    .treenode-choice {
        position: relative;
    }

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

    .manual-score-wrap {
        height: 44px;
        position: relative;

        &.mod-desc {
            height: 50px;
        }
    }

    .manual-score {
        align-items: center;
        background: #fff;
        border-radius: 3px;
        box-shadow: hsla(204, 38%, 34%, .2) 0 5px 15px;
        display: flex;
        gap: .75rem;
        height: 44px;
        justify-content: center;
        left: calc(50% - 100px);
        padding: 1rem;
        position: absolute;
        top: 3px;
        width: 200px;
        z-index: 1;

        > label {
            font-weight: 400;
            margin-bottom: 0;
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

    .btn-deselect {
        background:none;
        border: none;
        color: #dedede;
        height: 27px;
        width: 22px;

        &.is-active {
            color: #6195b8;

            &:hover, &:active {
                color: #447697;
            }
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

    @media only screen and (max-width: 679px) {
        .manual-score-wrap {
            height: 52px;
        }
        .btn-deselect {
            align-self: end;
        }
    }
</style>
<style>
    .gg-backspace {
        box-sizing: border-box;
        position: relative;
        display: block;
        width: 14px;
        height: 14px;
        transform: scale(var(--ggs,1));
        border: 2px solid;
        border-left: 0;
        border-top-right-radius: 2px;
        border-bottom-right-radius: 2px
    }
    .gg-backspace::after,
    .gg-backspace::before {
        content: "";
        display: block;
        box-sizing: border-box;
        position: absolute
    }
    .gg-backspace::before {
        background:
            linear-gradient( to left,
            currentColor 18px,transparent 0)
            no-repeat center center/10px 2px;
        border-right: 3px solid transparent;
        box-shadow: inset 0 0 0 2px;
        right: 2px;
        bottom: 1px;
        width: 8px;
        height: 8px;
        border-left: 3px solid transparent;
        transform: rotate(45deg)
    }
    .gg-backspace::after {
        width: 10px;
        height: 10px;
        border-top: 2px solid;
        border-left: 2px solid;
        border-top-left-radius: 1px;
        transform: rotate(-45deg);
        top: 0;
        left: -5px;
    }
</style>