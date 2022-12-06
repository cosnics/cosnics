<template>
    <table-cell-input @edit="onEdit" @cancel="$emit('cancel')">
        <template v-slot:content>
            <input class="u-font-normal" ref="title-input" type="text" :value="itemTitle" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')">
        </template>
    </table-cell-input>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import TableCellInput from './TableCellInput.vue';

@Component({
    name: 'item-title-input',
    components: { TableCellInput },
})
export default class ItemTitleInput extends Vue {
    @Prop({type: String, default: ''}) readonly itemTitle!: string;

    get titleInput() {
        return this.$refs['title-input'] as HTMLInputElement;
    }

    onEdit() {
        this.$emit('ok', this.titleInput.value);
    }

    mounted() {
        this.$nextTick(() => this.titleInput.focus());
    }
}
</script>

<style scoped>
input {
    border: 1px solid silver;
    color: #333;
    min-height: 24px;
    padding: 2px 18px 2px 4px;
    width: 100%;
}
input:focus {
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%), 0 0 8px rgb(102 175 233 / 60%);
    outline: 0;
}
</style>