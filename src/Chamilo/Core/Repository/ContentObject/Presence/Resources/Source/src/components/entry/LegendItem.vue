<template>
    <div class="u-flex u-align-items-baseline u-gap-small">
        <div :class="['color-code', color]" style="height: 20px; padding: 2px 4px;">
            <span>{{label}}</span>
        </div>
        <span>{{ title }}</span>
    </div>
</template>
<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'legend-item'
})
export default class LegendItem extends Vue {
    @Prop({type: String, default: ''}) readonly label!: string;
    @Prop({type: String, default: ''}) readonly color!: string;
    @Prop({type: String, default: ''}) readonly title!: string;
}
</script>