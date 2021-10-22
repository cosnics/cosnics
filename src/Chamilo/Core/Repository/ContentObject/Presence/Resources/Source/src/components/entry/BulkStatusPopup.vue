<i18n>
{
    "en": {
        "apply": "Apply",
        "cancel": "Cancel",
        "set-students-without-status": "Set all students without a status to"
    },
    "nl": {
        "apply": "Pas toe",
        "cancel": "Annuleer",
        "set-students-without-status": "Zet alle studenten zonder status op"
    }
}
</i18n>
<template>
    <b-popover target="show-more" :show.sync="isVisible" triggers="click" placement="bottom" custom-class="bulk-status">
        <div class="p-8">
            <div class="u-flex u-justify-content-center msg-text mb-6">{{ $t('set-students-without-status') }}:</div>
            <div class="u-flex u-gap-small u-flex-wrap mb-12">
                <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`"
                        class="color-code mod-selectable" :class="[status.color, selectedStatus === status ? 'mod-shadow is-selected' : 'mod-shadow-grey']" @click="selectedStatus = status">
                    <span>{{ status.code }}</span></button>
            </div>
            <div class="u-flex u-gap-small u-justify-content-end">
                <button class="btn btn-primary btn-sm px-8 py-2" @click="apply" :disabled="selectedStatus === null">{{ $t('apply') }}</button>
                <button class="btn btn-default btn-sm px-8 py-2" @click="cancel">{{ $t('cancel') }}</button>
            </div>
        </div>
    </b-popover>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {PresenceStatus} from '../../types';

@Component({
    name: 'bulk-status-popup'
})
export default class BulkStatusPopup extends Vue {
    selectedStatus: PresenceStatus|null = null;

    @Prop({type: Boolean, default: false}) readonly isVisible!: boolean;
    @Prop({type: Array, default: () => []}) readonly presenceStatuses!: PresenceStatus[];

    apply() {
        this.$emit('apply', this.selectedStatus);
        this.selectedStatus = null;
    }

    cancel() {
        this.$emit('cancel');
        this.selectedStatus = null;
    }
}
</script>

<style>
.bulk-status {
    max-width: 344px;
}
</style>
<style scoped>
.msg-text {
    color: #666;
    font-size: 13px;
    line-height: 16px;
}
.mb-6 {
    margin-bottom: 6px;
}
.mb-12 {
    margin-bottom: 12px;
}
.p-8 {
    padding: 8px;
}
.px-8 {
    padding-left: 8px;
    padding-right: 8px;
}
.py-2 {
    padding-top: 2px;
    padding-bottom: 2px;
}
</style>
