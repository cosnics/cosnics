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
    <b-popover :target="target" :show.sync="isVisible" triggers="click" placement="right" custom-class="bulk-status">
        <div style="padding: 4px 8px 8px; border-bottom: 1px solid #d4d4d4">
            <a :href="printQrCodeUrl" target="_blank" style="font-size: 13px;line-height: 16px">Toon QR code voor zelfregistratie</a>
        </div>
        <div style="padding: 4px 8px 8px; border-bottom: 1px solid #d4d4d4">
            <div class="u-flex u-justify-content-start u-align-items-baseline msg-text u-gap-small">
                <input type="checkbox" id="disable-selfreg-check"><label for="disable-selfreg-check" style="font-weight: normal;margin-bottom: 0">Beëindig zelfregistratie voor periode</label>
            </div>
        </div>
        <div style="padding: 8px 8px 8px">
            <div id="lbl-bulk" class="u-flex u-justify-content-start u-align-items-center msg-text u-cursor-pointer">{{ $t('set-students-without-status') }}
                <i class="fa fa-chevron-right" aria-hidden="true" style="color: #999; margin-left: 3px"></i></div>
            <b-popover target="lbl-bulk" triggers="hover" placement="rightbottom">
                <div class="p-8">
                    <div class="u-flex u-gap-small u-flex-wrap mb-12" role="radiogroup" aria-labelledby="lbl-bulk">
                        <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" :title="getPresenceStatusTitle(status)"
                                class="color-code mod-selectable" :class="[status.color, selectedStatus === status ? 'mod-shadow is-selected' : 'mod-shadow-grey']"
                                role="radio" :aria-checked="selectedStatus === status"
                                @click="selectedStatus = status">
                            <span>{{ status.code }}</span></button>
                    </div>
                    <div class="u-flex u-gap-small u-justify-content-end">
                        <button class="btn btn-primary btn-sm px-8 py-2" @click="apply" :disabled="selectedStatus === null">{{ $t('apply') }}</button>
                        <button class="btn btn-default btn-sm px-8 py-2" @click="cancel">{{ $t('cancel') }}</button>
                    </div>
                </div>
            </b-popover>
        </div>
    </b-popover>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {PresenceStatus, PresenceStatusDefault} from '../../types';

@Component({
    name: 'bulk-status-popup'
})
export default class BulkStatusPopup extends Vue {
    selectedStatus: PresenceStatus|null = null;

    @Prop({type: String, required: true}) readonly target!: string;
    @Prop({type: Boolean, default: false}) readonly isVisible!: boolean;
    @Prop({type: Array, default: () => []}) readonly presenceStatuses!: PresenceStatus[];
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: String, default: ''}) readonly printQrCodeUrl!: string;

    apply() {
        this.$emit('apply', this.selectedStatus);
        this.selectedStatus = null;
    }

    cancel() {
        this.$emit('cancel');
        this.selectedStatus = null;
    }

    getPresenceStatusTitle(status: PresenceStatus): string {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
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
