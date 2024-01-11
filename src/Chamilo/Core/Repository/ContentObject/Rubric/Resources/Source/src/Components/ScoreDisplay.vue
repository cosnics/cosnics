<template>
    <span v-if="score !== null" :style="isInvalid ? 'color: #e10505' : ''"><i v-if="isInvalid" class="fa fa-warning" aria-hidden="true" style="font-size: .8125rem; margin-right: .1875rem;"></i>{{ this.score.toLocaleString(undefined, {maximumFractionDigits: 2}) }}<template v-if="percent"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template></span>
    <span v-else aria-hidden="true" v-html="'&nbsp;'"></span>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({})
export default class ScoreDisplay extends Vue {
    @Prop({type: Number, required: true}) readonly score!: number|null;
    @Prop({type: Boolean, default: false}) readonly percent!: boolean;
    @Prop({type: Boolean, default: false}) readonly isInvalid!: boolean;
}
</script>
