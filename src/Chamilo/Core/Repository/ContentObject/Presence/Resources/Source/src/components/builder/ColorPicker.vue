<template>
  <b-popover :target="target" :triggers="triggers" :placement="placement">
    <div class="presence-swatches">
      <template v-for="variant in variants">
        <template v-for="color in colors">
          <button :class="[`btn-color mod-swatch ${color}-${variant}`, {'is-selected': selectedColor === `${color}-${variant}`}]" :key="`color-swatch-${color}-${variant}`" @click.stop="$emit('color-selected', `${color}-${variant}`)"></button>
        </template>
      </template>
    </div>
  </b-popover>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';

@Component({
  name: 'color-picker'
})
export default class ColorPicker extends Vue {
  
  readonly variants = [100, 300, 500, 700, 900];
  readonly colors = ['pink', 'blue', 'cyan', 'teal', 'green', 'light-green', 'lime', 'yellow', 'amber', 'deep-orange', 'grey'];
  
  @Prop({type: String, required: true}) readonly target!: string;
  @Prop({type: String, default: 'click'}) readonly triggers!: string;
  @Prop({type: String, default: 'bottom'}) readonly placement!: string;
  @Prop({type: String, default: ''}) readonly selectedColor!: string;
}
</script>

<style>
.presence-swatches {
    display: grid;
    grid-gap: 2px;
    grid-template-columns: repeat(11, 1fr);
    padding: 2px;
}

.btn-color.mod-swatch {
    width: 20px;
    z-index: 1000;
}

.btn-color.mod-swatch.is-selected {
    position: relative;
}

.btn-color.mod-swatch.is-selected:after {
    content: '\f00c';
    font-family: 'FontAwesome';
    font-size: 11px;
    left: calc(50% - .5em);
    position: absolute;
    text-align: center;
    top: 0;
}
</style>