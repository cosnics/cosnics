<i18n>
{
    "en": {
        "total": "Total",
        "error-Timeout": "The server took too long to respond. Your changes have possibly not been saved. You can try again later.",
        "error-LoggedOut": "It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.",
        "error-Unknown": "An unknown error occurred. Your changes have possibly not been saved. You can try again later.",
        "export": "Export",
        "legend": "Legend",
        "students-not-in-course": "Students not in course",
        "without-status": "Without status",
        "checkout-mode": "Checkout mode",
        "show-all-periods": "Show all periods",
        "new-period": "New period",
        "refresh": "Refresh",
        "changes-filters": "You have made changes so that the shown results possibly no longer reflect the chosen filter criteria. Choose different criteria or click refresh to remedy.",
        "remove-period": "Remove period",
        "more": "More"
    },
    "nl": {
        "total": "Totaal",
        "error-LoggedOut": "Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "export": "Exporteer",
        "legend": "Legende",
        "students-not-in-course": "Studenten niet in cursus",
        "without-status": "Zonder status",
        "checkout-mode": "Uitcheckmodus",
        "show-all-periods": "Toon alle perioden",
        "new-period": "Nieuwe periode",
        "refresh": "Vernieuwen",
        "changes-filters": "Je hebt een wijziging gedaan waardoor de getoonde resultaten mogelijk niet meer overeenkomen met de gekozen filtercriteria. Kies andere criteria of klik op Vernieuwen om dit op te lossen.",
        "remove-period": "Verwijder periode",
        "more": "Meer"
    }
}
</i18n>

<template>
    <div>
        <div class="u-flex u-gap-small-3x u-align-items-center" :class="[{'m-controls': defaultTableShown}]">
            <search-bar v-if="defaultTableShown" :search-options="searchOptions" @filter-changed="onFilterChanged" @filter-cleared="onFilterCleared" />
            <div v-if="statusFiltersShown" class="status-filters u-flex u-gap-small u-align-items-baseline">
                <span class="lbl-filters"><i class="fa fa-filter"></i>Filters:</span>
                <filter-status-button v-for="(status, index) in presenceStatuses" :key="`status-${index}`"
                                      :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color"
                                      :is-selected="statusFilters.indexOf(status) !== -1"
                                      @toggle-filter="toggleStatusFilters(status)"/>
                <filter-status-button :label="$t('without-status')" color="grey-100" :is-selected="withoutStatusSelected" @toggle-filter="toggleWithoutStatus"/>
            </div>
            <template v-else-if="!useStatistics">
                <a :href="apiConfig.exportURL" class="btn btn-default btn-sm mod-export">{{ $t('export') }}</a>
                <button class="btn btn-default btn-sm mod-create-period" @click="createResultPeriod"><i aria-hidden="true" class="fa fa-plus"></i>{{ $t('new-period') }}</button>
            </template>
        </div>
        <div class="u-flex">
            <div class="w-max-content">
                <div class="u-relative">
                    <entry-table v-show="defaultTableShown" id="course-students" :items="itemsProvider" :periods="periods"
                                 :status-defaults="statusDefaults" :presence="presence" :selected-period="selectedPeriod"
                                 :global-search-query="globalSearchQuery" :pagination="pagination" :is-saving="isSaving" :checkout-mode="checkoutMode"
                                 :is-creating-new-period="creatingNew" :statistics="statistics" :use-statistics="useStatistics"
                                 @create-period="createResultPeriod"
                                 @period-label-changed="setSelectedPeriodLabel"
                                 @select-student-status="setSelectedStudentStatus"
                                 @toggle-checkout-mode="checkoutMode = !checkoutMode"
                                 @toggle-checkout="toggleCheckout"
                                 @change-selected-period="setSelectedPeriod">
                        <template v-slot:slot-top v-if="hasSelectedPeriod">
                            <div class="u-flex u-align-items-baseline u-justify-content-space-between u-gap-small-2x minw-100">
                                <button class="btn btn-sm mod-period-action mod-show-periods" @click="setSelectedPeriod(null)">{{ $t('show-all-periods') }}</button>
                                <button id="show-more" @click="showMore = !showMore" class="btn btn-default btn-sm mod-more">{{ $t('more') }}&hellip;</button>
                                <bulk-status-popup target="show-more" :is-visible="showMore" :presence-statuses="presenceStatuses" :status-defaults="statusDefaults"
                                                   :print-qr-code-url="`${apiConfig.printQrCodeURL}&presence_period_id=${selectedPeriod.id}`" @apply="applyBulkStatus" @cancel="cancelBulkStatus"/>
                            </div>
                        </template>
                        <template v-slot:slot-bottom v-if="hasSelectedPeriod">
                            <button class="btn btn-sm mod-period-action mod-remove-period" @click="removeSelectedPeriod" :disabled="toRemovePeriod === selectedPeriod">{{ $t('remove-period') }}</button>
                        </template>
                    </entry-table>
                    <periods-stats-table v-if="periodStatsShown" id="course-students" :periods="periods" :is-busy="loadingStatistics"
                                         :status-defaults="statusDefaults" :presence="presence"
                                         :statistics="statistics"></periods-stats-table>
                    <div v-if="!creatingNew" class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
                </div>
                <div v-if="paginationShown" class="pagination-container u-flex u-justify-content-end">
                    <b-pagination v-model="pagination.currentPage" :total-rows="pagination.total" :per-page="pagination.perPage"
                                  aria-controls="course-students" :disabled="changeAfterStatusFilters"></b-pagination>
                    <ul class="pagination">
                        <li class="page-item" :class="{active: !changeAfterStatusFilters, disabled: changeAfterStatusFilters}">
                            <a class="page-link" :class="{'u-text-line-through': changeAfterStatusFilters}">{{ $t('total') }} {{ pagination.total }}</a>
                        </li>
                        <li v-if="changeAfterStatusFilters" class="page-item active">
                            <a class="page-link u-cursor-pointer" v-b-popover.hover.right="$t('changes-filters')" @click="refreshFilters">{{ $t('refresh') }} <i class="fa fa-info-circle"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <error-display v-if="errorData" @close="errorData = null">
            <span v-if="errorData.code === 500">{{ errorData.message }}</span>
            <span v-else-if="!!errorData.type">{{ $t('error-' + errorData.type) }}</span>
        </error-display>
        <template v-if="nonCourseStudentsShown">
            <h4 class="u-font-medium h-not-in-course">{{ $t('students-not-in-course') }}</h4>
            <entry-table id="non-course-students" :items="nonCourseStudents" :selected-period="selectedPeriod" :periods="periods"
                         :status-defaults="statusDefaults" :presence="presence" :is-fully-editable="false"
                         :is-saving="isSavingNonCourse" :is-creating-new-period="creatingNew" :checkout-mode="checkoutMode" :use-statistics="useStatistics"
                         @select-student-status="setSelectedStudentStatus" @toggle-checkout="toggleCheckout" />
        </template>
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
import EntryTable from './entry/EntryTable.vue';
import FilterStatusButton from './entry/FilterStatusButton.vue';
import OnOffSwitch from './OnOffSwitch.vue';
import BulkStatusPopup from './entry/BulkStatusPopup.vue';
import PeriodsStatsTable from './entry/PeriodsStatsTable.vue';
import ErrorDisplay from './ErrorDisplay.vue';

@Component({
    name: 'entry',
    components: {PeriodsStatsTable, EntryTable, OnOffSwitch, FilterStatusButton, SearchBar, LegendItem, DynamicFieldKey, BulkStatusPopup, ErrorDisplay}
})
export default class Entry extends Vue {
    statusDefaults: PresenceStatusDefault[] = [];
    presence: Presence | null = null;
    connector: Connector | null = null;
    connectorNonCourse: Connector | null = null;
    periods: PresencePeriod[] = [];
    toRemovePeriod: PresencePeriod | null = null;
    students: any[] = [];
    nonCourseStudents: any[] = [];
    creatingNew = false;
    createdId: number | null = null;
    pageLoaded = false;
    errorData: string|null = null;
    statusFilters: PresenceStatus[] = [];
    withoutStatusSelected = false;
    checkoutMode = false;
    showMore = false;
    statistics: any[] = [];
    loadingStatistics = false;

    statOptions = [
        {text: 'Geen statistiek', value: 0},
        {text: 'Student/Status', value: 1},
        {text: 'Status/Periode', value: 2}
    ];

    pagination = {
        currentPage: 1,
        perPage: 15,
        total: 0
    };

    searchOptions = {
        globalSearchQuery: ''
    };
    requestCount = true;
    requestNonCourseStudents = true;
    selectedPeriod: PresencePeriod|null = null;
    changeAfterStatusFilters = false;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Number, default: 0}) readonly loadIndex!: number;
    @Prop({type: Boolean, default: false}) readonly useStatistics!: boolean;
    @Prop({type: String, default: ''}) readonly statMode!: string;

    get globalSearchQuery() {
        return this.searchOptions.globalSearchQuery;
    }

    set globalSearchQuery(query: string) {
        this.searchOptions.globalSearchQuery = query;
    }

    get defaultTableShown() {
        return !this.useStatistics || (this.useStatistics && this.statMode === 'student');
    }

    get nonCourseStudentsShown() {
        return this.defaultTableShown && this.nonCourseStudents.length;
    }

    get statusFiltersShown() {
        return this.hasSelectedPeriod && !this.useStatistics;
    }

    get paginationShown() {
        return this.defaultTableShown && this.pageLoaded && this.pagination.total > 0;
    }

    get periodStatsShown() {
        return this.useStatistics && this.statMode === 'period';
    }

    get hasSelectedPeriod() {
        return !!this.selectedPeriod;
    }

    toggleStatusFilters(status: PresenceStatus) {
        const statusFilters = this.statusFilters;
        const index = statusFilters.indexOf(status);
        if (index === -1) {
            this.statusFilters.push(status);
        } else {
            this.statusFilters = statusFilters.slice(0, index).concat(statusFilters.slice(index + 1));
        }
        this.requestCount = true;
        this.$emit('refresh');
    }

    refreshFilters() {
        this.requestCount = true;
        this.$emit('refresh');
    }

    toggleWithoutStatus() {
        this.withoutStatusSelected = !this.withoutStatusSelected;
        this.requestCount = true;
        this.$emit('refresh');
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

    getPresenceStatusTitle(status: PresenceStatus): string {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }

    getPlaceHolder(periodId: number) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    applyBulkStatus(status: PresenceStatus) {
        this.showMore = false;
        if (!this.selectedPeriod) { return; }
        this.errorData = null;
        this.connector?.bulkSavePresenceEntries(this.selectedPeriod.id, status.id, (data: any) => {
            if (data?.status === 'ok') {
                this.$emit('refresh');
            }
        });
    }

    cancelBulkStatus() {
        this.showMore = false;
    }

    async load(): Promise<void> {
        const presenceData : any = await this.connector?.loadPresence();
        if (presenceData) {
            this.statusDefaults = presenceData['status-defaults'];
            this.presence = presenceData.presence;
        }
    }

    async itemsProvider(ctx: any) {
        const parameters: any = {
            global_search_query: ctx.filter,
            sort_field: ctx.sortBy,
            sort_direction: ctx.sortDesc ? 'desc' : 'asc',
            items_per_page: ctx.perPage,
            page_number: ctx.currentPage,
            request_count: this.requestCount,
            request_non_course_students: this.requestNonCourseStudents
        };
        if (this.selectedPeriod && (this.statusFilters.length || this.withoutStatusSelected)) {
            parameters['period_id'] = this.selectedPeriod.id;
            parameters['status_filters'] = this.statusFilters.map(status => status.id);
            parameters['without_status'] = this.withoutStatusSelected;
        }
        const data = await this.connector?.loadPresenceEntries(parameters);
        this.changeAfterStatusFilters = false;
        const {periods, students} = data;
        this.periods = periods;
        this.students = students;
        if (data.count !== undefined) {
            this.pagination.total = data.count;
            this.requestCount = false;
        }
        if (this.requestNonCourseStudents) {
            if (data['non_course_students'] !== undefined) {
                this.nonCourseStudents = data['non_course_students'];
            }
            this.requestNonCourseStudents = false;
        }
        const selectedPeriod = this.selectedPeriod;
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
        if (!students.length) {
            return [{tableEmpty: true}];
        }
        return students;
    }

    get isSaving() {
        return this.connector?.isSaving || false;
    }

    get isSavingNonCourse() {
        return this.connectorNonCourse?.isSaving || false;
    }

    setError(data: any) : void {
        this.errorData = data;
        console.log(this.errorData);
    }

    async createResultPeriod() {
        this.selectedPeriod = null;
        this.creatingNew = true;
        this.errorData = null;
        this.checkoutMode = false;
        await this.connector?.createResultPeriod((data: any) => {
            if (data?.status === 'ok') {
                this.createdId = data.id;
                this.$emit('refresh');
            }
        });
    }

    removeSelectedPeriod() {
        if (!this.selectedPeriod) { return; }
        this.errorData = null;
        const selectedPeriod = this.selectedPeriod;
        this.toRemovePeriod = selectedPeriod;
        const index = this.periods.indexOf(selectedPeriod);
        this.connector?.deletePresencePeriod(selectedPeriod.id, (data: any) => {
            this.toRemovePeriod = null;
            if (data?.status === 'ok') {
                this.periods.splice(index, 1);
                this.setSelectedPeriod(null);
            }
        })
    }

    setSelectedPeriod(periodId: number|null) {
        if (periodId === null) {
            this.selectedPeriod = null;
        } else {
            this.selectedPeriod = this.periods.find((p: any) => p.id === periodId) || null;
        }

        const hasFiltersSet = (this.statusFilters.length || this.withoutStatusSelected);
        if (this.selectedPeriod === null && hasFiltersSet) {
            this.statusFilters = [];
            this.withoutStatusSelected = false;
            this.requestCount = true;
            this.$emit('refresh');
        } else if (hasFiltersSet) {
            this.requestCount = true;
            this.$emit('refresh');
        }

        //this.checkoutMode = false;
    }

    setSelectedPeriodLabel(selectedPeriod: PresencePeriod, label: string) {
        this.errorData = null;
        selectedPeriod.label = label;
        this.connector?.updatePresencePeriod(selectedPeriod.id, label);
    }

    async setSelectedStudentStatus(student: any, selectedPeriod: PresencePeriod, status: number, isFullyEditable = true) {
        this.errorData = null;
        const periodId = selectedPeriod.id;
        student[`period#${periodId}-status`] = status;
        if (isFullyEditable && (this.statusFilters.length || this.withoutStatusSelected)) {
            this.changeAfterStatusFilters = true;
        }
        const connector = isFullyEditable ? this.connector : this.connectorNonCourse;
        connector?.savePresenceEntry(periodId, student.id, status, function(data: any) {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }

    toggleCheckout(student: any, selectedPeriod: PresencePeriod, isFullyEditable = true) {
        const periodId = selectedPeriod.id;
        if (!student[`period#${periodId}-checked_in_date`]) { return; }
        const connector = isFullyEditable ? this.connector : this.connectorNonCourse;
        connector?.togglePresenceEntryCheckout(periodId, student.id, (data: any) => {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this as ConnectorErrorListener);
        this.connectorNonCourse = new Connector(this.apiConfig);
        this.connectorNonCourse.addErrorListener(this as ConnectorErrorListener);
        this.load();
    }

    @Watch('loadIndex')
    _loadIndex() {
        this.load();
    }

    @Watch('statMode')
    async _statMode() {
        if (this.statMode === 'period') {
            this.loadingStatistics = true;
            this.statistics = [];
            const data = await this.connector?.loadStatistics() || null;
            this.loadingStatistics = false;
            this.statistics = data?.statistics || [];
        }
    }
}
</script>

<style>
.table.mod-entry {
    width: max-content;
}

.table.mod-entry .table-photo {
    padding: 0;
}

.table.mod-entry .table-period {
    min-width: 136px;
}

@-moz-document url-prefix() {
    .table.mod-entry th.table-period {
        background-clip: padding-box;
    }
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

.color-code.is-selected {
    position: relative;
}

.color-code.mod-selectable, .color-code.mod-plh {
    opacity: .42;
}

.color-code.mod-selectable.is-selected,
.color-code.mod-selectable:hover {
    opacity: 1;
}

.color-code.mod-selectable.mod-off:not(:hover) {
    background-color: #f5f5f5;
    color: #333;
}

.color-code.mod-disabled {
    --color: #f5f5f5;
    --text-color: var(--text-color-dark);
    opacity: .15;
}

.color-code.mod-none {
    --color: #deede1;
    background: transparent linear-gradient(135deg, var(--color) 10%, transparent 10%, transparent 50%, var(--color) 50%, var(--color) 60%, transparent 60%, transparent 100%) 0 0 / 7px 7px;
    border-radius: 5px;
    height: 17px;
}

.color-code.mod-shadow.is-selected {
    box-shadow: 0 0 0 .2rem var(--selected-color);
}

.color-code.mod-shadow-grey:hover {
    box-shadow: 1px 1px 2px -1px #673ab7;
}

.btn.mod-export {
    padding: 3px 6px;
}

.btn.mod-create-period {
    padding: 3px 6px 3px 4px;
}

.btn.mod-create-period > .fa {
    color: #406e8e;
}

.btn.mod-create-period > .fa-plus {
    margin-right: 5px;
}

.btn.mod-more {
    padding: 1px 4px 0 6px;
}

.btn.mod-period-action {
    background: none;
    padding: 1px 4px 0;
}

.btn.mod-period-action:active, .btn.mod-period-action:focus {
    box-shadow: none;
    outline: none;
}

.btn.mod-period-action[disabled] {
    cursor: not-allowed;
}

.btn.mod-period-action:active:focus {
    outline: none;
}

.btn.mod-show-periods {
    border-color: #e1ebf4;
    color: #337ab7;
}

.btn.mod-show-periods:hover {
    border-color: #95acc0;
    color: #23527c;
}

.btn.mod-show-periods:active, .btn.mod-show-periods:focus {
    border-color: #95acc0;
    color: #23527c;
}

.btn.mod-remove-period {
    border-color: #f8dfdf;
    color: #c9605d;
}

.btn.mod-remove-period:hover {
    border-color: #e48380;
    color: #b72d2a;
}

.btn.mod-remove-period:active, .btn.mod-remove-period:focus {
    border-color: #ac2925;
    color: #ac2925;
}
</style>
<style scoped>
.minw-100 {
    min-width: 100%;
}

.w-max-content {
    width: max-content;
}

.m-controls {
    margin-bottom: 13px;
}

.m-errors {
    margin: 10px 0;
    max-width: 85ch;
}

.lbl-filters {
    color: #666;
    margin-right: 5px;
    width: max-content;
}

.lbl-filters > .fa-filter {
    margin-right: 2px;
}

.pagination-container {
    margin-top: -10px;
}

.h-not-in-course {
    color: #507177;
    font-size: 14px;
    margin-top: -5px;
}
</style>
