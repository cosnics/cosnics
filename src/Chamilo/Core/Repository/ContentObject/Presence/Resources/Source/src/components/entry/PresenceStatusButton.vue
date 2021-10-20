<template>
    <button :title="title" :disabled="isDisabled" :aria-pressed="isSelected ? 'true': 'false'"
            class="color-code mod-selectable"
            :class="[isDisabled && !isSelected ? 'grey-100' : status.color, { 'is-selected': isSelected, 'is-checkout-mode': isDisabled }]"
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

    select() {
        if (!this.isSelected) {
            this.$emit('select');
        }
    }
}
</script>
