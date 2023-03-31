<i18n>
{
    "en": {
        "display-total": "Max score"
    },
    "nl": {
        "display-total": "Max score"
    }
}
</i18n>

<template>
    <table-cell-input @edit="onEdit" @cancel="$emit('cancel')">
        <template v-slot:content>
            <label for="display-total" class="u-font-medium">{{ $t('display-total') }}:</label>
            <div class="u-relative">
                <input id="display-total" class="percent-input u-font-normal" ref="display-total-input" type="number" min="0" max="100" :value="displayTotal|formatNum" autocomplete="off" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')">
            </div>
        </template>
    </table-cell-input>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import TableCellInput from './TableCellInput.vue';

@Component({
    name: 'display-total-input',
    components: { TableCellInput },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        }
    }
})
export default class WeightInput extends Vue {
    @Prop({type: Number, default: ''}) readonly displayTotal!: number|null;

    get displayTotalInput() {
        return this.$refs['display-total-input'] as HTMLInputElement;
    }

    onEdit() {
        const el = this.displayTotalInput as HTMLInputElement;
        if (!el.checkValidity()) {
            el.reportValidity();
            return;
        }
        const value = parseFloat(this.displayTotalInput.value);
        this.$emit('ok', isNaN(value) ? null : value);
    }

    mounted() {
        this.$nextTick(() => this.displayTotalInput.focus());
    }
}
</script>

<style lang="scss" scoped>
label {
    font-size: 1.25rem;
    margin-bottom: .15rem;
    margin-left: .15rem;
}

.percent-input {
    border: 1px solid #ced4da;
    border-radius: .2rem;
    color: #333;
    min-height: 24px;
    padding: 2px 18px 2px 4px;
    width: 100%;

    -moz-appearance: textfield;
    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    &:focus {
        outline: 0;
        border: 1px solid #6ac;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6);
    }
}

.percent {
    align-items: center;
    background-color: #e9ecef;
    border-left: 1px solid #ced4da;
    color: #5b5f64;
    display: flex;
    font-size: 1rem;
    font-weight: 400;
    inset: 1px 1px 1px auto;
    line-height: 1.5;
    padding: 0.375rem 0.75rem;
    position: absolute;
    text-align: center;
    white-space: nowrap;
    z-index: 1;
}
</style>