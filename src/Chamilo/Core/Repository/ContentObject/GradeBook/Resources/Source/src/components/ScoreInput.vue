<template>
    <highlight-input @edit="onEdit" @cancel="$emit('cancel')">
        <div style="display: flex; gap: 5px">
            <div style="display: flex">
                <input id="score" ref="score-input" type="number" :value="score|formatNum" autocomplete="off" @input="type = 'number'" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')">
                <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
            <button class="btn-afw" :class="{'is-selected': type === 'afw'}" style="padding: 2px 4px;line-height:1" @click="type = 'afw'"><span style="font-variant: all-small-caps">AFW</span></button>
            <button class="btn-gafw" :class="{'is-selected': type === 'gafw'}" style="padding: 2px 4px;line-height:1" @click="type = 'gafw'"><span style="font-variant: all-small-caps">GAFW</span></button>
        </div>
    </highlight-input>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import HighlightInput from './HighlightInput.vue';
import {ResultType} from '../domain/GradeBook';

@Component({
    name: 'score-input',
    components: { HighlightInput },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        }
    }
})
export default class ScoreInput extends Vue {
    private type: 'number'|'afw'|'gafw' = 'number';

    @Prop({type: [Number, String], default: null}) readonly score!: ResultType;

    get scoreInput() {
        return this.$refs['score-input'] as HTMLInputElement;
    }

    onEdit() {
        if (this.type === 'number') {
            const value = parseFloat(this.scoreInput.value);
            this.$emit('ok', isNaN(value) ? null : value);
        } else if (this.type === 'afw') {
            this.$emit('ok', 'afw');
        } else if (this.type === 'gafw') {
            this.$emit('ok', 'gafw');
        }
    }

    mounted() {
        this.$nextTick(() => this.scoreInput.focus());
        if (this.score === 'afw') { this.type = 'afw'; return; }
        if (this.score === 'gafw') { this.type = 'gafw'; return; }
        this.type = 'number';
    }
}
</script>

<style lang="scss" scoped>
input {
    border: 1px solid #ced4da;min-height: 24px;color: #333;padding: 2px 18px 2px 4px;font-weight: 400;width: 100%;
    border-bottom-left-radius: .2rem;
    border-top-left-radius: .2rem;
    border-right-width: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}

input:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%), 0 0 8px rgb(102 175 233 / 60%);
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
    border: 1px solid #ced4da;
    border-radius: 0;
    border-top-right-radius: 0.2rem;
    border-bottom-right-radius: 0.2rem;
    z-index: 1;
}
label {
    font-weight: 500;
    margin-bottom: 0;
    font-size: 1.25rem;
    margin-left: .15rem;
}

/*.btn-afw {
    background: #ff5722;
    color: white;
    opacity: .42;
}

.btn-afw, .btn-afw:focus {
    outline: 0;
}*/

.btn-afw, .btn-gafw {
    border: 1px solid transparent;
    border-radius: .2rem;
}
</style>