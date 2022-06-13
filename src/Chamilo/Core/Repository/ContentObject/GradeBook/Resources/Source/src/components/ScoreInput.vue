<template>
    <highlight-input @edit="onEdit" @cancel="$emit('cancel')">
        <div class="score-input-wrap">
            <div class="number-input" :class="{'is-selected': type === 'number'}">
                <input id="score" class="percent-input" ref="score-input" type="number" :value="numValue|formatNum" autocomplete="off" @input="type = 'number'" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')" @focus="type = 'number'">
                <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
            <button class="color-code deep-orange-500" :class="{'is-selected': type === 'afw'}" @click="setAbsent"><span>AFW</span></button>
            <button class="color-code amber-700" :class="{'is-selected': type === 'gafw'}" @click="setAuthAbsent"><span>GAFW</span></button>
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
.score-input-wrap {
    display: flex;
    gap: 5px;
}

.number-input {
    opacity: .42;
    position: relative;

    &.is-selected {
        opacity: 1;
    }
}

.percent-input {
    border: 1px solid #ced4da;
    border-radius: .2rem;
    color: #333;
    font-weight: 400;
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
        box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%), 0 0 8px rgb(102 175 233 / 60%);
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

.color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    display: flex;
    justify-content: center;
    line-height: 1;
    opacity: .42;
    padding: 2px 4px;
    transition: background 75ms linear,color 75ms linear,opacity 75ms linear;
    width: fit-content;

    &:hover, &:focus {
        opacity: 1;
    }

    &:focus-visible {
        outline: none;
    }

    &.is-selected {
        box-shadow: 0 0 0 .2rem var(--selected-color);
        opacity: 1;
    }

    > span {
        font-size: 14px;
        font-variant: all-small-caps;
        font-weight: 900;
        line-height: 1.1;
    }
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