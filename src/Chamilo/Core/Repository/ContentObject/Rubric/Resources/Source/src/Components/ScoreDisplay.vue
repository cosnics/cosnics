<template>
    <span :data-value="score"><span class="integral">{{integralDigits}}</span><span class="fractional" :class="{ 'is-zero': muteFraction && isFractionZero }">{{fractionalDigits}}</span><i v-if="showPercent" class="fa fa-percent" aria-hidden="true"></i><span v-if="showPercent" class="sr-only">%</span></span>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({})
export default class ScoreDisplay extends Vue {
    @Prop({type: Number, required: true}) readonly score!: number;
    @Prop({type: Boolean, default: false}) readonly showPercent!: boolean;
    @Prop({type: Boolean, default: true}) readonly muteFraction!: boolean;

    get twoDigits() {
        return this.score.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    get integralDigits() {
        const s = this.twoDigits;
        return s.substring(0, s.length - 3);
    }

    get fractionalDigits() {
        const s = this.twoDigits;
        return s.substring(s.length - 3);
    }

    get isFractionZero() {
        return this.fractionalDigits.endsWith('00');
    }
}
</script>

<style scoped>
.integral {
    font-size: 1.6rem;
    line-height: 1rem;
}
.fractional {
    font-size: 1.25rem;
    line-height: 1rem;
}
.fractional.is-zero {
    opacity: .7;
}

.fa-percent {
    font-size: 1.05rem;
    opacity: .8;
}
</style>