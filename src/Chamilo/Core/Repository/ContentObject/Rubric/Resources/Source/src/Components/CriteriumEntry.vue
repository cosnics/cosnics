<template>
    <component :is="tag" role="grid">
      <div v-if="showErrors && !preview && !hasSelection()" class="rubric-entry-error">Selecteer een score</div>
      <div class="criterium mod-responsive mod-entry-view" role="row" :class="feedbackVisibleClass">
            <div class="criterium-title-header mod-responsive mod-entry-view" role="gridcell">
                <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title mod-entry-view category-indicator">{{ criterium.title }}</h4>
                <button v-if="!showDefaultFeedbackFields" class="btn-show-feedback" aria-label="Toon standaard feedback beschrijving criterium" :aria-expanded="ext.showDefaultFeedback ? 'true' : 'false'" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                    <i tabindex="-1" class="btn-icon-show-feedback fa" :class="feedbackVisibleClass" aria-hidden="true" />
                </button>
            </div>
            <div v-for="choice in ext.choices" class="criterium-level mod-entry-view" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                <div v-if="preview" :aria-checked="choice.level.isDefault" class="criterium-level-header mod-entry-view" :class="{ 'is-selected': isSelected(choice.level) }">
                    <div class="criterium-level-title" :class="{ 'is-selected': isSelected(choice.level) }">
                        {{choice.title}}
                    </div>
                    <span class="score-number" :class="{ 'is-selected': isSelected(choice.level) }" :aria-label="`${ choice.score } punten`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                </div>
                <button v-else role="radio" :aria-checked="isSelected(choice.level)" class="criterium-level-header mod-entry-view btn-score-number" :class="{ 'is-selected': isSelected(choice.level) }" @click="selectLevel(choice.level)">
                    <div class="criterium-level-title" :class="{ 'is-selected': isSelected(choice.level) }">
                        {{choice.title}}
                    </div>
                    <span class="score-number" :class="{ 'is-selected': isSelected(choice.level) }" :aria-label="`${ choice.score } punten`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                </button>
                <div class="default-feedback-entry-view" :class="feedbackVisibleClass">
                    {{ choice.feedback }}
                </div>
            </div>
            <div class="subtotal criterium-total mod-entry-view" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                <div class="score-number-calc mod-entry-view mod-criterium"><span class="text-hidden">Totaal:</span> {{ preview ? 0 : criteriumScore }} <span class="text-hidden">punten</span></div>
            </div>
        </div>
        <div class="custom-feedback" :class="feedbackVisibleClass">
            <textarea v-if="evaluation" class="ta-custom-feedback" placeholder="Geef Feedback" v-model="evaluation.feedback" @input="$emit('feedback-changed', evaluation)"></textarea>
        </div>
    </component>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Level from '../Domain/Level';
    import Criterium from '../Domain/Criterium';
    import {CriteriumEvaluation, CriteriumExt} from '../Util/interfaces';

    @Component({})
    export default class CriteriumEntry extends Vue {
        @Prop({type: String, default: 'div'}) readonly tag!: String;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;
        @Prop({type: Boolean, default: true}) readonly showDefaultFeedbackFields!: boolean;
        @Prop({type: Object}) readonly ext!: CriteriumExt;
        @Prop({type: Object}) readonly evaluation!: CriteriumEvaluation|null;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

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