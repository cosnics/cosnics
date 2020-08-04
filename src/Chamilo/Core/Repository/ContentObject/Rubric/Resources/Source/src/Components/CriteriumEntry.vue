<template>
    <component :is="tag" role="grid" :class="{'is-feedback-visible': ext.showDefaultFeedback }">
        <div class="criterium" role="row">
            <div class="criterium-title-header" role="gridcell">
                <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title category-indicator">{{ criterium.title }}</h4><button v-if="!showDefaultFeedbackFields" class="btn-more" aria-label="Toon standaard feedback beschrijving criterium" :aria-expanded="ext.showDefaultFeedback ? 'true' : 'false'" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback"><i tabindex="-1" class="check fa" aria-hidden="true" /></button>
            </div>
            <div v-for="choice in ext.choices" class="criterium-level" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                <div v-if="preview" :aria-checked="choice.level.isDefault" class="criterium-level-header" :class="{ selected: choice.level.isDefault }">
                    <div class="criterium-level-title">
                        {{choice.title}}
                    </div>
                    <span class="score-number" :aria-label="`${ choice.score } punten`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                </div>
                <button v-else role="radio" :aria-checked="isSelected(choice.level)" class="criterium-level-header btn-score-number" :class="{ selected: isSelected(choice.level) }" @click="selectLevel(choice.level)">
                    <div class="criterium-level-title">
                        {{choice.title}}
                    </div>
                    <span class="score-number" :aria-label="`${ choice.score } punten`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                </button>
                <div class="default-feedback">
                    {{ choice.feedback }}
                </div>
            </div>
            <div class="subtotal criterium-total" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                <div class="score-number"><span class="text-hidden">Totaal:</span> {{ preview ? 0 : criteriumScore }} <span class="text-hidden">punten</span></div>
            </div>
        </div>
        <div class="custom-feedback">
            <textarea v-if="evaluation" placeholder="Geef Feedback" v-model="evaluation.feedback" @input="$emit('feedback-changed', evaluation)"></textarea>
        </div>
    </component>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Level from '../Domain/Level';
    import Criterium from '../Domain/Criterium';
    import {CriteriumEvaluation, CriteriumExt} from '../Domain/util';

    @Component({})
    export default class CriteriumEntry extends Vue {
        @Prop({type: String, default: 'div'}) readonly tag!: String;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;
        @Prop({type: Boolean}) readonly showDefaultFeedbackFields!: boolean;
        @Prop({type: Object}) readonly ext!: CriteriumExt;
        @Prop({type: Object}) readonly evaluation!: CriteriumEvaluation|null;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;

        get criteriumScore() : number {
            if (!this.evaluation) { return 0; }
            return this.evaluation.score || 0;
        }

        isSelected(level: Level) : boolean {
            if (!this.evaluation) { return level.isDefault; }
            return this.evaluation.level === level;
        }

        selectLevel(level: Level) : void {
            if (!this.evaluation) { return; }
            this.$emit('level-selected', this.evaluation, level);
        }
    }
</script>