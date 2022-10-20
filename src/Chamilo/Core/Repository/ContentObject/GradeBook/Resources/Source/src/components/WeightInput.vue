<i18n>
{
    "en": {
        "weight": "Weight"
    },
    "nl": {
        "weight": "Gewicht"
    }
}
</i18n>
<template>
    <highlight-input @edit="onEdit" @cancel="$emit('cancel')">
        <template v-slot:content>
            <label for="weight" class="u-font-medium">{{ $t('weight') }}:</label>
            <div class="u-relative">
                <input id="weight" class="percent-input u-font-normal" ref="weight-input" type="number" :value="itemWeight|formatNum" autocomplete="off" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')">
                <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
        </template>
    </highlight-input>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import HighlightInput from './HighlightInput.vue';

@Component({
    name: 'weight-input',
    components: { HighlightInput },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        }
    }
})
export default class WeightInput extends Vue {
    @Prop({type: Number, default: ''}) readonly itemWeight!: number|null;

    get weightInput() {
        return this.$refs['weight-input'] as HTMLInputElement;
    }

    onEdit() {
        const value = parseFloat(this.weightInput.value);
        this.$emit('ok', isNaN(value) ? null : value);
    }

    mounted() {
        this.$nextTick(() => this.weightInput.focus());
    }
}
</script>

<style lang="scss" scoped>
label {
    font-size: 1.25rem;
    margin-bottom: 0;
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