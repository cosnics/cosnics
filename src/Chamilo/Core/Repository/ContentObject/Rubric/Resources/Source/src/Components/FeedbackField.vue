<i18n>
{
    "en": {
        "enter-level-description": "Enter level description"
    },
    "fr": {
        "enter-level-description": "Entrer une description de niveau"
    },
    "nl": {
        "enter-level-description": "Voer een niveauomschrijving in"
    }
}
</i18n>

<template>
    <div class="feedback-input-area">
        <textarea v-model="choice.feedback" :placeholder="$t('enter-level-description')" class="ta-default-feedback" :class="{'is-input-active': isFeedbackInputActive || !choice.feedback}" @input="onFeedbackChange" @focus="isFeedbackInputActive = true" @blur="isFeedbackInputActive = false">></textarea>
        <div class="feedback-markup-preview" :class="{'is-input-active': isFeedbackInputActive || !choice.feedback}" v-html="marked(choice.feedback)"></div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Choice from '../Domain/Choice';
    import DOMPurify from 'dompurify';
    import * as marked from 'marked';

    @Component({
        name: 'feedback-field',
        components: {
        },
    })
    export default class FeedbackField extends Vue {
        private isFeedbackInputActive = false;

        @Prop({type: Choice, required: true}) readonly choice!: Choice;

        constructor() {
            super();
            this.onChange = debounce(this.onChange, 750);
        }

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
        }

        onChange() {
            this.$emit('change', this.choice);
        }

        onFeedbackChange(e: InputEvent) {
            this.$emit('input', e);
            this.onChange();
        }
    }
</script>

<style lang="scss">
    .feedback-input-area {
        position: relative;
    }

    .ta-default-feedback {
        opacity: 0;

        &.is-input-active {
            opacity: 1;
        }
    }

    .feedback-markup-preview {
        border: none;
        bottom: .5em;
        padding: .3em;
        pointer-events: none;
        position: absolute;
        top: 0;

        &.is-input-active {
            opacity: 0;
        }

        ul {
            list-style: disc;
            margin: 0 0 0 2rem;
            padding: 0;
        }
    }
</style>