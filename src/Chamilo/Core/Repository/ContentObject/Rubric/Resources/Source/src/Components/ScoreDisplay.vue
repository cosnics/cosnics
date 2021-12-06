<template>
    <span v-if="!fractionDigits">{{ this.score }}<i v-if="showPercent" class="fa fa-percent" aria-hidden="true"></i><span v-if="showPercent" class="sr-only">%</span></span>
    <span v-else :data-value="score"><span class="integral" :class="{'mod-small': showPercent}">{{integralDigits}}</span><span class="fractional" :class="{ 'mod-half-mute': muteFraction && isFractionZero && showPercent, 'mod-mute': muteFraction && isFractionZero && !showPercent }">{{fractionalDigits}}</span><i v-if="showPercent" class="fa fa-percent" aria-hidden="true"></i><span v-if="showPercent" class="sr-only">%</span></span>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({})
export default class ScoreDisplay extends Vue {
    @Prop({type: Number, required: true}) readonly score!: number;
    @Prop({type: Object, required: true}) readonly options!: any;


    get fractionDigits() {
        return this.options.fractionDigits;
    }

    get muteFraction() {
        return this.options.muteFraction;
    }

    get showPercent() {
        return this.options.showPercent;
    }

    get oneDigit() {
        return this.score.toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 1});
    }

    get twoDigits() {
        return this.score.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    get integralDigits() {
        const s = this.fractionDigits === 1 ? this.oneDigit : this.twoDigits;
        return s.substring(0, s.length - (this.fractionDigits === 1 ? 2 : 3));
    }

    get fractionalDigits() {
        const s = this.fractionDigits === 1 ? this.oneDigit : this.twoDigits;
        return s.substring(s.length - (this.fractionDigits === 1 ? 2 : 3));
    }

    get isFractionZero() {
        return (this.fractionDigits === 2 && this.fractionalDigits.endsWith('00') || this.fractionDigits === 1 && this.fractionalDigits.endsWith('0'));
    }
}
</script>

<style scoped>
.integral {
    line-height: 1rem;
}

.integral.mod-small {
    font-size: 1.6rem;
}

.fractional {
    font-size: 1.25rem;
    line-height: 1rem;
}

.fractional.mod-half-mute {
    opacity: .7;
}

.fractional.mod-mute {
    opacity: .3;
}

.fa-percent {
    font-size: 1.05rem;
    opacity: .8;
}
</style>