<template>
    <highlight-input @edit="onEdit" @cancel="$emit('cancel')">
        <div style="display: flex; gap: 5px">
            <div style="display: flex" class="number-input" :class="{'is-selected': type === 'number'}">
                <input id="score" ref="score-input" type="number" :value="numValue|formatNum" autocomplete="off" @input="type = 'number'" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')" @focus="type = 'number'">
                <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
            <button class="color-code deep-orange-500" :class="{'is-selected': type === 'afw'}" style="padding: 2px 4px;line-height:1" @click="setAbsent"><span style="font-variant: all-small-caps">AFW</span></button>
            <button class="color-code amber-700" :class="{'is-selected': type === 'gafw'}" style="padding: 2px 4px;line-height:1" @click="setAuthAbsent"><span style="font-variant: all-small-caps">GAFW</span></button>
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
    private numValue: number|string = '';

    @Prop({type: [Number, String], default: null}) readonly score!: ResultType;

    get scoreInput() {
        return this.$refs['score-input'] as HTMLInputElement;
    }

    onEdit() {
        console.log('edit');
        if (this.type === 'number') {
            const value = parseFloat(this.scoreInput.value);
            this.$emit('ok', isNaN(value) ? null : value);
        } else if (this.type === 'afw') {
            this.$emit('ok', 'afw');
        } else if (this.type === 'gafw') {
            this.$emit('ok', 'gafw');
        }
    }

    setAbsent() {
        this.type = 'afw';
        this.$nextTick(() => this.numValue = '');
    }

    setAuthAbsent() {
        this.type = 'gafw';
        this.$nextTick(() => this.numValue = '');
    }

    mounted() {
        if (this.score === 'afw') { this.type = 'afw'; return; }
        if (this.score === 'gafw') { this.type = 'gafw'; return; }
        this.type = 'number';
        this.numValue = String(this.score);
        this.$nextTick(() => this.scoreInput.focus());
    }
}
</script>

<style lang="scss" scoped>

.number-input {
    opacity: .42;
}

.number-input.is-selected {
    opacity: 1;
}

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
    border-left: 1px solid #ced4da;
    z-index: 1;

    position: absolute;
    top: 1px;
    right: 1px;
    bottom: 1px;
}

label {
    font-weight: 500;
    margin-bottom: 0;
    font-size: 1.25rem;
    margin-left: .15rem;
}

/*.btn-afw, .btn-gafw {
    border: 1px solid transparent;
    border-radius: .2rem;
}*/

.color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    display: flex;
    justify-content: center;
    padding: 2px 4px;
    transition: background 75ms linear,color 75ms linear,opacity 75ms linear;
    width: fit-content;
    opacity: .42;
}

.color-code:hover, .color-code:focus {
    opacity: 1;
}

.color-code:focus-visible {
    outline: none;
}

.color-code.is-selected {
    opacity: 1;
    box-shadow: 0 0 0 .2rem var(--selected-color)
}

.color-code > span {
    font-size: 14px;
    font-variant: all-small-caps;
    font-weight: 900;
    line-height: 1.1;
}

.deep-orange-500 {
    --color: #ff5722;
    --selected-color: #d53300;
    --text-color: white;
}

.amber-700 {
    --color: #ffa000;
    --selected-color: #db8a00;
    --text-color: white;
}
</style>