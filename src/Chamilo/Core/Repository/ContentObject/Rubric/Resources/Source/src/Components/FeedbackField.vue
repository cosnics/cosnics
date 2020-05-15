<template>
    <textarea v-model="choice.feedback" class="ta-feedback" @input="onFeedbackChange"></textarea>
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