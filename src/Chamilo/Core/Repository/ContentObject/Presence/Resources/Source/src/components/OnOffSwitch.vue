<template>
    <div class="onoffswitch" :class="[switchClass]" style="display: block;">
        <input type="checkbox" :id="`onoffswitch-${id}`" class="onoffswitch-checkbox"
               :checked="checked"
               @input="$emit('toggle')">
        <label class="onoffswitch-label" :class="[switchClass]" :for="`onoffswitch-${id}`">
            <span class="onoffswitch-inner">
                <span class="onoffswitch-inner-before" :class="[switchClass]">{{ onText }}</span>
                <span class="onoffswitch-inner-after" :class="[switchClass]">{{ offText }}</span>
            </span>
            <span class="onoffswitch-switch" :class="[switchClass]"></span>
        </label>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'on-off-switch'
})
export default class OnOffSwitch extends Vue {
    @Prop({type: Boolean}) readonly checked!: boolean;
    @Prop({type: String, default: ''}) readonly id!: string;
    @Prop({type: String, default: ''}) readonly onText!: string;
    @Prop({type: String, default: ''}) readonly offText!: string;
    @Prop({type: String, default: ''}) readonly switchClass!: string;
}
</script>