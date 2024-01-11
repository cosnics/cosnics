<i18n>
{
    "en": {
        "points": "points"
    },
    "fr": {
        "points": "points"
    },
    "nl": {
        "points": "punten"
    }
}
</i18n>

<template>
    <div class="treenode-rubric-input">
        <div class="treenode-choices">
            <div class="treenode-choice" v-for="{ item, level, title, score, useRangeScore, minimumScore } in entryChoices">
                <div class="treenode-level" :class="{'mod-scores': useScores, 'mod-fixed-levels': !rubric.hasCustomLevels}">
                    <span class="treenode-level-title">{{ title }}</span>
                    <span v-if="useScores" class="treenode-level-score"><template v-if="useRangeScore">{{ minimumScore|formatNum }}<i class="fa fa-caret-right" aria-hidden="true"></i></template>{{ score|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span v-else class="sr-only">{{ $t('points') }}</span></span>
                </div>
                <div class="treenode-level-description-input" @click="focusTextField" :class="{'mod-abs-weights': useScores && rubric.hasAbsoluteWeights}">
                    <description-field :descriptive-item="item" @input="$emit('input', $event)" @change="updateDescription(item, level)">
                        <span v-if="useScores && !rubric.hasCustomLevels" class="level-score" :class="{'mod-fixed': hasChoices && item.hasFixedScore}"><template v-if="useRangeScore">{{ minimumScore|formatNum }}<i class="fa fa-caret-right" aria-hidden="true"></i></template>{{ score|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span class="sr-only">{{ $t('points') }}</span></span>
                    </description-field>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import DescriptionField from './DescriptionField.vue';
    import {TreeNodeExt} from '../../../Util/interfaces';
    import Rubric from '../../../Domain/Rubric';
    import Criterium from '../../../Domain/Criterium';
    import {EntryChoice, ChoiceEntryChoice, LevelEntryChoice} from '../../../Domain/EntryChoice';
    import Level from '../../../Domain/Level';
    import Choice from '../../../Domain/Choice';

    @Component({
        components: {
            DescriptionField
        },
        filters: {
            formatNum: function (v: number) {
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeDescriptions extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;
        @Prop({type: Object, required: true}) readonly ext!: TreeNodeExt;

        get hasLevels(): boolean {
            return !!this.ext.levels.length;
        }

        get hasChoices(): boolean {
            return !!this.ext.choices.length;
        }

        get useScores(): boolean {
            return this.rubric.useScores;
        }

        focusTextField(elem: any) {
            if (elem.target.className === 'default-feedback') {
                elem.target.querySelector('.ta-default-feedback').focus();
            }
        }

        updateDescription(descriptiveItem: Level|Choice, level: Level) {
            if (descriptiveItem === level) {
                this.$emit('update-level-description', descriptiveItem);
            } else {
                this.$emit('update-choice-feedback', descriptiveItem, this.criterium, level);
            }
        }

        get entryChoices(): EntryChoice[] {
            if (this.hasLevels) {
                return this.ext.levels.map(level => new LevelEntryChoice(level, null));
            }
            return this.ext.choices.map(choiceObject => new ChoiceEntryChoice(choiceObject, null, !this.rubric.useRelativeWeights));
        }
    }
</script>

<style lang="scss" scoped>
    .treenode-level {
        background: #e6e6e6;
        border: 1px solid transparent;
        border-bottom-color: $score-light;
        border-radius: $border-radius;
        padding: .0625rem .375rem;
        transition: 200ms background;
        width: 100%;
    }

    .fa-percent {
        font-size: .625rem;
        margin-left: 0;
        opacity: .6;
    }

    /* todo: .treenode-level {
        height: 2.7rem;
        text-align: center;

    }*/

    .treenode-level.mod-scores {
        align-items: flex-start;
        display: flex;
        gap: .625rem;
        justify-content: space-between;

        .fa-caret-right {
            color: #adadad;
            font-size: .9375rem;
        }
    }

    /*.treenode-level-title {
        display: none;
    }*/

    .treenode-level-title {
        display: block;
        font-size: .8125rem;
        line-height: 1.125rem;
        padding: .15625rem 0;
    }

    .treenode-level-score {
        font-size: 1.125rem;
        white-space: nowrap;
    }

    @media only screen and (min-width: 680px) {
        .treenode-level.mod-fixed-levels {
            display: none;
        }
    }


/*    @media only screen and (max-width: 679px) {

    }*/


    /*.treenode-level.mod-levels {
        font-size: 1.4rem;
        padding-left: .7rem;
        text-align: left;
    }*/
</style>
