<template>
    <button :title="isActive ? title : ''" :disabled="isDisabled" role="radio" :aria-checked="isSelected ? 'true': 'false'"
            class="color-code"
            :class="[isActive ? status.color : 'mod-disabled', {'mod-selectable': isActive, 'mod-shadow-grey': isActive && !isSelected, 'mod-shadow is-selected': isSelected}]"
            @click="select">
        <span>{{ status.code }}</span>
    </button>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {PresenceStatus} from '../../types';

@Component({
    name: 'presence-status-button'
})
export default class PresenceStatusButton extends Vue {
    @Prop({type: Boolean}) readonly isSelected!: boolean;
    @Prop({type: Boolean}) readonly isDisabled!: boolean;
    @Prop({type: Object, required: true}) readonly status!: PresenceStatus;
    @Prop({type: String, default: ''}) readonly title!: string;

    get isActive() {
        return this.isSelected || !this.isDisabled;
    }

    select() {
        if (!this.isSelected) {
            this.$emit('select');
        }
    }
}
</script>
