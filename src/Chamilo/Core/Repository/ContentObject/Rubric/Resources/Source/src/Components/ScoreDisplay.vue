<template>
    <span v-if="score !== null">{{ this.score.toLocaleString(undefined, {maximumFractionDigits: 2}) }}<template v-if="percent"><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></template></span>
    <span v-else></span>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({})
export default class ScoreDisplay extends Vue {
    @Prop({type: Number, required: true}) readonly score!: number|null;
    @Prop({type: Boolean, default: false}) readonly percent!: boolean;
}
</script>
