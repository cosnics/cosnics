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
            <div class="treenode-choice" v-for="item in items">
                <div class="treenode-level" :class="{'mod-scores': useScores, 'mod-fixed-levels': !rubric.hasCustomLevels}">
                    <span class="treenode-level-title">{{ getTitle(item) }}</span>
                    <span v-if="useScores" class="treenode-level-score"><template v-if="hasMinimumScore(item)">{{ getMinimumScore(item)|formatNum }}<i class="fa fa-caret-right" aria-hidden="true"></i></template>{{ getScore(item)|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span v-else class="sr-only">{{ $t('points') }}</span></span>
                </div>
                <div class="treenode-level-description-input" @click="focusTextField" :class="{'mod-abs-weights': useScores && rubric.hasAbsoluteWeights}">
                    <description-field :field-item="getFieldItem(item)" @input="$emit('input', $event)" @change="updateDescription(item)">
                        <span v-if="useScores && !rubric.hasCustomLevels" class="level-score" :class="{'mod-fixed': hasChoices && item.choice.hasFixedScore}"><template v-if="hasMinimumScore(item)">{{ getMinimumScore(item)|formatNum }}<i class="fa fa-caret-right" aria-hidden="true"></i></template>{{ getScore(item)|formatNum }}<template v-if="rubric.useRelativeWeights"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template><span class="sr-only">{{ $t('points') }}</span></span>
                    </description-field>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import DescriptionField from '../Components/DescriptionField.vue';
    import Rubric from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';

    interface CriteriumExt {
        criterium: Criterium;
        choices: any[];
        levels: Level[];
    }

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
        @Prop({type: Object, required: true}) readonly ext!: CriteriumExt;

        hasMinimumScore(item: any) {
            if (this.hasChoices) {
                return item.level.useRangeScore;
            }
            return item.useRangeScore;
        }

        getMinimumScore(item: any) {
            if (this.hasChoices) {
                return item.level.minimumScore;
            }
            return item.minimumScore;
        }

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

        updateDescription(item: any) {
            if (this.hasLevels) {
                this.$emit('update-level-description', item);
            } else {
                this.$emit('update-choice-feedback', item.choice, this.criterium, item.level);
            }
        }

        get items() {
            if (this.ext.levels.length) {
                return this.ext.levels;
            }
            return this.ext.choices;
        }

        getTitle(item: any) {
            if (this.hasChoices) {
                return item.level.title;
            }
            return item.title;
        }

        getScore(item: any) {
            if (this.hasChoices) {
                return this.getChoiceScore(item);
            }
            return item.score;
        }

        getFieldItem(item: any) {
            if (this.hasChoices) {
                return item.choice;
            }
            return item;
        }

        getChoiceScore(choice: any): number {
            return this.rubric.useRelativeWeights ? choice.level.score : choice.score;
        }
    }
</script>

<style lang="scss" scoped>
    .treenode-level {
        background: #e6e6e6;
        border: 1px solid transparent;
        border-bottom-color: $score-light;
        border-radius: $border-radius;
        padding: .1rem .6rem;
        transition: 200ms background;
        width: 100%;
    }

    .fa-percent {
        font-size: 1rem;
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
        gap: 1rem;
        justify-content: space-between;

        .fa-caret-right {
            color: #adadad;
            font-size: 1.5rem;
        }
    }

    /*.treenode-level-title {
        display: none;
    }*/

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
