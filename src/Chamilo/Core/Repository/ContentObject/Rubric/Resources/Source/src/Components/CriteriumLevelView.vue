<i18n>
{
    "en": {
        "enter-feedback": "Enter feedback",
        "fixed-score": "This level has a fixed score for this level. Click to remove."
    },
    "fr": {
        "enter-feedback": "Commentaire / Feed-back",
        "fixed-score": "Ce critère a une note fixe pour ce niveau. Cliquez pour l'annuler."
    },
    "nl": {
        "enter-feedback": "Geef feedback",
        "fixed-score": "Dit criterium heeft een overschreven vaste score voor dit niveau. Klik om dit ongedaan te maken."
    }
}
</i18n>

<template>
    <div>
        <label :for="`level-${level.id}`" class="b-criterium-level-title">{{ level.title }} <span v-if="level.description" class="fa fa-question-circle criterium-level-description" :title="level.description"></span></label>
        <div class="criterium-level-input">
        <textarea :id="`level-${level.id}`" v-model="choice.feedback" ref="feedbackField" class="criterium-level-feedback input-detail"
                  :placeholder="$t('enter-feedback')"
                  @input="onFeedbackChange"></textarea>
            <div v-if="rubric.useScores" class="criterium-level-score">
                <button v-if="choice.hasFixedScore" class="remove-fixed" @click="removeFixedScore" :title="$t('fixed-score')"><i class="fa fa-lock" /><i class="fa fa-unlock" /></button>
                <input class="fixed-score input-detail" type="number" step="0.1" v-if="choice.hasFixedScore" v-model="choice.fixedScore" @input="onChange" />
                <input type="number" class="input-detail" step="0.1" v-else v-model="rubric.getChoiceScore(criterium, level)" @input="changeChoiceScore" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import Criterium from '../Domain/Criterium';
    import Choice from '../Domain/Choice';

    @Component({
        name: 'criterium-level-view'
    })
    export default class CriteriumLevelView extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Level, required: true}) readonly level!: Level;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;

        constructor() {
            super();
            this.onChange = debounce(this.onChange, 750);
        }

        get choice() : Choice {
            return this.rubric.getChoice(this.criterium, this.level);
        }

        removeFixedScore() {
            this.choice.hasFixedScore = false;
            this.choice.fixedScore = Choice.FIXED_SCORE;
            this.onChange();
            this.$forceUpdate();
        }

        changeChoiceScore(event: any) {
            const value = parseFloat(event.target.value);
            if (!isNaN(value)) {
                this.choice.hasFixedScore = true;
                this.choice.fixedScore = value;
                this.onChange();
                this.$forceUpdate();
            }
        }

        onChange() {
            this.$emit('change', this.choice);
        }

        onFeedbackChange(e: InputEvent) {
            this.$emit("input", e);
            this.onChange();
        }
    }
</script>