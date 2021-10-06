<template>
    <div class="u-flex" style="align-items: baseline; gap: 5px">
        <div :class="['color-code', color]" style="padding: 2px 4px; height: 20px;">
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