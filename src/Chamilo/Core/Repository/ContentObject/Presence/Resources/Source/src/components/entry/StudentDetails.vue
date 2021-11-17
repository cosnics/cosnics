<i18n>
{
    "en": {
        "not-checked-out": "Not checked out"
    },
    "nl": {
        "not-checked-out": "Niet uitgechecked"
    }
}
</i18n>
<template>
    <b-tr>
        <b-td>{{ periodTitle }}</b-td>
        <b-td v-if="showStatus"><presence-status-display :title="title" :label="label" :color="color" /></b-td>
        <template v-if="hasCheckout">
            <template v-if="showCheckout">
                <b-td>{{ checkInDateFormatted }}</b-td>
                <b-td v-if="isCheckedOut">{{ checkOutDateFormatted }}</b-td>
                <b-td v-else class="not-checked-out">{{ $t('not-checked-out') }}</b-td>
            </template>
            <template v-else>
                <b-td><div class="color-code mod-none"></div></b-td>
                <b-td><div class="color-code mod-none"></div></b-td>
            </template>
        </template>
        <template v-else>
            <b-td v-if="!!checkInDate">{{ checkInDateFormatted }}</b-td>
            <b-td v-else><div class="color-code mod-none"></div></b-td>
        </template>
    </b-tr>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import PresenceStatusDisplay from './PresenceStatusDisplay.vue';

function pad(num: number) : string {
    return `${num < 10 ? '0' : ''}${num}`;
}

@Component({
    name: 'student-details',
    components: {PresenceStatusDisplay},
    filters: {
        fDate: function (date: Date) {
            if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                return '';
            }
            return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
        }
    }
})
export default class StudentDetails extends Vue {
    @Prop({type: Boolean, default: false}) readonly hasCheckout!: boolean;
    @Prop({type: Boolean, default: false}) readonly showStatus!: boolean;
    @Prop({type: String, default: ''}) readonly periodTitle!: string;
    @Prop({type: String, default: ''}) readonly title!: string;
    @Prop({type: String, default: ''}) readonly label!: string;
    @Prop({type: String, default: ''}) readonly color!: string;
    @Prop({type: Number, default: 0}) readonly checkInDate!: number|undefined|null;
    @Prop({type: Number, default: 0}) readonly checkOutDate!: number|undefined|null;

    get showCheckout() {
        return this.hasCheckout && !!this.checkInDate;
    }

    createDate(timestamp: number) {
        const d = new Date(0);
        d.setUTCSeconds(timestamp);
        return d;
    }

    formatDate(timestamp: number) {
        const d = new Date(0);
        d.setUTCSeconds(timestamp);
        return d.toLocaleString();
    }

    get isCheckedOut() {
        if (typeof this.checkOutDate !== 'number' || typeof this.checkInDate !== 'number') { return false; }
        return this.checkOutDate > this.checkInDate;
    }

    getDateFormatted(timestamp: number) {
        const date = this.createDate(timestamp);
        if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
            return '';
        }
        return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    get checkInDateFormatted() {
        if (typeof this.checkInDate !== 'number') { return ''; }
        return this.getDateFormatted(this.checkInDate);
    }

    get checkOutDateFormatted() {
        if (typeof this.checkOutDate !== 'number') { return ''; }
        return this.getDateFormatted(this.checkOutDate);
    }
}

</script>

<style scoped>
.color-code.mod-none {
    width: 105px;
}
.not-checked-out {
    color: #919191;
}
</style>