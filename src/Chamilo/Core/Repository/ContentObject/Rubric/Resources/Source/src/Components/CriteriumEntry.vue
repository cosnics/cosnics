<i18n>
{
    "en": {
        "extra-feedback": "Enter extra feedback",
        "points": "points",
        "select-level": "Select a level",
        "show-default-description": "Show level descriptions and feedback",
        "total": "Total"
    },
    "fr": {
        "extra-feedback": "Feed-back supplémentaire",
        "points": "points",
        "select-level": "Selectionnez un niveau",
        "show-default-description": "Afficher descriptions de niveau et feed-back",
        "total": "Total"
    },
    "nl": {
        "extra-feedback": "Geef bijkomende feedback",
        "points": "punten",
        "select-level": "Selecteer een niveau",
        "show-default-description": "Toon niveauomschrijvingen en feedback",
        "total": "Totaal"
    }
}
</i18n>

<template>
    <component :is="tag" role="grid">
      <div class="criterium treenode-hover mod-responsive mod-entry-view" role="row" :class="feedbackVisibleClass">
            <div class="criterium-header treenode-header mod-responsive mod-entry-view" role="gridcell">
                <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title mod-entry-view category-indicator">{{ criterium.title }}</h4>
                <button v-if="!showDefaultFeedbackFields" class="btn-show-feedback mod-criterium" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" :aria-expanded="ext.showDefaultFeedback ? 'true' : 'false'" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                    <i tabindex="-1" class="btn-icon-show-feedback fa" :class="feedbackVisibleClass" aria-hidden="true" />
                </button>
            </div>
            <div class="criterium-levels-wrapper">
                <div v-if="showErrors && !preview && !hasSelection()" class="rubric-entry-error">{{ $t('select-level') }}</div>
                <ul class="criterium-levels">
                    <li v-for="choice in ext.choices" class="criterium-level mod-entry-view" :class="feedbackVisibleClass" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                        <div v-if="preview" :aria-checked="choice.level.isDefault" class="criterium-level-header mod-entry-view" :class="{ 'is-selected': isSelected(choice.level) }">
                            <div class="criterium-level-title mod-entry-view" :class="{ 'is-selected': isSelected(choice.level) }">
                                {{choice.title}}
                            </div>
                            <span v-if="useScores" class="score-number" :class="{ 'is-selected': isSelected(choice.level) }" :aria-label="`${ choice.score } ${ $t('points') }`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                            <span v-else class="graded-level" :class="{ 'is-selected': isSelected(choice.level) }"><i class="level-icon-check fa fa-check" :class="{ 'is-selected': isSelected(choice.level) }" /></span>
                        </div>
                        <button v-else role="radio" :aria-checked="isSelected(choice.level)" class="criterium-level-header mod-entry-view btn-score-number" :class="{ 'is-selected': isSelected(choice.level) }" @click="selectLevel(choice.level)">
                            <div class="criterium-level-title mod-entry-view" :class="{ 'is-selected': isSelected(choice.level) }">
                                {{choice.title}}
                            </div>
                            <span v-if="useScores" class="score-number" :class="{ 'is-selected': isSelected(choice.level) }" :aria-label="`${ choice.score } ${ $t('points') }`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                            <span v-else class="graded-level" :class="{ 'is-selected': isSelected(choice.level) }"><i class="level-icon-check fa fa-check" :class="{ 'is-selected': isSelected(choice.level) }" /></span>
                        </button>
                        <div v-if="choice.feedback" class="default-feedback-entry-view" :class="feedbackVisibleClass" v-html="marked(choice.feedback)">
                        </div>
                    </li>
                    <div v-if="useScores" class="subtotal criterium-total mod-entry-view" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                        <div class="score-number-calc mod-entry-view mod-criterium"><span class="text-hidden">{{ $t('total') }}:</span> {{ preview ? 0 : criteriumScore }} <span class="text-hidden">{{ $t('points') }}</span></div>
                    </div>
                </ul>
                <div v-if="evaluation" class="custom-feedback mod-criterium" :class="[feedbackVisibleClass, { 'mod-scores': useScores, 'mod-default-feedback': hasDefaultFeedback }]">
                    <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="$emit('feedback-changed', evaluation)"></textarea>
                </div>
            </div>
        </div>
    </component>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Level from '../Domain/Level';
    import Criterium from '../Domain/Criterium';
    import {TreeNodeEvaluation, TreeNodeExt} from '../Util/interfaces';
    import * as marked from 'marked';
    import DOMPurify from 'dompurify';

    @Component({
    })
    export default class CriteriumEntry extends Vue {
        @Prop({type: String, default: 'div'}) readonly tag!: String;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;
        @Prop({type: Boolean, default: true}) readonly showDefaultFeedbackFields!: boolean;
        @Prop({type: Object}) readonly ext!: TreeNodeExt;
        @Prop({type: Object}) readonly evaluation!: TreeNodeEvaluation|null;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;
        @Prop({type: Boolean, default: false}) readonly useScores!: boolean;

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
        }

        get hasDefaultFeedback() {
            return this.ext.choices.map(choice => choice.feedback.length).reduce((v1: number, v2: number) => v1 + v2, 0) > 0;
        }

        get feedbackVisibleClass() {
            return { 'is-feedback-visible': this.ext.showDefaultFeedback || this.showDefaultFeedbackFields };
        }

        get criteriumScore() : number {
            if (!this.evaluation) { return 0; }
            return this.evaluation.score || 0;
        }

        isSelected(level: Level) : boolean {
            if (this.preview || !this.evaluation) { return level.isDefault; }
            return this.evaluation.level === level;
        }

        hasSelection() : boolean {
            if (!this.evaluation) { return false; }
            return !!this.evaluation.level;
        }

        selectLevel(level: Level) : void {
            if (!this.evaluation) { return; }
            this.$emit('level-selected', this.evaluation, level);
        }
    }
</script>

<style lang="scss">
    .default-feedback-entry-view {
        white-space: initial!important;
    }
    .default-feedback-entry-view {
        ul {
            list-style: disc;
        }

        ul, ol {
            margin: 0 0 0 2rem;
            padding: 0;
        }
    }
</style>