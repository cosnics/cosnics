<i18n>
{
    "en": {
        "checked-out": "Checked out",
        "not-checked-out": "Not checked out"
    },
    "nl": {
        "checked-out": "Uitgechecked",
        "not-checked-out": "Niet uitgechecked"
    }
}
</i18n>

<template>
    <div class="result-wrap" :class="{'u-flex': showCheckout, 'u-align-items-center': showCheckout}">
        <div :title="title" class="color-code u-cursor-default" :class="[color || 'mod-none']">
            <span>{{ label }}</span>
        </div>
        <template v-if="showCheckout">
            <i aria-hidden="true" class="fa fa-sign-out checkout-indicator" :class="{'is-checked-out': isCheckedOut }" :title="$t(isCheckedOut ? 'checked-out' : 'not-checked-out')"></i>
            <span class="sr-only">{{ $t(isCheckedOut ? 'checked-out' : 'not-checked-out') }}</span>
        </template>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'presence-status-display'
})
export default class PresenceStatusDisplay extends Vue {
    @Prop({type: Boolean, default: false}) readonly hasCheckout!: boolean;
    @Prop({type: String, default: ''}) readonly title!: string;
    @Prop({type: String, default: ''}) readonly label!: string;
    @Prop({type: String, default: ''}) readonly color!: string;
    @Prop({type: Number, default: 0}) readonly checkInDate!: number|undefined|null;
    @Prop({type: Number, default: 0}) readonly checkOutDate!: number|undefined|null;

    get showCheckout() {
        return this.hasCheckout && !!this.checkInDate;
    }

    get isCheckedOut() {
        if (typeof this.checkOutDate !== 'number' || typeof this.checkInDate !== 'number') { return false; }
        return this.checkOutDate > this.checkInDate;
    }
}
</script>
