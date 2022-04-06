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
    <div class="description-input-area">
        <textarea v-model="description" :placeholder="$t('enter-level-description')" class="ta-default-feedback" :class="{'is-input-active': isDescriptionInputActive || !description}" @input="onDescriptionChange" @focus="isDescriptionInputActive = true" @blur="isDescriptionInputActive = false"></textarea>
        <div class="description-markup-preview" :class="{'is-input-active': isDescriptionInputActive || !description}">
            <slot></slot>
            <div class="preview" v-html="preview"></div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Choice from '../Domain/Choice';
    import Level from '../Domain/Level';

    @Component({
        name: 'description-field',
        components: {
        },
    })
    export default class DescriptionField extends Vue {
        private isDescriptionInputActive = false;

        @Prop({type: [Choice, Level], required: true}) readonly fieldItem!: Choice|Level;

        set description(desc: string) {
            this.fieldItem.description = desc;
        }

        get description(): string {
            return this.fieldItem.description;
        }

        get preview() {
            return this.fieldItem.toMarkdown();
        }

        constructor() {
            super();
            this.onChange = debounce(this.onChange, 750);
        }

        onChange() {
            this.$emit('change', this.fieldItem);
        }

        onDescriptionChange(e: InputEvent) {
            this.$emit('input', e);
            this.onChange();
        }
    }
</script>

<style lang="scss">
    .description-input-area {
        position: relative;
    }

    .ta-default-feedback {
        opacity: 0;

        &.is-input-active {
            opacity: 1;
        }

        &:focus + .description-markup-preview.is-input-active .level-score {
            opacity: 0;
        }
    }

    .description-markup-preview {
        border: none;
        bottom: .5em;
        left: 0;
        /*overflow: hidden;*/
        padding: .3em;
        pointer-events: none;
        position: absolute;
        right: 0;
        top: 0;

        &.is-input-active .preview {
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
        .description-markup-preview .level-score {
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
            margin: -3px -3px .5em .5em;
            padding-left: 0.5em;
            padding-right: 0.5em;
            text-align: center;

            &.mod-fixed {
                background-color: #caeab8;
                border-color: #d4d4d4;
                color: hsl(0, 0%, 20%);
            }

            .fa-caret-right {
                color: hsla(190, 18%, 59%, .85);
                font-size: 1.5rem;
            }
        }
    }
</style>