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
        <div class="feedback-markup-preview" :class="{'is-input-active': isFeedbackInputActive || !choice.feedback}">
            <slot></slot>
            <div class="choice-preview" v-html="choice.toMarkdown()"><template>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Choice from '../Domain/Choice';

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

        &:focus + .feedback-markup-preview.is-input-active .level-score {
            opacity: 0;
        }
    }

    .feedback-markup-preview {
        border: none;
        bottom: .5em;
        left: 0;
        padding: .3em;
        pointer-events: none;
        position: absolute;
        right: 0;
        top: 0;

        &.is-input-active .choice-preview {
            opacity: 0;
        }

        ul {
            list-style: disc;
            margin: 0 0 0 2rem;
            padding: 0;
        }

        .level-score {
            display: none;
        }
    }

    @media only screen and (min-width: 680px) {
        .feedback-markup-preview .level-score {
            align-items: center;
            float: right;
            background-color: #f7fcfc;
            border-bottom: 1px solid hsl(0, 0%, 90%);
            border-bottom-left-radius: 3px;
            border-left: 1px solid hsl(0 ,0%, 90%);
            color: hsl(190, 30%, 40%);
            display: flex;
            font-size: 1.5rem;
            height: 1.75em;
            justify-content: center;
            margin: -4px -4px .5em .5em;
            padding-left: 0.5em;
            padding-right: 0.5em;
            text-align: center;

            &.mod-fixed {
                background-color: #caeab8;
                border-color: #d4d4d4;
                color: hsl(0, 0%, 20%);
            }
        }
    }
</style>