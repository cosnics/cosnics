<i18n>
{
    "en": {
        "display": "Display",
        "move-up": "Move up",
        "move-down": "Move down",
        "remove": "Remove",
        "save": "Save",
        "cancel": "Cancel",
        "new-presence-status": "New presence status",
        "label": "Label",
        "title": "Title",
        "meaning": "Maps to",
        "color": "Color"
    },
    "nl": {
        "display": "Weergave",
        "move-up": "Verplaats naar boven",
        "move-down": "Verplaats naar beneden",
        "remove": "Verwijder",
        "save": "Opslaan",
        "cancel": "Annuleren",
        "new-presence-status": "Nieuwe aanwezigheidsstatus",
        "label": "Label",
        "title": "Titel",
        "meaning": "Mapt naar",
        "color": "Kleur"
    }
}
</i18n>

<template>
    <div v-if="presence" @click.stop="selectedStatus = null">
        <b-table bordered :foot-clone="createNew" :items="presenceStatuses" :fields="fields"
                 class="mod-presence mod-builder" :class="{'is-changes-disabled': createNew}"
                 :tbody-tr-class="rowClass">
            <template #thead-top="">
                <b-tr>
                    <b-th style="background: white;font-weight: 400">{{ $t('display') }}</b-th>
                    <b-th colspan="3" style="background: white">
                        <div class="u-flex u-gap-small u-flex-wrap" style="padding: 4px 0">
                            <div v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code"
                                 :class="[status.color]"><span>{{ status.code }}</span></div>
                        </div>
                    </b-th>
                </b-tr>
            </template>
            <template #cell(code)="status">
                <div class="cell-pad" @click.stop="onSelectStatus(status.item)">
                    <b-input type="text" v-model="status.item.code" autocomplete="off" :disabled="createNew"
                             class="mod-input mod-pad mod-small" @focus="onSelectStatus(status.item)"/>
                </div>
            </template>
            <template #cell(title)="status">
                <div class="cell-pad" @click.stop="onSelectStatus(status.item)">
                    <template v-if="status.item.type === 'fixed' || status.item.type === 'semifixed'"><span
                        style="line-height: 26px">{{ getStatusDefault(status.item).title }}</span></template>
                    <template v-else-if="savedEntryStatuses.includes(status.item.id)"><span
                        style="line-height: 26px">{{ status.item.title }}</span></template>
                    <b-input v-else type="text" v-model="status.item.title" autocomplete="off" :disabled="createNew"
                             class="mod-input mod-pad" @focus="onSelectStatus(status.item)"/>
                </div>
            </template>
            <template #head(code)>{{ $t('label') }}</template>
            <template #head(title)>{{ $t('title') }}</template>
            <template #head(meaning)>{{ $t('meaning') }}</template>
            <template #head(color)>{{ $t('color') }}</template>
            <template #cell(meaning)="status">
                <div class="cell-pad" style="line-height: 26px" @click.stop="onSelectStatus(status.item)">
                    <span v-if="status.item.type === 'fixed' || status.item.type === 'semifixed'">{{
                            getStatusDefault(status.item, true).title
                        }}</span>
                    <span v-else-if="savedEntryStatuses.includes(status.item.id)">{{ statusDefaults.find(s => s.id === status.item.aliasses).title }}</span>
                    <select v-else class="form-control mod-select" :disabled="createNew"
                            @focus="onSelectStatus(status.item)" v-model="status.item.aliasses">
                        <option v-for="(statusDefault, index) in fixedStatusDefaults" :key="`fs-${index}`"
                                :value="statusDefault.id">{{ statusDefault.title }}
                        </option>
                    </select>
                </div>
            </template>
            <template #cell(color)="status">
                <div class="u-flex cell-pad" style="align-items: center; height: 42px;"
                     @click.stop="onSelectStatus(status.item)">
                    <button :id="`color-${status.index}`" class="color"
                            :class="[{'is-selected': status.item === selectedStatus}, status.item.color]"
                            :disabled="createNew" @focus="onSelectStatus(status.item)"></button>
                    <color-picker :target="`color-${status.index}`" triggers="click blur" placement="right"
                                  :selected-color="status.item.color"
                                  @color-selected="setColorForItem(status.item, $event)"></color-picker>
                </div>
            </template>
            <template #cell(actions)="status">
                <div class="cell-pad-x">
                    <div class="u-flex u-gap-small presence-actions">
                        <button class="btn btn-default btn-sm mod-presence" :title="$t('move-up')"
                                :disabled="createNew || status.index === 0" @click.stop="onMoveUp(status)"
                                :id="`btn-up-${status.index}`" @focus="onSelectStatus(status.item)">
                            <i class="fa fa-arrow-up" aria-hidden="true"></i>
                            <span class="sr-only">{{ $t('move-up') }}</span>
                        </button>
                        <button class="btn btn-default btn-sm mod-presence" :title="$t('move-down')"
                                :disabled="createNew || status.index >= presenceStatuses.length - 1"
                                @click.stop="onMoveDown(status)" :id="`btn-down-${status.index}`"
                                @focus="onSelectStatus(status.item)">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                            <span class="sr-only">{{ $t('move-down') }}</span>
                        </button>
                        <button :title="$t('remove')" :disabled="createNew || status.item.type === 'fixed' || savedEntryStatuses.includes(status.item.id)"
                                class="btn btn-default btn-sm mod-presence" @click.stop="onRemove(status.item)"
                                @focus="onSelectStatus(status.item)">
                            <i class="fa fa-minus-circle" aria-hidden="true"></i>
                            <span class="sr-only">{{ $t('remove') }}</span>
                        </button>
                    </div>
                </div>
            </template>
            <template #foot(code)="">
                <input type="text" class="form-control mod-input mod-pad mod-small" id="new-presence-code"
                       v-model="codeNew"/>
            </template>
            <template #foot(title)="">
                <b-input type="text" class="mod-input mod-pad" v-model="titleNew"/>
            </template>
            <template #foot(meaning)="">
                <select class="form-control mod-select" v-model="aliasNew">
                    <option v-for="(statusDefault, index) in fixedStatusDefaults" :key="`fs-${index}`"
                            :value="statusDefault.id">{{ statusDefault.title }}
                    </option>
                </select>
            </template>
            <template #foot(color)="">
                <div class="u-flex">
                    <button class="color" :class="colorNew" id="color-new"></button>
                    <color-picker target="color-new" triggers="click blur" placement="right" :selected-color="colorNew"
                                  @color-selected="colorNew = $event"></color-picker>
                </div>
            </template>
            <template #foot(actions)="">
                <div class="u-flex u-gap-small presence-actions">
                    <button class="btn btn-default btn-sm mod-presence" :title="$t('save')" @click.stop="onSaveNew"
                            :disabled="!(codeNew && titleNew && aliasNew > 0)">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                        <span class="sr-only">{{ $t('save') }}</span>
                    </button>
                    <button class="btn btn-default btn-sm mod-presence mod-cancel" :title="$t('cancel')"
                            @click.stop="onCancelNew">
                        <i class="fa fa-minus-circle" aria-hidden="true"></i>
                        <span class="sr-only">{{ $t('cancel') }}</span>
                    </button>
                </div>
            </template>
        </b-table>
        <div style="margin: 8px 0 0 8px" v-if="!createNew">
            <a class="presence-new" @click="onCreateNew"><i class="fa fa-plus" aria-hidden="true"></i>
                {{ $t('new-presence-status') }}</a>
        </div>
        <div style="margin: 16px 0 0 8px" v-if="!createNew">
            <button class="btn btn-primary mod-presence-save" @click="onSave()">{{ $t('save') }}</button>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
import {PresenceStatusDefault, PresenceStatus, Presence} from '../types';
import APIConfig from '../connect/APIConfig';
import Connector from '../connect/Connector';
import ColorPicker from './ColorPicker.vue';

//import Builder from './Builder.vue';
const DEFAULT_COLOR_NEW = 'yellow-100';

@Component({
    components: {ColorPicker}
})
export default class Main extends Vue {
    readonly fields = [
        { key: 'code', sortable: false },
        { key: 'title', sortable: false },
        { key: 'meaning', sortable: false },
        { key: 'color', sortable: false },
        { key: 'actions', sortable: false, label: '', variant: 'actions' }
    ];

    statusDefaults: PresenceStatusDefault[] = [];
    presence: Presence | null = null;
    savedEntryStatuses: number[] = [];
    connector: Connector | null = null;

    createNew = false;
    codeNew = '';
    titleNew = '';
    aliasNew = 3;
    colorNew = DEFAULT_COLOR_NEW;

    selectedStatus: PresenceStatus|null = null;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Number, default: 0}) readonly loadIndex!: number;

    async load(): Promise<void> {
        const presenceData : any = await this.connector?.loadPresence();
        if (presenceData) {
            this.statusDefaults = presenceData['status-defaults'];
            this.presence = presenceData.presence;
        }
    }

    async loadSavedEntryStatuses(): Promise<void> {
        const data: any = await this.connector?.loadRegisteredPresenceEntryStatuses();
        if (data) {
            this.savedEntryStatuses = data.statuses;
        }
    }

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    onRemove(status: PresenceStatus) {
        if (!this.presence || status.type === 'fixed') {
            return;
        }
        const statuses = this.presence.statuses;
        const index = statuses.findIndex(o => o === status);
        if (index === -1) {
            return;
        }
        this.presence.statuses = statuses.slice(0, index).concat(statuses.slice(index + 1));
    }

    onSave() {
        if (!this.presence) {
            return;
        }
        this.connector?.updatePresence(this.presence.id, this.presenceStatuses, (data: any) => {
            this.$emit('presence-data-changed', {statusDefaults: this.statusDefaults, presence: this.presence});
        });
    }

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.load();
    }

    get fixedStatusDefaults(): PresenceStatusDefault[] {
        return this.statusDefaults.filter((s : PresenceStatusDefault) => s.type === 'fixed');
    }

    getStatusDefault(status: PresenceStatus, fixed = false): PresenceStatusDefault {
        const statusDefault = this.statusDefaults.find(s => s.id === status.id)!;
        if (!fixed) { return statusDefault; }
        return statusDefault.type === 'fixed' ? statusDefault : this.statusDefaults.find(s => s.id === statusDefault.aliasses)!;
    }

    onSelectStatus(status: PresenceStatus) {
        if (!this.createNew) {
            this.selectedStatus = status;
        }
    }

    onCreateNew() {
        this.createNew = true;
        this.selectedStatus = null;
        this.$nextTick(() => {
            document.getElementById('new-presence-code')?.focus();
        });
    }

    onSaveNew() {
        if (!this.presence) {
            return;
        }
        const status = {
            id: Math.max(this.statusDefaults.length, Math.max.apply(null, this.presence.statuses.map(s => s.id))) + 1,
            type: 'custom', code: this.codeNew, title: this.titleNew, aliasses: this.aliasNew, color: this.colorNew
        };
        this.presence.statuses.push(status);

        this.resetNew();
        this.$nextTick(() => {
            this.selectedStatus = this.presenceStatuses[this.presenceStatuses.length - 1];
        });
    }

    onCancelNew() {
        this.resetNew();
    }

    resetNew() {
        this.createNew = false;
        this.codeNew = '';
        this.titleNew = '';
        this.aliasNew = 3;
        this.colorNew = DEFAULT_COLOR_NEW;
    }

    onMoveDown(status: any) {
        const index = status.index;
        if (!this.presence || index >= this.presence.statuses.length - 1) {
            return;
        }
        const statuses = this.presence.statuses;
        this.presence.statuses = statuses.slice(0, index).concat(statuses[index + 1], statuses[index]).concat(statuses.slice(index + 2));
        this.$nextTick(() => {
            let el : HTMLButtonElement|null = document.querySelector(`#btn-down-${status.index + 1}`);
            if (el?.disabled) {
                el = el?.previousSibling as HTMLButtonElement;
            }
            el?.focus();
        });
    }

    onMoveUp(status: any) {
        const index = status.index;
        if (!this.presence || index <= 0) {
            return;
        }
        const statuses = this.presence.statuses;
        this.presence.statuses = statuses.slice(0, index - 1).concat(statuses[index], statuses[index - 1]).concat(statuses.slice(index + 1));
        this.$nextTick(() => {
            let el : HTMLButtonElement|null = document.querySelector(`#btn-up-${status.index - 1}`);
            if (el?.disabled) {
                el = el?.nextSibling as HTMLButtonElement;
            }
            el?.focus();
        });
    }

    setColorForItem(item: PresenceStatus, color: string) {
        if (item.color !== color) {
            item.color = color;
        }
    }

    rowClass(item: PresenceStatus) : string {
        if (item === this.selectedStatus) { return 'is-selected'; }
        return '';
    }

    @Watch('loadIndex')
    _loadIndex() {
        this.loadSavedEntryStatuses();
    }
}
</script>

<style>
.u-flex {
    display: flex;
}

.u-gap-small {
    gap: 5px;
}

.u-flex-wrap {
    flex-flow: wrap;
}

.u-justify-content-end {
    justify-content: flex-end;
}

.u-txt-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn.mod-presence-save:focus {
    color: #fff;
}

.presence-new {
    color: #337ab7;
    cursor: pointer;
}

.presence-new:hover, .presence-new:focus {
    color: #23527c;
    text-decoration: none;
}

.presence-new .fa {
    font-size: 13px;
    margin-right: 5px;
}

.btn.btn-default.mod-presence {
    color: #4d88b3;
    font-size: 14px;
    padding: 2px 5px;
    width: 25px;
}

.btn.mod-presence.mod-cancel .fa {
    color: red;
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
    width: initial;
}

.presence-swatches {
    display: grid;
    grid-gap: 2px;
    grid-template-columns: repeat(11, 1fr);
    padding: 2px;
}

.color, .color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
}

.color[disabled] {
    cursor: not-allowed;
    opacity: .4;
}

.color {
    height: 16px;
    width: 40px;
}

.color.mod-swatch {
    width: 20px;
    z-index: 1000;
}

.table.mod-presence .color {
    transition: opacity 200ms linear, background 75ms linear;
}

.color-code {
    transition: background 75ms linear, color 75ms linear, opacity 75ms linear;
}

.table-period .color-code {
    opacity: .42;
}

.table-period .color-code.is-selected,
.table-period .color-code:hover {
    opacity: 1;
}

.table.mod-builder .color.is-selected, .table.mod-builder .color:hover, .table-period .color-code:not(.is-selected):hover {
    box-shadow: 1px 1px 2px -1px #673ab7;
}

.color-code.is-selected {
    box-shadow: 0 0 0 .2rem var(--selected-color);
}

.color-code.mod-none {
    --color: #deede1;
    background: none;
    background-image: linear-gradient(135deg, var(--color) 10%, transparent 10%, transparent 50%, var(--color) 50%, var(--color) 60%, transparent 60%, transparent 100%);
    background-size: 7px 7px;
    border-radius: 5px;
    height: 17px;
}

.color.mod-swatch.is-selected {
    position: relative;
}

.color.mod-swatch.is-selected:after {
    content: '\f00c';
    font-family: 'FontAwesome';
    font-size: 11px;
    left: calc(50% - .5em);
    position: absolute;
    text-align: center;
    top: 0;
}

.color-code {
    display: flex;
    height: 20px;
    min-width: 40px;
    padding: 2px 4px;
    justify-content: center;
}

.color-code > span {
    font-size: 14px;
    font-variant: all-small-caps;
    font-weight: 900;
    line-height: 12px;
}

.color-code.is-selected {
    position: relative;
}

/*.color-code.is-selected:after {
    background-color: inherit;
    /*  border: 1px solid rgba(255, 255, 255, .92);*/
    /*border-radius: 50%;
    bottom: -5px;
    content: '\f00c';
    font-family: 'FontAwesome';
    font-size: 8px;
    font-weight: 400;
    line-height: 8px;
    padding: 2px 1px 1px 2px;
    position: absolute;
    right: -5px;
    z-index: 10;
}*/

.tbl-no-sort {
    pointer-events: none
}

.table.mod-presence {
    border-top-color: #ebebeb;
}

.table.mod-presence thead th:not(.table-actions) {
    background-color: #f8fbfb;
}

.table.mod-presence.mod-builder thead th:not(.table-actions) {
    border-top: 1px solid #ebebeb;
}

.table.mod-presence thead th {
    color: #727879;
}

.table.mod-presence th, .table.mod-presence td {
    border: 1px solid #ebebeb;
    vertical-align: middle;
}

.table.mod-presence .table-actions {
    border: 0;
}

.table.mod-builder {
    border-right: 0;
    border-bottom: 0;
    border-top: 0;
}

.table.mod-presence tbody {
    transition: opacity 200ms linear;
}

.table.mod-presence thead th {
    border-bottom: 0;
}

.table.mod-presence tbody tr:first-child td:not(.table-actions) {
    background: linear-gradient(to bottom, #e3eaed 0, rgba(255, 255, 255, 0) 4px);
    border-top: 0;
}

.table.mod-builder {
    margin-bottom: 0;
    width: fit-content;
}

.table.mod-builder .form-control {
    transition: background 200ms linear;
}

.table.mod-builder.is-changes-disabled tbody {
    opacity: .8;
}

.table.mod-builder tfoot th {
    font-weight: 400;
}

.table.mod-builder {
    position: relative;
}

.table.mod-builder td {
    padding: 0;
}

.table.mod-builder .cell-pad {
    padding: 8px;
}

.table.mod-builder .cell-pad-x {
    padding: 0 8px;
}

@media only screen and (max-width: 459px) {
    .table.mod-builder .presence-actions {
        position: absolute;
        right: 17px;
        bottom: -45px;
    }

    .table.mod-builder .btn.mod-presence {
        font-size: 20px;
        width: unset;
    }
}

@media (pointer: fine) {
    .table.mod-builder tbody tr:hover td:not(.table-actions) {
        background: #f4fbfb;
        cursor: pointer;
    }

    .table.mod-builder tbody tr:first-child:hover td:not(.table-actions) {
        background: linear-gradient(to bottom, #e3eaed 0, #f4fbfb 4px);
    }

    .table.mod-builder tbody tr:hover td:not(.table-actions) {
        border-color: #e3e3e3;
    }
}

.table.mod-builder tbody tr.is-selected td:not(.table-actions) {
    background: #ecf4f4;
}

.table.mod-builder tbody tr.is-selected:first-child td:not(.table-actions) {
    background: linear-gradient(to bottom, #e3eaed 0, #ecf4f4 4px);
}

.table.mod-builder tbody tr.is-selected td:not(.table-actions) {
    border-color: #e3e3e3;
}

.table.mod-builder.is-changes-disabled tbody tr, .table.mod-builder.is-changes-disabled tbody tr:hover {
    background: unset;
    border-color: unset;
    cursor: unset;
}

.table.mod-builder tr.is-selected .btn.mod-presence:last-child:not(:disabled) {
    color: red;
}

.table.mod-builder tbody tr:not(.is-selected) .table-actions {
    pointer-events: none;
}

.table.mod-builder tbody tr:not(.is-selected) .btn.mod-presence {
    pointer-events: none;
    opacity: 0;
    box-shadow: none;
}

/*.radio-tabs-default {
  background-color: rgba(255, 255, 255, .45);
  border-radius: 3px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
  color: #337ab7;
}

.radio-tabs-default:hover, .radio-tabs-default:focus {
  background-color: #fff;
  box-shadow: 0 1px 2px hsla(208, 55%, 25%, .10);
  color: #507177;
}

.radio-tabs {
  cursor: pointer;
  font-weight: normal;
  margin: 0;
  padding: 0 .5em;
}

.radio-tabs-active, .radio-tabs-active:hover, .radio-tabs-active:focus  {
  background-color: #fff;
  box-shadow: 0 1px 2px hsla(208, 55%, 25%, .40);
  color: #507177;
}*/

.table.mod-entry th.table-result {
    max-width: 1px;
}
.table.mod-entry th.table-result.mod-save {
    position: relative;
    text-align: center;
}
@-moz-document url-prefix() {
    .table.mod-entry th.table-period {
        background-clip: padding-box;
    }
}
.selected-period-close-btn {
    align-items: center;
    background: #f8fbfb;
    border: 1px solid #337ab7;
    display: flex;
    height: 22px;
    justify-content: center;
    position: absolute;
    right: -1px;
    top: -22px;
    border-bottom: none;
    font-size: 12px;
    font-weight: normal;
    padding: 4px 8px 0;
}

.selected-period-close-btn:hover {
    color: #5a93c4;
}

.table.mod-entry th.table-period > div {
    align-items: center;
    display: flex;
    gap: 5px;
    max-width: fit-content;
}

.select-period-btn {
    background: #f7f7f7;
    border: 1px solid;
    border-color: rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.2);
    border-radius: 4px;
    color: #333;
    font-weight: 400;
    padding: 1px 4px;
    text-align: center;
}

.select-period-btn:hover, .select-period-btn:focus {
    background: #ededed;
}

/*.table.mod-entry .table-period {
    border-right: 0;
}

.table.mod-entry .table-result {
    border-left: 0;
}*/

.table-period {
    position: relative;
}

.table-result .result-wrap {
    margin: 0 auto;
    max-width: fit-content;
}

tfoot .table-period {
    text-align: right;
}

/*.table.mod-entry tbody .table-period:after {
    background-color: #ebebeb;
    bottom: 8px;
    content: '';
    display: block;
    position: absolute;
    right: -1px;
    top: 8px;
    width: 1px;
}*/

.table.mod-entry tfoot tr {
    border: 1px solid transparent;
}

.table.mod-entry tfoot th {
    border: none;
    font-weight: 400;
    padding: 6px 8px;
}

.table.mod-entry + .lds-ellipsis {
    display: none;
    left: calc(50% - 20px);
    position: absolute;
    top: 40px;
}

.table.mod-entry[aria-busy=true] + .lds-ellipsis {
    display: inline-block;
}

.btn-remove {
    background: none;
    border: none;
    color: #d9534f;
    cursor: pointer;
    padding: 0;
}

.btn-remove[disabled] {
    cursor: not-allowed;
}

.btn-remove:hover {
    color: #b72d2a;
}

.btn-remove:active, .btn-remove:focus {
    color: #ac2925;
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