<i18n>
{
    "en": {
        "cancel": "Cancel",
        "ok": "OK"
    },
    "fr": {
        "cancel": "Annuler",
        "ok": "D'accord"
    },
    "nl": {
        "cancel": "Annuleer",
        "ok": "OK"
    }
}
</i18n>

<template>
    <div class="name-input" @click.stop="">
		<div class="name-input-title">
            <!--<input type="text" class="name-input-field mod-textfield" @keyup="onChange" :placeholder="placeholder" ref="name-input" @keyup.enter="ok" @keyup.esc="cancel" :value="value" @input="$emit('input', $event.target.value)">-->
            <textarea class="name-input-field mod-textarea" @keyup="onChange" :placeholder="placeholder" ref="name-input" @keydown.enter.prevent="ok" @keyup.esc="cancel" :value="value" @input="onInput"></textarea>
		</div>
        <div class="name-input-actions">
            <button class="btn-strong mod-confirm" @click="ok" :disabled="!(allowEmpty || hasInput)">{{ okTitle || $t('ok') }}</button>
            <button class="btn-strong" @click="cancel">{{ cancelTitle || $t('cancel') }}</button>
        </div>
    </div>
</template>

<script lang="ts">
    import { Component, Prop, Vue } from 'vue-property-decorator';

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight}px`;
    }

    @Component({
        name: 'name-input',
        components: {}
    })
    export default class NameInput extends Vue {
        @Prop()
        placeholder!: string;

        @Prop()
        classname!: string;

        @Prop()
        value!: string;

        @Prop()
        okTitle!: string;

        @Prop()
        cancelTitle!: string;

        @Prop({ default: false })
        allowEmpty!: boolean;

        hasInput: boolean = false;

        ok() {
            if (!(this.hasInput || this.allowEmpty)) { return; }
            this.$emit('ok');
        }

        cancel() {
            this.$emit('cancel');
        }

        clearInput() {
            this.$emit('input', '');
            this.hasInput = false;
            if (this.$refs['name-input']) {
                (this.$refs['name-input'] as HTMLElement).focus();
            }
        }

        onInput(event: any) {
            this.$emit('input', event.target.value);
            updateHeight(event.target);
        }

        onChange() {
            if (this.$refs['name-input']) {
                this.hasInput = (this.$refs['name-input'] as any).value !== '';
            }
        }

        mounted() {
            if (this.value) {
                this.hasInput = true;
            }
            if (this.$refs['name-input']) {
                (this.$refs['name-input'] as HTMLElement).focus();
                /*if (this.value !== '') {
                    updateHeight(this.$refs['name-input'] as HTMLElement);
                }*/
            }
        }
    }
</script>
