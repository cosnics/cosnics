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
            <div class="criterium-level-input-area" :class="{ 'is-using-orig-scores': rubric.useScores && !rubric.useRelativeWeights }">
                <textarea :id="`level-${level.id}`" v-model="choice.feedback" ref="feedbackField" class="criterium-level-feedback input-detail"
                          :class="{ 'is-input-active': isFeedbackInputActive || !choice.feedback }"
                          :placeholder="$t('enter-level-description')"
                          @input="onFeedbackChange" @focus="isFeedbackInputActive = true" @blur="isFeedbackInputActive = false">
                </textarea>
                <div class="criterium-level-markup-preview" :class="{'is-input-active': isFeedbackInputActive || !choice.feedback}" v-html="marked(choice.feedback)"></div>
            </div>
            <template v-if="rubric.useScores">
                <div v-if="rubric.useRelativeWeights" style="font-size: 2.1rem; margin-top: .05em; text-align: right; width: 1.5em;">{{ level.score }}</div>
                <div v-else class="criterium-level-score">
                    <button v-if="choice.hasFixedScore" class="remove-fixed" @click="removeFixedScore" :title="$t('fixed-score')"><i class="fa fa-lock" /><i class="fa fa-unlock" /></button>
                    <input v-if="choice.hasFixedScore" type="number" required min="0" max="100" class="fixed-score input-detail" v-model.number="choice.fixedScore" @input="onChange" />
                    <input v-else type="number" required min="0" max="100" class="input-detail" v-model="rubric.getChoiceScore(criterium, level)" @input="changeChoiceScore" />
                </div>
            </template>
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
            this.emitChange();
            this.$forceUpdate();
        }

        changeChoiceScore(event: any) {
            const el = event.target as HTMLInputElement;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            const value = parseFloat(event.target.value);
            if (!isNaN(value)) {
                this.choice.hasFixedScore = true;
                this.choice.fixedScore = value;
                this.emitChange();
                this.$forceUpdate();
            }
        }

        emitChange() {
            this.$emit('change', this.choice);
        }

        onChange(event: InputEvent) {
            const el = event.target as HTMLInputElement;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            this.emitChange();
        }

        onFeedbackChange(e: InputEvent) {
            this.$emit('input', e);
            this.emitChange();
        }
    }
</script>
<style lang="scss">
    .criterium-level-input-area {
        flex: 1;
        position: relative;

        &.is-using-orig-scores {
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