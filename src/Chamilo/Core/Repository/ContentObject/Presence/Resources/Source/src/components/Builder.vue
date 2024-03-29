<i18n>
{
    "en": {
        "new-presence-status": "New status",
        "label": "Label",
        "title": "Title",
        "aliasses": "Corresponds to",
        "color": "Color",
        "checkout": "Checkout possible",
        "no-checkout": "No checkout",
        "verification-icon": "Verification icon for self registration"
    },
    "nl": {
        "new-presence-status": "Nieuwe status",
        "label": "Label",
        "title": "Titel",
        "aliasses": "Komt overeen met",
        "color": "Kleur",
        "checkout": "Checkout mogelijk",
        "no-checkout": "Geen checkout",
        "verification-icon": "Verificatie-icoon voor zelfregistratie"
    }
}
</i18n>

<template>
    <div v-if="presence">
        <div class="u-flex u-flex-wrap u-gap-small-3x">
            <div class="presence-builder">
                <b-table ref="builder" bordered :items="presenceStatuses" :fields="fields"
                         class="mod-presence mod-builder" :class="{'is-enabled': isEditEnabled}" :tbody-tr-class="rowClass"
                         :selectable="isEditEnabled" select-mode="single" selected-variant="" @row-selected="onRowSelected">
                    <template #thead-top="">
                        <selection-preview :presence-statuses="presenceStatuses" class="presence-preview-row" />
                    </template>
                    <template #head(label)>{{ $t('label') }}</template>
                    <template #head(title)>{{ $t('title') }}</template>
                    <template #head(aliasses)>{{ $t('aliasses') }}</template>
                    <template #head(color)>{{ $t('color') }}</template>
                    <template #cell(label)="{item, index}">
                        <b-input type="text" required v-model="item.code" autocomplete="off" :disabled="isEditDisabled"
                                 class="mod-input mod-trans mod-pad mod-small" @focus="onSelectStatus(item, index)"/>
                    </template>
                    <template #cell(title)="{item, index}">
                        <title-control :status="item" :status-title="getStatusTitle(item)"
                                       :is-editable="isStatusEditable(item)" :disabled="isEditDisabled"
                                       @select="onSelectStatus(item, index)" />
                    </template>
                    <template #cell(aliasses)="{item, index}">
                        <alias-control :status="item" :alias-title="getAliasedTitle(item)" :fixed-status-defaults="fixedStatusDefaults"
                                       :is-editable="isStatusEditable(item)" :is-select-disabled="isEditDisabled"
                                       @select="onSelectStatus(item, index)"/>
                    </template>
                    <template #cell(color)="{item, index}">
                        <color-control :id="index" :disabled="isEditDisabled" :color="item.color" :selected="item === selectedStatus"
                                       class="u-flex u-align-items-center"
                                       @select="onSelectStatus(item, index)"
                                       @color-selected="setStatusColor(item, $event)"/>
                    </template>
                    <template #cell(actions)="{item, index}">
                        <selection-controls
                            :id="item.id"
                            :is-up-disabled="isEditDisabled || index === 0"
                            :is-down-disabled="isEditDisabled || index >= presenceStatuses.length - 1"
                            :is-remove-disabled="isEditDisabled || item.type === 'fixed' || savedEntryStatuses.includes(item.id)"
                            class="u-flex u-gap-small presence-actions"
                            @move-down="onMoveDown(item.id, index)" @move-up="onMoveUp(item.id, index)"
                            @remove="onRemove(item)" @select="onSelectStatus(item, index)"/>
                    </template>
                    <template #bottom-row v-if="createNew">
                        <b-td><b-input required type="text" class="mod-input mod-pad mod-small" id="new-presence-code" v-model="codeNew"/></b-td>
                        <b-td><b-input required type="text" class="mod-input mod-pad" v-model="titleNew"/></b-td>
                        <b-td><alias-control :status="aliasNew" :fixed-status-defaults="fixedStatusDefaults"/></b-td>
                        <b-td><color-control id="999" :color="colorNew" class="u-flex u-align-items-center" @color-selected="colorNew = $event"/></b-td>
                        <b-td class="table-actions"><new-status-controls :isSavingDisabled="!(codeNew && titleNew && aliasNew.aliasses > 0)"
                                                   class="u-flex u-gap-small presence-actions"
                                                   @save="onSaveNew" @cancel="onCancelNew" /></b-td>
                    </template>
                </b-table>
                <div class="m-new" v-if="!createNew">
                    <button class="btn-new-status u-text-no-underline" @click="onCreateNew"><i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t('new-presence-status') }}</button>
                </div>
                <error-display v-if="errorData" @close="errorData = null"><error-message :error-data="errorData" /></error-display>
            </div>
            <div class="u-align-self-start">
                <div style="margin-bottom: 15px">
                    <on-off-switch id="allow-checkout" :checked="presence.has_checkout" :on-text="$t('checkout')" :off-text="$t('no-checkout')"
                                   switch-class="mod-checkout-choice" style="width: 136px"
                                   @toggle="presence.has_checkout = !presence.has_checkout"/>
                </div>
                <div :style="useVerificationIcon ? 'margin-bottom: 10px' : ''">
                    <button :aria-pressed="useVerificationIcon ? 'true' : 'false'" :aria-expanded="useVerificationIcon ? 'true' : 'false'" class="btn-check" :class="{ 'checked': useVerificationIcon }"
                            @click="useVerificationIcon = !useVerificationIcon">
                        <span tabindex="-1" class="lbl-check"><i aria-hidden="true" class="btn-icon-check fa"></i>{{ $t('verification-icon') }}</span>
                    </button>
                </div>
                <verification-icon ref="verification-icon" v-show="useVerificationIcon" :icon-data="presence.verification_icon_data || null" :use-builder="true"></verification-icon>
            </div>
        </div>
        <div class="m-save">
            <save-control :is-disabled="isEditDisabled" :is-saving="isSaving" @save="onSave()" />
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
import {Presence, PresenceStatus, PresenceStatusDefault} from '../types';
import APIConfig from '../connect/APIConfig';
import Connector, {ConnectorErrorListener} from '../connect/Connector';
import TitleControl from './builder/TitleControl.vue';
import AliasControl from './builder/AliasControl.vue';
import ColorControl from './builder/ColorControl.vue';
import SelectionControls from './builder/SelectionControls.vue';
import NewStatusControls from './builder/NewStatusControls.vue';
import SelectionPreview from './builder/SelectionPreview.vue';
import SaveControl from './builder/SaveControl.vue';
import ErrorMessage from './builder/ErrorMessage.vue';
import ErrorDisplay from './ErrorDisplay.vue';
import VerificationIcon from './builder/VerificationIcon.vue';
import OnOffSwitch from './OnOffSwitch.vue';

const DEFAULT_COLOR_NEW = 'yellow-100';
const CONFLICT_ERRORS = ['PresenceStatusMissing', 'InvalidType', 'NoTitleGiven', 'TitleUpdateForbidden', 'InvalidAlias', 'AliasUpdateForbidden', 'NoCodeGiven', 'NoColorGiven', 'InvalidColor'];

@Component({
    components: {
        OnOffSwitch, TitleControl, AliasControl, ColorControl, SelectionControls, NewStatusControls, SelectionPreview, SaveControl, ErrorMessage, ErrorDisplay, VerificationIcon
    }
})
export default class Builder extends Vue {
    readonly fields = [
        { key: 'label', sortable: false },
        { key: 'title', sortable: false },
        { key: 'aliasses', sortable: false },
        { key: 'color', sortable: false },
        { key: 'actions', sortable: false, label: '', variant: 'actions' }
    ];

    statusDefaults: PresenceStatusDefault[] = [];
    presence: Presence | null = null;
    selectedStatus: PresenceStatus|null = null;
    savedEntryStatuses: number[] = [];
    useVerificationIcon = false;

    connector: Connector | null = null;
    errorData: string|null = null;

    createNew = false;
    codeNew = '';
    titleNew = '';
    aliasNew = { aliasses: 3};
    colorNew = DEFAULT_COLOR_NEW;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Number, default: 0}) readonly loadIndex!: number;

    async load(): Promise<void> {
        const presenceData : any = await this.connector?.loadPresence();
        this.statusDefaults = presenceData?.['status-defaults'] || [];
        this.presence = presenceData?.presence || null;
        if (this.presence?.verification_icon_data) {
            this.useVerificationIcon = true;
        }
    }

    async loadSavedEntryStatuses(): Promise<void> {
        const data: any = await this.connector?.loadRegisteredPresenceEntryStatuses();
        this.savedEntryStatuses = data?.statuses;
    }

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this as ConnectorErrorListener);
        this.load();
    }

    get isEditEnabled() {
        return !this.createNew;
    }

    get isEditDisabled() {
        return this.createNew;
    }

    get isSaving(): boolean {
        return this.connector?.isSaving || false;
    }

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    get fixedStatusDefaults(): PresenceStatusDefault[] {
        return this.statusDefaults.filter(sd => sd.type === 'fixed');
    }

    getStatusDefault(status: PresenceStatus, fixed = false): PresenceStatusDefault {
        const statusDefault = this.statusDefaults.find(sd => sd.id === status.id)!;
        if (!fixed) { return statusDefault; }
        return statusDefault.type === 'fixed' ? statusDefault : this.statusDefaults.find(sd => sd.id === statusDefault.aliasses)!;
    }

    isStatusEditable(status: PresenceStatus) {
        return !(status.type === 'fixed' || status.type === 'semifixed' || this.savedEntryStatuses.includes(status.id));
    }

    getStatusTitle(status: PresenceStatus) {
        if (status.type === 'fixed' || status.type === 'semifixed') {
            return this.getStatusDefault(status)?.title || '';
        }
        return status.title;
    }

    getAliasedTitle(status: PresenceStatus): string {
        if (status.type === 'fixed' || status.type === 'semifixed') {
            return this.getStatusDefault(status, true)?.title || '';
        }
        return this.statusDefaults.find(sd => sd.id === status.aliasses)?.title || '';
    }

    setStatusColor(status: PresenceStatus, color: string) {
        if (status.color !== color) {
            status.color = color;
        }
    }

    rowClass(status: PresenceStatus) : string {
        return `table-body-row presence-builder-row${status === this.selectedStatus ? ' is-selected' : ''}${ this.createNew ? '' : ' is-enabled'}`;
    }

    hasEmptyFields(): boolean {
        let hasEmptyFields = false;
        const inputs = [...document.querySelectorAll<HTMLFormElement>('.presence-builder .form-control')];
        inputs.reverse();
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.reportValidity();
                hasEmptyFields = true;
            }
        });
        return hasEmptyFields;
    }

    isConflictError(errorType: string): boolean {
        return CONFLICT_ERRORS.includes(errorType);
    }

    setError(data: any): void {
        this.errorData = data;
    }

    resetNew() {
        this.createNew = false;
        this.codeNew = '';
        this.titleNew = '';
        this.aliasNew.aliasses = 3;
        this.colorNew = DEFAULT_COLOR_NEW;
    }

    onCreateNew() {
        this.createNew = true;
        this.selectedStatus = null;
        this.$nextTick(() => {
            document.getElementById('new-presence-code')?.focus();
        });
    }

    onCancelNew() {
        this.resetNew();
    }

    onSaveNew() {
        if (!this.presence) { return; }
        this.presence.statuses.push({
            id: Math.max(this.statusDefaults.length, Math.max.apply(null, this.presence.statuses.map(s => s.id))) + 1,
            type: 'custom', code: this.codeNew, title: this.titleNew, aliasses: this.aliasNew.aliasses, color: this.colorNew
        });
        this.resetNew();
        this.$nextTick(() => {
            this.selectedStatus = this.presenceStatuses[this.presenceStatuses.length - 1];
        });
    }

    onSelectStatus(status: PresenceStatus, index: number = 0) {
        if (!this.createNew) {
            this.selectedStatus = status;
            (this.$refs['builder'] as unknown as any).selectRow(index);
        }
    }

    onRowSelected(items: PresenceStatus[]) {
        this.selectedStatus = items[0] || null;
    }

    onMoveDown(id: number, index: number) {
        if (!this.presence || index >= this.presence.statuses.length - 1) { return; }
        const statuses = this.presence.statuses;
        this.presence.statuses = statuses.slice(0, index).concat(statuses[index + 1], statuses[index]).concat(statuses.slice(index + 2));
        this.$nextTick(() => {
            let el : HTMLButtonElement|null = document.querySelector(`#btn-down-${id}`);
            if (el?.disabled) {
                el = el?.previousSibling as HTMLButtonElement;
            }
            el?.focus();
        });
    }

    onMoveUp(id: number, index: number) {
        if (!this.presence || index <= 0) { return; }
        const statuses = this.presence.statuses;
        this.presence.statuses = statuses.slice(0, index - 1).concat(statuses[index], statuses[index - 1]).concat(statuses.slice(index + 1));
        this.$nextTick(() => {
            let el : HTMLButtonElement|null = document.querySelector(`#btn-up-${id}`);
            if (el?.disabled) {
                el = el?.nextSibling as HTMLButtonElement;
            }
            el?.focus();
        });
    }

    onRemove(status: PresenceStatus) {
        if (!this.presence || status.type === 'fixed') { return; }
        const statuses = this.presence.statuses;
        const index = statuses.findIndex(s => s === status);
        if (index === -1) { return; }
        this.presence.statuses = statuses.slice(0, index).concat(statuses.slice(index + 1));
    }

    onSave() {
        if (!this.presence) { return; }
        if (this.hasEmptyFields()) { return; }
        this.setError(null);
        if (!this.useVerificationIcon) {
            this.presence.verification_icon_data = null;
        } else {
            this.presence.verification_icon_data = {version: 1, result: (this.$refs['verification-icon'] as unknown as any).verificationIconCode};
        }
        this.connector?.updatePresence(this.presence.id, this.presenceStatuses, this.presence.has_checkout, this.presence.verification_icon_data, (data: any) => {
            if (data?.status === 'ok') {
                this.$emit('presence-data-changed', {statusDefaults: this.statusDefaults, presence: this.presence});
            }
        });
    }

    @Watch('loadIndex')
    _loadIndex() {
        this.loadSavedEntryStatuses();
    }
}
</script>

<style>
.u-relative {
    position: relative;
}

.u-block {
    display: block;
}

.u-flex {
    display: flex;
}

.u-gap-small {
    gap: 5px;
}

.u-gap-small-2x {
    gap: 10px;
}

.u-gap-small-3x {
    gap: 15px;
}

.u-flex-wrap {
    flex-flow: wrap;
}

.u-align-items-start {
    align-items: flex-start;
}

.u-align-items-baseline {
    align-items: baseline;
}

.u-align-items-center {
    align-items: center;
}

.u-align-self-start {
    align-self: flex-start;
}

.u-justify-content-center {
    justify-content: center;
}

.u-justify-content-start {
    justify-content: flex-start;
}

.u-justify-content-end {
    justify-content: flex-end;
}

.u-justify-content-space-between {
    justify-content: space-between;
}

.u-max-w-fit {
    max-width: fit-content;
}

.u-bg-none {
    background: none;
}

.u-font-normal {
    font-weight: 400;
}

.u-font-medium {
    font-weight: 500;
}

.u-font-bold {
    font-weight: 700;
}

.u-font-italic {
    font-style: italic;
}

.u-text-center {
    text-align: center;
}

.u-text-no-underline, .u-text-no-underline:hover, .u-text-no-underline:focus {
    text-decoration: none;
}

.u-text-line-through {
    text-decoration: line-through;
}

.u-txt-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.u-pointer-events-none {
    pointer-events: none;
}

.u-cursor-default {
    cursor: default;
}

.u-cursor-pointer {
    cursor: pointer;
}
</style>
<style>
.table.mod-presence {
    border-top-color: #ebebeb;
}

.table.mod-presence th, .table.mod-presence td {
    border: 1px solid #ebebeb;
    vertical-align: middle;
}

.table.mod-presence th {
    background-color: #f8fbfb;
    border-bottom: 0;
    color: #727879;
}

.table-body-row:first-child td {
    background: linear-gradient(to bottom, #e3eaed 0, rgba(255, 255, 255, 0) 4px);
    border-top: 0;
}
</style>
<style>
.table.mod-builder {
    border: none;
    position: relative;
}

.table.mod-builder .table-actions {
    background: none;
    border: none;
}

.table.mod-builder tr:first-child th {
    background-color: #fff;
}

.table.mod-builder .presence-preview-row th:not(.table-actions) {
    border-top: 1px solid #ebebeb;
}

.presence-builder-row {
    transition: opacity 200ms linear;
}

.presence-builder-row:focus {
    outline: none;
}

.presence-builder-row:not(.is-enabled) {
    opacity: .8;
}

.presence-builder-row:not(.is-selected) .table-actions {
    pointer-events: none;
}

.presence-builder-row.is-selected td:not(.table-actions) {
    background: #ecf4f4;
    background-clip: padding-box;
    border-color: #e3e3e3;
}

.presence-builder-row.is-selected:first-child td:not(.table-actions) {
    background: linear-gradient(to bottom, #e3eaed 0, #ecf4f4 4px);
}

@media (pointer: fine) {
    .presence-builder-row.is-enabled:not(.is-selected):hover td:not(.table-actions) {
        background: #f4fbfb;
        border-color: #e3e3e3;
        cursor: pointer;
    }

    .presence-builder-row.is-enabled:not(.is-selected):first-child:hover td:not(.table-actions) {
        background: linear-gradient(to bottom, #e3eaed 0, #f4fbfb 4px);
    }
}
</style>
<style>
.color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    display: flex;
    height: 24px;
    justify-content: center;
    min-width: 40px;
    padding: 4px;
    transition: background 75ms linear, color 75ms linear, opacity 75ms linear;
}

.color-code > span {
    font-size: 14px;
    font-variant: all-small-caps;
    font-weight: 900;
    line-height: 12px;
}

.form-control.mod-input.mod-pad {
    height: initial;
    padding: 2px 5px;
}

.form-control.mod-input.mod-small {
    width: 64px;
}

.form-control.mod-select {
    height: 26px;
    padding: 3px 5px;
}

.form-control.mod-trans {
    transition: background 200ms linear;
}

.btn-new-status {
    background: none;
    border: none;
    color: #337ab7;
    padding: 0;
}

.btn-new-status:hover,
.btn-new-status:focus {
    color: #23527c;
}

.btn-new-status > .fa {
    font-size: 13px;
    margin-right: 2px;
}

.btn.btn-default.mod-status-action {
    color: #4d88b3;
    font-size: 14px;
    padding: 2px 5px;
    width: 25px;
}

.btn.mod-status-action.mod-remove:not(:disabled),
.btn.mod-status-action.mod-cancel {
    color: red;
}

.presence-builder-row:not(.is-selected) .btn.mod-status-action {
    opacity: 0;
}

@media only screen and (max-width: 459px) {
    .presence-actions {
        bottom: -45px;
        position: absolute;
        right: 17px;
    }

    .btn.btn-default.mod-status-action {
        font-size: 20px;
        width: 30px;
    }
}

.btn-check {
    background: none;
    border: none;
    color: #888;
    cursor: default;
    margin: 0;
    padding: 0;
}

.btn-check:focus,
.btn-check:hover {
    color: #1d4567;
}

.lbl-check {
    border: 1px solid transparent;
    border-radius: 3px;
}

.btn-check,
.lbl-check:focus {
    outline: none;
}

.btn-icon-check {
    margin-right: .3em;
    width: 1em;
}

.btn-icon-check:before {
    content: '\f096';
}

.btn-check.checked .btn-icon-check:before {
    content: '\f046';
}
</style>
<style scoped>
.m-new {
    margin: 8px 0 0 8px;
}

.m-errors {
    margin: 10px 0 0 0;
    max-width: 85ch;
}

.m-save {
    margin: 20px 0 0 8px;
}

.btn-check.checked {
    color: #526060;
}
</style>
<style>
.lds-ellipsis {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
}

.lds-ellipsis div {
    position: absolute;
    top: 13px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: hsla(190, 40%, 45%, 1);
    animation-timing-function: cubic-bezier(0, 1, 1, 0);
}

.lds-ellipsis div:nth-child(1) {
    left: 8px;
    animation: lds-ellipsis1 0.6s infinite;
}

.lds-ellipsis div:nth-child(2) {
    left: 8px;
    animation: lds-ellipsis2 0.6s infinite;
}

.lds-ellipsis div:nth-child(3) {
    left: 32px;
    animation: lds-ellipsis2 0.6s infinite;
}

.lds-ellipsis div:nth-child(4) {
    left: 56px;
    animation: lds-ellipsis3 0.6s infinite;
}

@keyframes lds-ellipsis1 {
    0% {
        transform: scale(0);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes lds-ellipsis3 {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(0);
    }
}

@keyframes lds-ellipsis2 {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(24px, 0);
    }
}
</style>
<style lang="scss">
:root {
    --text-color-dark: #333;
    --text-color-light: #fff;
}

/*  Material Design colors
 *  Credits:
 *
 *  Original color palette by
 *  https://www.google.com/design/spec/style/color.html
 */

.pink-100 {
    $color: #f8bbd0;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.pink-300 {
    $color: #f06292;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 20%)};
}

.pink-500 {
    $color: #e91e63;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 30%)};
}

.pink-700 {
    $color: #c2185b;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 40%)};
}

.pink-900 {
    $color: #880e4f;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 50%)};
}

.blue-100 {
    $color: #bbdefb;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.blue-300 {
    $color: #64b5f6;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 15%)};
}

.blue-500 {
    $color: #2196f3;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 25%)};
}

.blue-700 {
    $color: #1976d2;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 30%)};
}

.blue-900 {
    $color: #0d47a1;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 40%)};
}

.cyan-100 {
    $color: #b2ebf2;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.cyan-300 {
    $color: #4dd0e1;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 25%)};
}

.cyan-500 {
    $color: #00bcd4;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 5%)};
}

.cyan-700 {
    $color: #0097a7;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 15%)};
}

.cyan-900 {
    $color: #006064;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 25%)};
}

.teal-100 {
    $color: #b2dfdb;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.teal-300 {
    $color: #4db6ac;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 25%)};
}

.teal-500 {
    $color: #009688;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 5%)};
}

.teal-700 {
    $color: #00796b;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 15%)};
}

.teal-900 {
    $color: #004d40;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 20%)};
}

.green-100 {
    $color: #c8e6c9;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.green-300 {
    $color: #81c784;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 10%)};
}

.green-500 {
    $color: #4caf50;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 10%)};
}

.green-700 {
    $color: #388e3c;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 15%)};
}

.green-900 {
    $color: #1b5e20;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 25%)};
}

.light-green-100 {
    $color: #dcedc8;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 15%)};
}

.light-green-300 {
    $color: #aed581;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 15%)};
}

.light-green-500 {
    $color: #8bc34a;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 15%)};
}

.light-green-700 {
    $color: #689f38;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 15%)};
}

.light-green-900 {
    $color: #33691e;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 25%)};
}

.lime-100 {
    $color: #f0f4c3;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 15%)};
}

.lime-300 {
    $color: #dce775;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 15%)};
}

.lime-500 {
    $color: #cddc39;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.lime-700 {
    $color: #afb42b;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 10%)};
}

.lime-900 {
    $color: #827717;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 20%)};
}

.yellow-100 {
    $color: #fff9c4;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 30%)};
}

.yellow-300 {
    $color: #fff176;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 25%)};
}

.yellow-500 {
    $color: #ffeb3b;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 15%)};
}

.yellow-700 {
    $color: #fbc02d;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.yellow-900 {
    $color: #f57f17;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 10%)};
}

.amber-100 {
    $color: #ffecb3;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 20%)};
}

.amber-300 {
    $color: #ffd54f;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 20%)};
}

.amber-500 {
    $color: #ffc107;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.amber-700 {
    $color: #ffa000;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 7%)};
}

.amber-900 {
    $color: #ff6f00;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 10%)};
}

.deep-orange-100 {
    $color: #ffccbc;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 10%)};
}

.deep-orange-300 {
    $color: #ff8a65;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 20%)};
}

.deep-orange-500 {
    $color: #ff5722;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 15%)};
}

.deep-orange-700 {
    $color: #e64a19;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{darken($color, 15%)};
}

.deep-orange-900 {
    $color: #bf360c;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 30%)};
}

.grey-100 {
    $color: #f5f5f5;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 20%)};
}

.grey-300 {
    $color: #e0e0e0;
    --color: #{$color};
    --text-color: var(--text-color-dark);
    --selected-color: #{darken($color, 30%)};
}

.grey-500 {
    $color: #9e9e9e;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 15%)};
}

.grey-700 {
    $color: #616161;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 45%)};
}

.grey-900 {
    $color: #212121;
    --color: #{$color};
    --text-color: var(--text-color-light);
    --selected-color: #{lighten($color, 55%)};
}
</style>
<style>
.bs-popover-auto[x-placement^=right], .bs-popover-right {
    margin-left: .5rem;
}

.bs-popover-auto[x-placement^=left], .bs-popover-left {
    margin-right: .5rem;
}

.bs-popover-auto[x-placement^=bottom], .bs-popover-bottom {
    margin-top: .5rem;
}

.bs-popover-auto[x-placement^=top], .bs-popover-top {
    margin-bottom: .5rem;
}
</style>
<style>
.tbl-sort-option {
    background-position: right calc(.75rem / 2) center;
    background-repeat: no-repeat;
    background-size: .65em 1em;
    cursor: pointer;
    padding-right: calc(.75rem + .85em);
    pointer-events: all;
}

.tbl-sort-option[aria-sort=none] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='101' height='101' view-box='0 0 101 101' preserveAspectRatio='none'%3e%3cpath fill='black' opacity='.3' d='M51 1l25 23 24 22H1l25-22zM51 101l25-23 24-22H1l25 22z'/%3e%3c/svg%3e");
}

.tbl-sort-option[aria-sort=ascending] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='101' height='101' view-box='0 0 101 101' preserveAspectRatio='none'%3e%3cpath fill='black' d='M51 1l25 23 24 22H1l25-22z'/%3e%3cpath fill='black' opacity='.3' d='M51 101l25-23 24-22H1l25 22z'/%3e%3c/svg%3e");
}

.tbl-sort-option[aria-sort=descending] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='101' height='101' view-box='0 0 101 101' preserveAspectRatio='none'%3e%3cpath fill='black' opacity='.3' d='M51 1l25 23 24 22H1l25-22z'/%3e%3cpath fill='black' d='M51 101l25-23 24-22H1l25 22z'/%3e%3c/svg%3e");
}
</style>
<style>
.onoffswitch {
    position: relative;
}

.onoffswitch.mod-checkout {
    width: 136px;
}

.onoffswitch.mod-checkout-choice {
    width: 136px;
}

.onoffswitch-checkbox {
    display: none;
}

.onoffswitch.mod-self-disable {
    width: 200px;
}

.onoffswitch.mod-self-disable.mod-period {
    width: 220px;
}

.onoffswitch-label {
    border: 1px solid #80a2b3;
    border-radius: 3px;
    cursor: pointer;
    display: block;
    margin-bottom: 0;
    overflow: hidden;
}

.onoffswitch-label.mod-self-disable, .onoffswitch-label.mod-checkout, .onoffswitch-label.mod-checkout-choice {
    border-color: #d6dee0;
}

.onoffswitch-checkbox:checked + .onoffswitch-label.mod-checkout {
    border-color: #6dab6f;
}

.onoffswitch-checkbox:checked + .onoffswitch-label.mod-self-disable,
.onoffswitch-checkbox:checked + .onoffswitch-label.mod-checkout-choice {
    border-color: #91a0b1;
}

.onoffswitch-inner {
    display: block;
    margin-left: -100%;
    transition: margin .2s ease-in 0s;
    width: 200%;
}

.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}

.onoffswitch-inner-before, .onoffswitch-inner-after {
    box-sizing: border-box;
    color: #fff;
    display: block;
    float: left;
    font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
    font-weight: 500;
    height: 20px;
    line-height: 18px;
    padding: 0;
    width: 50%;
}

.onoffswitch-inner-before.mod-checkout-choice, .onoffswitch-inner-after.mod-checkout-choice {
    font-size: 12px;
    line-height: 20px;
}

.onoffswitch-inner-before.mod-checkout {
    background-color: #6dab6f;
    color: white;
    padding-left: 8px;
}

.onoffswitch-inner-before.mod-self-disable,
.onoffswitch-inner-before.mod-checkout-choice {
    background-color: #91a0b1;
    color: white;
    padding-left: 8px;
}

.onoffswitch-inner-after {
    background-color: #fff;
    color: #919191;
    padding-left: 16px;
}

.onoffswitch-switch {
    background: #ffffff;
    border: 1px solid #80a2b3;
    border-radius: 3px;
    bottom: 0;
    display: block;
    margin: 0;
    position: absolute;
    right: calc(100% - 12px);
    top: 0;
    transition: all .2s ease-in 0s;
    width: 12px;
}

.onoffswitch-switch.mod-self-disable, .onoffswitch-switch.mod-checkout, .onoffswitch-switch.mod-checkout-choice {
    border-color: #c2ced1;
}

.onoffswitch-checkbox:checked+.onoffswitch-label .onoffswitch-switch.mod-checkout {
    border-color: #6dab6f;
}

.onoffswitch-checkbox:checked+.onoffswitch-label .onoffswitch-switch.mod-self-disable,
.onoffswitch-checkbox:checked+.onoffswitch-label .onoffswitch-switch.mod-checkout-choice {
    border-color: #91a0b1;
}

.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0;
}
</style>
