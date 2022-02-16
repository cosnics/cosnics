<i18n>
{
    "en": {
        "no-description": "No description"
    },
    "fr": {
        "no-description": "Pas de description"
    },
    "nl": {
        "no-description": "Geen omschrijving"
    }
}
</i18n>

<template>
    <div class="treenode-choices">
        <div class="treenode-choice" :class="{'mod-show-description': showDescription, 'mod-empty-description': showDescription && !description}" v-for="{ title, description, score, markdown, level, isSelected } in entryChoices">
            <component :is="preview ? 'div' : 'button'" class="treenode-level" :class="{'mod-fixed-levels': !rubric.hasCustomLevels, 'is-selected': isSelected, 'mod-btn': !preview }" @click="onSelect(level)">
                <span class="treenode-level-title" :class="{'mod-fixed-levels': !rubric.hasCustomLevels}">{{ title }}</span>
                <span v-if="useScores" class="treenode-level-score">{{ score|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span v-else class="sr-only">{{ $t('points') }}</span></span>
                <span v-else><i class="treenode-level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }"></i></span>
            </component>
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
    import Level from '../Domain/Level';
    import {LevelEntryChoice, ChoiceEntryChoice} from '../Domain/EntryChoice';

    @Component({
        filters: {
            formatNum: function (v: number) {
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

        onSelect(level: Level) {
            if (!this.preview) {
                this.$emit('select', this.evaluation, level);
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