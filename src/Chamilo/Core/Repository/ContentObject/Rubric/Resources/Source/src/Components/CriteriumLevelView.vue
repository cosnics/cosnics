<i18n>
{
    "en": {
        "enter-level-description": "Enter level description",
        "fixed-score": "This level has a fixed score for this level. Click to remove."
    },
    "fr": {
        "enter-level-description": "Entrer une description de niveau",
        "fixed-score": "Ce critère a une note fixe pour ce niveau. Cliquez pour l'annuler."
    },
    "nl": {
        "enter-level-description": "Voer een niveauomschrijving in",
        "fixed-score": "Dit criterium heeft een overschreven vaste score voor dit niveau. Klik om dit ongedaan te maken."
    }
}
</i18n>

<template>
    <div>
        <label :for="`level-${level.id}`" class="b-criterium-level-title">{{ level.title }} <span v-if="level.description" class="fa fa-question-circle criterium-level-description" :title="level.description"></span></label>
        <div class="criterium-level-input" >
            <div class="criterium-level-input-area" :class="{ 'is-using-scores': rubric.useScores}">
                <textarea :id="`level-${level.id}`" v-model="choice.feedback" ref="feedbackField" class="criterium-level-feedback input-detail"
                          :class="{ 'is-input-active': isFeedbackInputActive || !choice.feedback }"
                          :placeholder="$t('enter-level-description')"
                          @input="onFeedbackChange" @focus="isFeedbackInputActive = true" @blur="isFeedbackInputActive = false">
                </textarea>
                <div class="criterium-level-markup-preview" :class="{'is-input-active': isFeedbackInputActive || !choice.feedback}" v-html="marked(choice.feedback)"></div>
            </div>
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
    import DOMPurify from 'dompurify';
    import * as marked from 'marked';

    @Component({
        name: 'criterium-level-view'
    })
    export default class CriteriumLevelView extends Vue {
        private isFeedbackInputActive = false;

        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop({type: Level, required: true}) readonly level!: Level;
        @Prop({type: Criterium, required: true}) readonly criterium!: Criterium;

        constructor() {
            super();
            this.onChange = debounce(this.onChange, 750);
        }

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
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
<style lang="scss">
    .criterium-level-input-area {
        flex: 1;
        position: relative;

        &.is-using-scores {
            margin-right: 1.5em;
        }
    }

    .criterium-level-feedback {
        opacity: 0;
        &.is-input-active {
            opacity: 1;
        }
    }

    .criterium-level-markup-preview {
        background: hsla(190, 50%, 98%, 1);
        border: 1px solid #d4d4d4;
        border-radius: $border-radius;
        bottom: .5em;
        left: -5px;
        right: 5px;
        padding: 2px 5px 0;
        pointer-events: none;
        position: absolute;
        top: 0;

        &.is-input-active {
            opacity: 0;
        }

        ul {
            list-style: disc;
        }

        ul, ol {
            margin: 0 0 0 2rem;
            padding: 0;
        }
    }

    .criterium-level-input-area:hover .criterium-level-markup-preview {
        border-color: #aaa;
    }
</style>