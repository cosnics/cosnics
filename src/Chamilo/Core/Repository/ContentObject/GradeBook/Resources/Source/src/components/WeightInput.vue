<template>
    <highlight-input @edit="onEdit" @cancel="$emit('cancel')">
        <label for="weight">Gewicht:</label>
        <div style="position: relative">
            <input id="weight" ref="weight-input" type="number" :value="itemWeight|formatNum" autocomplete="off" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')">
            <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
        </div>
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
input {
    border: 1px solid #ced4da;min-height: 24px;color: #333;padding: 2px 18px 2px 4px;font-weight: 400;width: 100%;
    border-radius: .2rem;
}

input[type="number"] {
    -moz-appearance: textfield;
    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
         -webkit-appearance: none;
         margin: 0;
    }
}

.percent {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #5b5f64;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border-left: 1px solid #ced4da;
    z-index: 1;

    position: absolute;
    top: 1px;
    bottom: 1px;
    right: 1px;

}

input:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%), 0 0 8px rgb(102 175 233 / 60%);
}

label {
    font-weight: 500;
    margin-bottom: 0;
    font-size: 1.25rem;
    margin-left: .15rem;
}
</style>