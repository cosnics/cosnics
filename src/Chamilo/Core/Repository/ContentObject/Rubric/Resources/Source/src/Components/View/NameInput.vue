<template>
    <div class="name-input" @click.stop="">
		<div class="name-input-title">
			<input type="text" class="name-input-field" @keyup="onChange" :placeholder="placeholder" ref="name-input" @keyup.enter="ok" @keyup.esc="cancel" :value="value" @input="$emit('input', $event.target.value)">
            <button v-if="hasInput" class="btn-clear fa fa-times-circle" @click="clearInput" />
			<button v-else class="btn-clear fa fa-times-circle muted" />
		</div>
        <div class="name-input-actions">
            <button class="btn-name-input btn-ok" @click="ok" :disabled="!(allowEmpty || hasInput)">{{ okTitle || 'OK' }}</button>
            <button class="btn-name-input btn-cancel" @click="cancel">{{ cancelTitle || 'Annuleer' }}</button>
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

<style scoped>
    .name-input-title {
        position: relative;
    }
    .btn-clear {
        position: absolute;
        background: none;
        border: none;
        font-size: 16px;
        height: 100%;
        top: 0;
        right: 0;
        color: darkgrey;
        opacity: 1;
        transition: opacity 200ms, color 200ms;
    }
    .btn-clear.muted {
        opacity: 0;
    }
    .btn-clear:hover {
        color: #666;
    }
    .name-input-field {
        width: 100%;
        padding-right: 18px;
    }
    .btn-ok {
        margin-right: 5px;
    }
</style>
