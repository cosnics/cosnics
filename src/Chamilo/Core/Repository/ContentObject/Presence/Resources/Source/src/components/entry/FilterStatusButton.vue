<template>
    <button :title="title" :aria-pressed="isSelected ? 'true': 'false'"
            class="color-code mod-selectable" :class="[color, {'is-selected': isSelected}]"
            @click="$emit('toggle-filter')"><span>{{ label }}</span></button>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'filter-status-button'
})
export default class FilterStatusButton extends Vue {
    @Prop({type: Boolean}) readonly isSelected!: boolean;
    @Prop({type: String, default: ''}) readonly title!: string;
    @Prop({type: String, default: ''}) readonly label!: string;
    @Prop({type: String, default: ''}) readonly color!: string;
}
</script>
