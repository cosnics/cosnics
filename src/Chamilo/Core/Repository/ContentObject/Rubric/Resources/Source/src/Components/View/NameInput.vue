<template>
    <div class="name-input" @click.stop="">
		<span>
			<input type="text" @keyup="onChange" :placeholder="placeholder" ref="name-input" @keyup.enter="ok" @keyup.esc="cancel" v-bind:value="value" v-on:input="$emit('input', $event.target.value)">
            <i v-if="hasInput" class="fa fa-times-circle" @click="clearInput" />
			<i v-else class="fa fa-times-circle muted" />
		</span>
        <div>
            <button @click="ok" :disabled="!hasInput">{{ okTitle || 'OK' }}</button>
            <button @click="cancel">{{ cancelTitle || 'Annuleer' }}</button>
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

        hasInput: boolean = false;

        ok() {
            if (!this.hasInput) { return; }
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
    .name-input {
        display: flex;
        flex-direction: column;
    }
    .name-input div {
        margin-top: 5px;
    }
    span {
        position: relative;
    }
    span i {
        font-size: 16px;
        position: absolute;
        right: 4px;
        top: 8px;
        padding: 2px;
        color: darkgrey;
        opacity: 1;
        transition: opacity 200ms;
    }
    span i.muted {
        opacity: 0;
    }
    span i:hover {
        color: #666;
    }
    input {
        padding: 2px 20px 2px 4px;
        min-height: 36px;
        width: 100%;
    }
    input:focus {
        outline-width: 1px;
    }
    input::placeholder {
        color: #999;
    }
    button {
        margin: 0 5px 0 0;
        color: #444;
    }
    button:hover:nth-child(2) {
        border-color: transparent;
    }
    button[disabled] {
        background: #ccc!important;
    }
</style>
