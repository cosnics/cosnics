<i18n>
{
    "en": {
        "last-name": "Last name",
        "first-name": "First name",
        "official-code": "Official code",
        "total": "Total",
        "stop-edit-mode": "Stop editing",
        "new-period": "New period",
        "remove-period": "Remove period",
        "error-Timeout": "The server took too long to respond. Your changes have possibly not been saved. You can try again later.",
        "error-LoggedOut": "It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.",
        "error-Unknown": "An unknown error occurred. Your changes have possibly not been saved. You can try again later.",
        "checked-out": "Checked out",
        "not-checked-out": "Not checked out",
        "checkout-mode": "Checkout mode",
        "export": "Export",
        "legend": "Legend"
    },
    "nl": {
        "last-name": "Familienaam",
        "first-name": "Voornaam",
        "official-code": "Officiële code",
        "total": "Totaal",
        "stop-edit-mode": "Sluit editeren af",
        "new-period": "Nieuwe periode",
        "remove-period": "Verwijder periode",
        "error-LoggedOut": "Het lijkt erop dat je uitgelogt bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "checked-out": "Uitgechecked",
        "not-checked-out": "Niet uitgechecked",
        "checkout-mode": "Uitcheckmodus",
        "export": "Exporteer",
        "legend": "Legende"
    }
}
</i18n>

<template>
    <div>
        <div v-if="canEditPresence" class="u-flex u-align-items-center u-gap-small-3x u-max-w-fit m-controls">
            <search-bar :search-options="searchOptions" @filter-changed="onFilterChanged" @filter-cleared="onFilterCleared" />
            <div>
                <a :href="apiConfig.exportURL" class="btn btn-default btn-sm">{{ $t('export') }}</a>
            </div>
        </div>
        <div>
            <div class="u-relative">
                <div v-if="!canEditPresence" class="u-flex u-align-items-baseline u-gap-small-3x m-legend">
                    <span style="color: #507177">{{ $t('legend') }}:</span>
                    <legend-item v-for="status in presenceStatuses" :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color" />
                </div>
                <b-table ref="table" :foot-clone="canEditPresence && !!selectedPeriod && !checkoutMode" bordered :items="itemsProvider" :fields="fields" class="mod-presence mod-entry"
                         :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :per-page="pagination.perPage"
                         :current-page="pagination.currentPage" :filter="globalSearchQuery" no-sort-reset>
                    <template slot="table-colgroup" v-if="canEditPresence">
                        <col>
                        <col>
                        <col>
                        <col v-if="!!selectedPeriod && !creatingNew">
                        <col v-else-if="creatingNew" class="bd-selected-period">
                        <template v-if="!!selectedPeriod || creatingNew" v-for="period in periodsReversed">
                            <col v-if="!creatingNew && period === selectedPeriod" class="bd-selected-period">
                            <col v-else>
                        </template>
                    </template>
                    <template #cell(photo)="{item}" v-if="canEditPresence">
                        <img :src="item.photo">
                    </template>
                    <template #head(fullname) v-if="canEditPresence">
                        <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">{{ $t('last-name') }}</a>
                        <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">{{ $t('first-name') }}</a>
                    </template>
                    <template #head(fullname) v-else>Student</template>
                    <template #foot(fullname)><span></span></template>
                    <template #head(official_code) v-if="canEditPresence">
                        <a class="tbl-sort-option" :aria-sort="getSortStatus('official_code')" @click="sortByNameField('official_code')">{{ $t('official-code') }}</a>
                    </template>
                    <template #head(official_code) v-else>{{ $t('official-code') }}</template>
                    <template #head(new_period)>
                        <div role="button" tabindex="0" class="select-period-btn mod-new" @keyup.enter="createResultPeriod('')" @click="createResultPeriod('')" :title="$t('new-period')">
                            <i aria-hidden="true" class="fa fa-plus" style="color: #337ab7"></i> <span class="sr-only">{{ $t('new-period') }}</span>
                        </div>
                    </template>
                    <template #foot(new_period)><span></span></template>
                    <template #foot(official_code)><span></span></template>
                    <template #cell(fullname)="{item}">
                        {{ item.lastname.toUpperCase() }}, {{ item.firstname }}
                    </template>
                    <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="{label}">
                        <dynamic-field-key :is-editable="canEditPresence" @select="setSelectedPeriod(fieldKey.id)" :class="[{'select-period-btn' : canEditPresence}, 'u-txt-truncate']" :title="label">
                            <template v-slot>
                                <span v-if="label">{{ label }}</span>
                                <span v-else class="u-font-italic">{{ getPlaceHolder(fieldKey.id) }}</span>
                            </template>
                        </dynamic-field-key>
                    </template>
                    <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`cell(${fieldKey.key})`]="{item}">
                        <div class="result-wrap">
                            <div :title="getStatusTitleForStudent(item, fieldKey.id)" class="color-code u-cursor-default" :class="[getStatusColorForStudent(item, fieldKey.id) || 'mod-none']">
                                <span>{{ getStatusCodeForStudent(item, fieldKey.id) }}</span>
                            </div>
                        </div>
                    </template>
                    <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`foot(${fieldKey.key})`]><span></span></template>
                    <template #head(period-entry-plh)>
                        <div class="u-flex u-align-items-center u-gap-small">
                            <b-input type="text" autocomplete="off" :placeholder="$t('new-period') + '...'" class="u-bg-none u-font-normal u-font-italic u-pointer-events-none ti-label mod-border"></b-input>
                            <div class="spin">
                                <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                            </div>
                        </div>
                    </template>
                    <template #cell(period-entry-plh)>
                        <div class="u-flex u-gap-small u-flex-wrap u-pointer-events-none">
                            <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code mod-plh"
                                    :class="[status.color]"><span>{{ status.code }}</span></button>
                        </div>
                    </template>
                    <template #head(period-entry)>
                        <div class="u-flex u-align-items-center u-gap-small">
                            <b-input type="text" debounce="750" autocomplete="off" :placeholder="getPlaceHolder(selectedPeriod.id)" v-model="selectedPeriodLabel" class="u-font-normal ti-label"></b-input>
                            <div class="spin">
                                <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                            </div>
                        </div>
                        <div v-if="presence && presence.has_checkout" class="selected-period-controls">
                            <on-off-switch id="checkout" switch-class="mod-checkout-choice"
                                           :on-text="$t('checkout-mode')" :off-text="$t('checkout-mode')" :checked="checkoutMode"
                                           @toggle="checkoutMode = !checkoutMode"/>
                        </div>
                        <button :title="$t('stop-edit-mode')" class="btn btn-default btn-sm selected-period-close-btn" @click="selectedPeriod = null"><i aria-hidden="true" class="fa fa-close"></i><span class="sr-only">{{ $t('stop-edit-mode') }}</span></button>
                    </template>
                    <template #cell(period-entry)="{item}">
                        <template v-if="presence && presence.has_checkout && checkoutMode">
                            <on-off-switch v-if="item[`period#${selectedPeriod.id}-checked_in_date`]"
                                           :id="item.id" :on-text="$t('checked-out')" :off-text="$t('not-checked-out')"
                                           :checked="item[`period#${selectedPeriod.id}-checked_out_date`] > item[`period#${selectedPeriod.id}-checked_in_date`]"
                                           switch-class="mod-checkout"
                                           @toggle="toggleCheckout(item)"/>
                            <div v-else :title="getStatusTitleForStudent(item, selectedPeriod.id)" class="color-code u-cursor-default" :class="[getStatusColorForStudent(item, selectedPeriod.id) || 'mod-none']">
                                <span>{{ getStatusCodeForStudent(item, selectedPeriod.id) }}</span>
                            </div>
                        </template>
                        <div v-else class="u-flex u-gap-small u-flex-wrap">
                            <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code mod-selectable"
                                    :class="[status.color, { 'is-selected': hasSelectedStudentStatus(item, status.id) }]"
                                    :title="getPresenceStatusTitle(status)"
                                    @click="!hasSelectedStudentStatus(item, status.id) ? setSelectedStudentStatus(item, status.id) : null"
                                    :aria-pressed="hasSelectedStudentStatus(item, status.id) ? 'true': 'false'"><span>{{ status.code }}</span></button>
                        </div>
                    </template>
                    <template #foot(period-entry)>
                        <button class="btn-remove" @click="removeSelectedPeriod" :disabled="toRemovePeriod === selectedPeriod">{{ $t('remove-period') }}</button>
                    </template>
                </b-table>
                <div v-if="!creatingNew" class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
            </div>
            <div v-if="canEditPresence && pageLoaded" class="pagination-container u-flex u-justify-content-end">
                <b-pagination v-model="pagination.currentPage" :total-rows="pagination.total" :per-page="pagination.perPage"
                              aria-controls="data-table"></b-pagination>
                <ul class="pagination">
                    <li class="page-item active"><a class="page-link">{{ $t('total') }} {{ pagination.total }}</a></li>
                </ul>
            </div>
            <div v-if="errorData" class="alert alert-danger m-errors">
                <span v-if="errorData.code === 500">{{ errorData.message }}</span>
                <span v-else-if="!!errorData.type">{{ $t(`error-${errorData.type}`) }}</span>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
import {Presence, PresencePeriod, PresenceStatus, PresenceStatusDefault} from '../types';
import APIConfig from '../connect/APIConfig';
import Connector, {ConnectorErrorListener} from '../connect/Connector';
import LegendItem from './entry/LegendItem.vue';
import SearchBar from './entry/SearchBar.vue';
import DynamicFieldKey from './entry/DynamicFieldKey.vue';
import OnOffSwitch from './OnOffSwitch.vue';

@Component({
    name: 'entry',
    components: {OnOffSwitch, SearchBar, LegendItem, DynamicFieldKey}
})
export default class Entry extends Vue {
    statusDefaults: PresenceStatusDefault[] = [];
    presence: Presence | null = null;
    connector: Connector | null = null;
    periods: PresencePeriod[] = [];
    selectedPeriod: PresencePeriod | null = null;
    toRemovePeriod: PresencePeriod | null = null;
    students: any[] = [];
    creatingNew = false;
    createdId: number | null = null;
    pageLoaded = false;
    errorData: string|null = null;
    checkoutMode = false;

    sortBy = 'lastname';
    sortDesc = false;
    pagination = {
        currentPage: 1,
        perPage: 20,
        total: 0
    };
    searchOptions = {
        globalSearchQuery: ''
    };
    requestCount = true;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Number, default: 0}) readonly loadIndex!: number;
    @Prop({type: Boolean, default: false}) readonly canEditPresence!: boolean;

    async load(): Promise<void> {
        const presenceData : any = await this.connector?.loadPresence();
        if (presenceData) {
            this.statusDefaults = presenceData['status-defaults'];
            this.presence = presenceData.presence;
        }
    }

    get globalSearchQuery() {
        return this.searchOptions.globalSearchQuery;
    }

    set globalSearchQuery(query: string) {
        this.searchOptions.globalSearchQuery = query;
    }

    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }

    get selectedPeriodLabel(): string {
        return this.selectedPeriod?.label || '';
    }

    set selectedPeriodLabel(label: string) {
        if (!this.selectedPeriod) { return; }
        this.errorData = null;
        this.selectedPeriod.label = label;
        this.connector?.updatePresencePeriod(this.selectedPeriod.id, label);
    }

    toggleCheckout(student: any) {
        if (!this.selectedPeriod) { return; }
        const periodId = this.selectedPeriod.id;
        if (!student[`period#${periodId}-checked_in_date`]) { return; }
        this.connector?.togglePresenceEntryCheckout(periodId, student.id, (data: any) => {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }

    getPlaceHolder(periodId: number) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }

    get isSaving() {
        return this.connector?.isSaving || false;
    }

    setSelectedPeriod(id: number) {
        if (!this.canEditPresence) { return; }
        const selectedPeriod = this.periods.find((p: any) => p.id === id) || null;
        this.selectedPeriod = selectedPeriod || null;
        this.checkoutMode = false;
    }

    removeSelectedPeriod() {
        if (!this.selectedPeriod) { return; }
        this.errorData = null;
        this.toRemovePeriod = this.selectedPeriod;
        const index = this.periods.indexOf(this.selectedPeriod);
        this.connector?.deletePresencePeriod(this.selectedPeriod.id, (data: any) => {
            this.toRemovePeriod = null;
            if (data?.status === 'ok') {
                this.selectedPeriod = null;
                this.periods.splice(index, 1);
            }
        })
    }

    setError(data: any) : void {
        this.errorData = data;
    }

    get dynamicFieldKeys(): any {
        return this.periods.map((period: any) => ({key: `period#${period.id}`, id: period.id}));
    }

    async itemsProvider(ctx: any) {
        const selectedPeriod = this.selectedPeriod;
        const parameters = {
            global_search_query: ctx.filter,
            sort_field: ctx.sortBy,
            sort_direction: ctx.sortDesc ? 'desc' : 'asc',
            items_per_page: ctx.perPage,
            page_number: ctx.currentPage,
            request_count: this.requestCount
        };
        const data = await this.connector?.loadPresenceEntries(parameters);
        const {periods, students} = data;
        this.periods = periods;
        this.students = students;
        if (data.count !== undefined) {
            this.pagination.total = data.count;
            this.requestCount = false;
        }
        if (!this.pageLoaded && this.periods.length) {
            this.setSelectedPeriod(this.periods[this.periods.length - 1].id);
            this.pageLoaded = true;
        } else if (this.createdId !== null) {
            this.setSelectedPeriod(this.createdId);
            this.createdId = null;
            this.creatingNew = false;
        } else if (selectedPeriod) {
            this.setSelectedPeriod(selectedPeriod.id);
        }
        return students;
    }

    onFilterChanged() {
        this.requestCount = true;
    }

    onFilterCleared() {
        if (this.globalSearchQuery !== '') {
            this.globalSearchQuery = '';
            this.requestCount = true;
        }
    }

    getStudentStatusForPeriod(student: any, periodId: number) {
        return student[`period#${periodId}-status`];
    }

    hasSelectedStudentStatus(student: any, status: number) {
        if (!this.selectedPeriod) { return false; }
        return this.getStudentStatusForPeriod(student, this.selectedPeriod.id) === status;
    }

    async createResultPeriod() {
        this.selectedPeriod = null;
        this.creatingNew = true;
        this.errorData = null;
        await this.connector?.createResultPeriod((data: any) => {
            if (data?.status === 'ok') {
                this.createdId = data.id;
                (this.$refs.table as any).refresh();
            }
        });
    }

    async setSelectedStudentStatus(student: any, status: number) {
        if (!this.selectedPeriod) { return; }
        const selectedPeriod = this.selectedPeriod;
        this.errorData = null;
        const periodId = selectedPeriod.id;
        student[`period#${periodId}-status`] = status;
        this.connector?.savePresenceEntry(periodId, student.id, status, function(data: any) {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }

    getPresenceStatusTitle(status: PresenceStatus): string {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    getPresenceStatus(statusId: number): PresenceStatus | undefined {
        return this.presenceStatuses.find(status => status.id === statusId);
    }

    getStatusCodeForStudent(student: any, periodId: number|undefined = undefined): string {
        if (periodId === undefined) {
            if (!this.selectedPeriod) { return ''; }
            periodId = this.selectedPeriod.id;
        }
        return this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId))?.code || '';
    }

    getStatusColorForStudent(student: any, periodId: number|undefined = undefined): string {
        if (periodId === undefined) {
            if (!this.selectedPeriod) { return ''; }
            periodId = this.selectedPeriod.id;
        }
        return this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId))?.color || '';
    }

    getStatusTitleForStudent(student: any, periodId: number|undefined = undefined): string {
        if (periodId === undefined) {
            if (!this.selectedPeriod) { return ''; }
            periodId = this.selectedPeriod.id;
        }
        const status = this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId));
        return status ? this.getPresenceStatusTitle(status) : '';
    }

    get fields() {
        const periods = this.periods.map(period => {
            const key = this.canEditPresence && period === this.selectedPeriod ? 'period-entry' : `period#${period.id}`;
            const variant = this.canEditPresence && period === this.selectedPeriod ? 'period' : 'result';
            return {key, sortable: false, label: period.label, variant};
        });
        periods.reverse();
        return [
            this.canEditPresence ? {key: 'photo', sortable: false, label: '', variant: 'photo'} : null,
            {key: 'fullname', sortable: false, label: 'Student'},
            {key: 'official_code', sortable: false},
            this.canEditPresence && !this.creatingNew ? {key: 'new_period', sortable: false, label: ''} : null,
            this.creatingNew ? {key: 'period-entry-plh', sortable: false, variant: 'period'} : null,
            ...periods
        ];
    }

    getSortStatus(name: string) {
        if (this.sortBy !== name) { return 'none'; }
        return this.sortDesc ? 'descending' : 'ascending';
    }

    sortByNameField(namefield: string) {
        if (this.sortBy === namefield) {
            this.sortDesc = !this.sortDesc;
            return;
        }
        this.sortBy = namefield;
        this.sortDesc = false;
    }

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this as ConnectorErrorListener);
        this.load();
    }

    @Watch('loadIndex')
    _loadIndex() {
        this.load();
    }
}
</script>

<style scoped>
.m-controls {
    margin-bottom: 15px;
}
.m-legend {
    margin: 20px 8px 15px;
}
.m-errors {
    margin: 10px 0 0 0;
    max-width: 85px;
}
.bd-selected-period {
    border: 1px double #8ea4b3;
}
.spin {
    min-width: 13px;
}
.ti-label {
    height: 30px;
    padding: 6px;
}
.ti-label.mod-border {
    border-color: #e9eaea;
}
</style>
