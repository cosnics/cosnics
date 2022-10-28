<i18n>
{
    "en": {
        "aabs": "AABS",
        "absent": "Absent",
        "auth-absent": "Authorized absent",
        "comments": "Comments",
        "score": "Score",
        "use-source-result": "Use source result"
    },
    "nl": {
        "aabs": "GAFW",
        "absent": "Afwezig",
        "auth-absent": "Gewettigd afwezig",
        "comments": "Opmerkingen",
        "score": "Score",
        "use-source-result": "Gebruik bronresultaat"
    }
}
</i18n>
<template>
    <highlight-input @edit="onEdit" @cancel="$emit('cancel')">
        <template v-slot:menu>
            <div class="highlight-content">
                <div class="u-flex u-justify-content-end u-text-end">
                    <div class="menu-tab mod-left u-cursor-pointer" :class="{'mod-active': menuTab === 'score'}" @click="$emit('menu-tab-changed', 'score')">{{ $t('score') }}</div>
                    <div class="menu-tab mod-right u-cursor-pointer" :class="{'mod-active': menuTab === 'comment'}" @click="$emit('menu-tab-changed', 'comment')">{{ $t('comments') }}</div>
                </div>
            </div>
        </template>
        <template v-slot:content>
            <div v-if="menuTab === 'score'" class="u-flex u-gap-small">
                <div class="number-input u-relative" :class="{'is-selected': type === 'number'}" style="flex: 1">
                    <input id="score" class="percent-input u-font-normal" ref="score-input" type="number" min="0" max="100" :value="numValue|formatNum" autocomplete="off" @input="type = 'number'" @keyup.enter="onEdit" @keyup.esc="$emit('cancel')" @focus="type = 'number'">
                    <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                </div>
                <button class="color-code amber-700" :class="{'is-selected': type === 'aabs'}" @click="setAuthAbsent" :title="$t('auth-absent')"><span>{{ $t('aabs') }}</span></button>
                <button v-if="useRevert" class="btn btn-secundary btn-sm btn-revert" @click="setRevert" :title="$t('use-source-result')"><i class="fa fa-undo" aria-hidden="true"></i><span class="sr-only">{{ $t('use-source-result') }}</span></button>
            </div>
            <div v-else-if="menuTab === 'comment'">
                <textarea class="comment-field" ref="comment-input" v-model="commentValue"></textarea>
            </div>
        </template>
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
    private type: 'number'|'aabs'|'revert' = 'number';
    private numValue: number|string = '';
    private commentValue: string = '';

    @Prop({type: [Number, String], default: null}) readonly score!: ResultType;
    @Prop({type: String, default: null}) readonly comment!: string;
    @Prop({type: String, default: 'score'}) readonly menuTab!: string;
    @Prop({type: Boolean, default: false}) readonly useRevert!: boolean;

    get scoreInput() {
        return this.$refs['score-input'] as HTMLInputElement;
    }

    get commentInput() {
        return this.$refs['comment-input'] as HTMLInputElement;
    }

    onEdit() {
        if (this.menuTab === 'comment') {
            this.$emit('comment-updated', this.commentValue || null);
            return;
        }
        if (this.type === 'number') {
            const el = this.scoreInput as HTMLInputElement;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            const value = parseFloat(this.scoreInput.value);
            this.$emit('ok', isNaN(value) ? null : value);
        } else if (this.type === 'aabs') {
            this.$emit('ok', 'aabs');
        } else if (this.type === 'revert') {
            this.$emit('revert');
        }
    }

    setAuthAbsent() {
        this.type = 'aabs';
        this.$nextTick(() => this.numValue = '');
    }

    setRevert() {
        this.type = 'revert';
        this.$nextTick(() => this.numValue = '');
    }

    mounted() {
        if (this.score === 'aabs') { this.type = 'aabs'; return; }
        this.type = 'number';
        this.numValue = String(this.score);
        this.commentValue = this.comment || '';

        if (this.menuTab === 'comment') {
            this.$nextTick(() => this.commentInput.focus());
        } else {
            this.$nextTick(() => this.scoreInput.focus());
        }
    }
}
</script>

<style lang="scss" scoped>
.highlight-content {
    background-color: transparent;
    font-size: 11px;
    padding: 0;
    right: 0;
    top: -23px;
}

.highlight-content::v-deep + .highlight-content {
    border-top-right-radius: 0;
}

.menu-tab {
    color:#2e6da4;
    padding: 2px 8px;

    &.mod-left {
        background: linear-gradient(to left, #dedede 0, transparent 2px),linear-gradient(to top, #e0e0e0 0, #e6e6e6 4px);
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    &.mod-right {
        background: linear-gradient(to right, #dedede 0, transparent 2px),linear-gradient(to top, #e0e0e0 0, #e6e6e6 4px);
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    &.mod-active {
        background: #fff;
        color: #333;
    }
}

.number-input {
    opacity: .42;

    &.is-selected {
        opacity: 1;
    }
}

.comment-field:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6);
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

.btn-revert {
    color: #b8b9bc;
    padding: 2px 4px;

    &:hover {
        background-color: #e3e3e3;
        color: #919297;
    }

    &:focus {
        background-color: #337ab7;
        color: white;
        outline: 0;
    }
}
</style>