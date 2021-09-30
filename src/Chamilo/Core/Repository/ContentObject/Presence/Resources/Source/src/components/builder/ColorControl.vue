<template>
    <div @click.stop="$emit('select')">
        <button :id="`color-${id}`" :disabled="disabled"
                class="color" :class="[{'is-selected': selected}, color]"
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
