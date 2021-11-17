<template>
    <div>
        <button :id="`color-${id}`" :disabled="disabled"
                class="btn-color" :class="[{'is-selected': selected}, color]"
                @focus="$emit('select')"></button>
        <color-picker :target="`color-${id}`" :selected-color="color"
                      triggers="click blur" placement="right"
                      @color-selected="$emit('color-selected', $event)"></color-picker>
    </div>
</template>
<script lang="ts">
import ColorPicker from './ColorPicker.vue'

import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'color-control',
    components: {ColorPicker}
})
export default class ColorControl extends Vue {
    @Prop({type: Number, default: 0}) readonly id!: number;
    @Prop({type: Boolean, default: false}) readonly disabled!: boolean;
    @Prop({type: Boolean, default: false}) readonly selected!: boolean;
    @Prop({type: String, default: ''}) readonly color!: string;
}
</script>

<style>
.btn-color {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    height: 18px;
    transition: opacity 200ms linear, background 75ms linear;
    width: 40px;
}

.btn-color[disabled] {
    cursor: not-allowed;
    opacity: .4;
}

.btn-color.is-selected, .btn-color:hover {
    box-shadow: 1px 1px 2px -1px #673ab7;
}
</style>