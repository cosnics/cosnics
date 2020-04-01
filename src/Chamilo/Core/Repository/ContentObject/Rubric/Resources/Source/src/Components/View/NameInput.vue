<template>
    <div class="name-input">
		<span>
			<input type="text" @keyup="onChange" :placeholder="placeholder" ref="name-new" @keyup.enter="addNew" @keyup.esc="cancelNew" v-bind:value="value" v-on:input="$emit('input', $event.target.value)">
            <i v-if="hasInput" class="fa fa-times-circle" @click="clearInput" />
			<i v-else class="fa fa-times-circle muted" />
		</span>
        <div>
            <button @click="addNew" :disabled="!hasInput">Voeg Toe</button>
            <button @click="cancelNew">Annuleer</button>
        </div>
    </div>
</template>

<script lang="ts">
    import { Component, Prop, Vue } from "vue-property-decorator";

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

        hasInput: boolean = false;

        addNew() {
            if (!this.hasInput) { return; }
            this.$emit('ok');
        }

        cancelNew() {
            this.$emit('cancel');
        }

        clearInput() {
            this.$emit('input', '');
            if (this.$refs['name-new']) {
                (this.$refs['name-new'] as HTMLElement).focus();
            }
        }

        onChange() {
            if (this.$refs['name-new']) {
                this.$data.hasInput = (this.$refs['name-new'] as any).value !== '';
            }
        }
        mounted() {
            if (this.$refs['name-new']) {
                //@ts-ignore
                this.$refs['name-new'].focus();
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
