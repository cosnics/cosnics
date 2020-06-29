<template>
    <div class="name-input" @click.stop="">
		<div class="name-input-title">
			<input type="text" class="name-input-field mod-textfield" @keyup="onChange" :placeholder="placeholder" ref="name-input" @keyup.enter="ok" @keyup.esc="cancel" :value="value" @input="$emit('input', $event.target.value)">
            <button class="btn-clear-text mod-textfield" :class="{ 'is-empty-field': !hasInput }" :disabled="!hasInput" @click="clearInput"><span class="sr-only">Maak tekstveld leeg</span></button>
		</div>
        <div class="name-input-actions">
            <button class="btn-strong mod-confirm" @click="ok" :disabled="!(allowEmpty || hasInput)">{{ okTitle || 'OK' }}</button>
            <button class="btn-strong" @click="cancel">{{ cancelTitle || 'Annuleer' }}</button>
        </div>
    </div>
</template>

<script lang="ts">
    import { Component, Prop, Vue } from 'vue-property-decorator';

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
                //@ts-ignore
                this.$refs['name-input'].focus();
            }
        }
    }
</script>
