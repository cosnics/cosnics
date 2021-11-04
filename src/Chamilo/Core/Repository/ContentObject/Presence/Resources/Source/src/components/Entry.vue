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
        "refresh": "Refresh",
        "changes-filters": "You have made changes so that the shown results possibly no longer reflect the chosen filter criteria. Choose different criteria or click refresh to remedy.",
        "remove-period": "Remove period",
        "show-periods": "Show all periods",
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
        "refresh": "Vernieuwen",
        "changes-filters": "Je hebt een wijziging gedaan waardoor de getoonde resultaten mogelijk niet meer overeenkomen met de gekozen filtercriteria. Kies andere criteria of klik op Vernieuwen om dit op te lossen.",
        "remove-period": "Verwijder periode",
        "show-periods": "Toon alle perioden",
        "more": "Meer"
    }
}
</i18n>

<template>
    <div>
        <div v-if="canEditPresence" class="u-flex u-gap-small-3x m-controls" :class="[selectedPeriod ? 'u-align-items-start': 'u-align-items-center']"><!-- u-max-w-fit -->
            <search-bar :search-options="searchOptions" @filter-changed="onFilterChanged" @filter-cleared="onFilterCleared" />
            <div v-if="canEditPresence && !!selectedPeriod" class="status-filters u-flex u-gap-small u-align-items-baseline">
                <span style="color: #666; margin-right: 5px;width:max-content"><i class="fa fa-filter" style="margin-right: 2px"></i>Filters:</span>
                <filter-status-button v-for="(status, index) in presenceStatuses" :key="`status-${index}`"
                                      :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color"
                                      :is-selected="statusFilters.indexOf(status) !== -1"
                                      @toggle-filter="toggleStatusFilters(status)"/>
                <filter-status-button :label="$t('without-status')" color="grey-100" :is-selected="withoutStatusSelected" @toggle-filter="toggleWithoutStatus"/>
            </div>
            <template v-else>
                <b-form-select v-model="statMode" :options="statOptions" class="form-control" style="padding: 5px;width: initial">
                </b-form-select>
                <a :href="apiConfig.exportURL" class="btn btn-default btn-sm">{{ $t('export') }}</a>
            </template>
        </div>

        <div class="u-flex" style="gap: 8px">
            <div style="width: max-content">
                <div class="u-relative">
                    <div v-if="!canEditPresence" class="u-flex u-align-items-baseline u-flex-wrap u-gap-small-3x m-legend">
                        <span style="color: #507177">{{ $t('legend') }}:</span>
                        <legend-item v-for="status in presenceStatuses" :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color" />
                    </div>
                    <entry-table id="course-students" :items="itemsProvider" :periods="periods"
                                 :status-defaults="statusDefaults" :presence="presence" :selected-period="selectedPeriod" :can-edit-presence="canEditPresence"
                                 :global-search-query="globalSearchQuery" :pagination="pagination" :is-saving="isSaving" :checkout-mode="checkoutMode" :foot-clone="canEditPresence && !!selectedPeriod"
                                 :is-creating-new-period="creatingNew" :stat-mode="statMode" :statistics="statistics" :style="selectedPeriod ? 'margin-top: 3px' : ''"
                                 @create-period="createResultPeriod"
                                 @period-label-changed="setSelectedPeriodLabel"
                                 @select-student-status="setSelectedStudentStatus"
                                 @toggle-checkout-mode="checkoutMode = !checkoutMode"
                                 @toggle-checkout="toggleCheckout"
                                 @change-selected-period="setSelectedPeriod">
                        <template v-slot:entry-top v-if="canEditPresence">
                            <div class="extra-actions u-flex u-align-items-baseline" style="gap: 15px;min-width:100%;top:-26px;justify-content: space-between">
                                <a class="show-periods-btn" @click="setSelectedPeriod(null)">{{ $t('show-periods') }}</a>
                                <a id="show-more" @click="showMore = !showMore" class="btn btn-default btn-sm" style="padding: 1px 4px 0 6px;margin-right:8px">{{ $t('more') }}&hellip;</a>
                                <bulk-status-popup :is-visible="showMore" :presence-statuses="presenceStatuses"
                                                   @apply="applyBulkStatus" @cancel="cancelBulkStatus"/>
                            </div>
                        </template>
                        <template v-slot:entry-foot v-if="canEditPresence && !!selectedPeriod">
                            <button class="btn-remove" @click="removeSelectedPeriod" :disabled="toRemovePeriod === selectedPeriod">{{ $t('remove-period') }}</button>
                        </template>
                    </entry-table>
                    <div v-if="!creatingNew" class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
                </div>
                <div v-if="canEditPresence && pageLoaded && pagination.total > 0 && (statMode === 0 || statMode === 1)" class="pagination-container u-flex u-justify-content-end" :style="!!selectedPeriod ? 'margin-top: -20px': ''">
                    <b-pagination v-model="pagination.currentPage" :total-rows="pagination.total" :per-page="pagination.perPage"
                                  aria-controls="course-students" :disabled="changeAfterStatusFilters"></b-pagination>
                    <ul class="pagination">
                        <li class="page-item" :class="{active: !changeAfterStatusFilters, disabled: changeAfterStatusFilters}">
                            <a class="page-link" :style="changeAfterStatusFilters ? 'text-decoration: line-through' : ''">{{ $t('total') }} {{ pagination.total }}</a>
                        </li>
                        <li v-if="changeAfterStatusFilters" class="page-item active">
                            <a class="page-link" v-b-popover.hover.right="$t('changes-filters')" @click="refreshFilters" style="cursor: pointer">{{ $t('refresh') }} <i class="fa fa-info-circle"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div v-if="errorData" class="alert alert-danger m-errors">
            <span v-if="errorData.code === 500">{{ errorData.message }}</span>
            <span v-else-if="!!errorData.type">{{ $t('error-' + errorData.type) }}</span>
        </div>
        <template v-if="nonCourseStudents.length && (statMode === 0 || statMode === 1)">
            <h4 style="color: #507177;font-size: 14px;font-weight: 500;margin-top:-5px;margin-bottom:-10px">{{ $t('students-not-in-course') }}</h4>
            <entry-table id="non-course-students" style="margin-top: 20px" :items="nonCourseStudents" :selected-period="selectedPeriod" :periods="periods"
                         :status-defaults="statusDefaults" :presence="presence" :can-edit-presence="canEditPresence" :has-non-course-students="true"
                         :is-saving="isSavingNonCourse" :is-creating-new-period="creatingNew" :checkout-mode="checkoutMode" :stat-mode="statMode"
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

@Component({
    name: 'entry',
    components: {EntryTable, OnOffSwitch, FilterStatusButton, SearchBar, LegendItem, DynamicFieldKey, BulkStatusPopup}
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
    statMode = 0;
    statistics: any[] = [];

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
    @Prop({type: Boolean, default: false}) readonly canEditPresence!: boolean;

    get globalSearchQuery() {
        return this.searchOptions.globalSearchQuery;
    }

    set globalSearchQuery(query: string) {
        this.searchOptions.globalSearchQuery = query;
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

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    applyBulkStatus(status: PresenceStatus) {
        this.showMore = false;
        if (!this.canEditPresence || !this.selectedPeriod) { return; }
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
        if (!!this.selectedPeriod && (this.statusFilters.length || this.withoutStatusSelected)) {
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
            //this.$emit('selected-period', this.periods[this.periods.length - 1].id);
            this.pageLoaded = true;
        } else if (this.createdId !== null) {
            this.setSelectedPeriod(this.createdId);
            //this.$emit('selected-period', this.createdId);
            this.createdId = null;
            this.creatingNew = false;
        } else if (selectedPeriod) {
            this.setSelectedPeriod(selectedPeriod.id);
            //this.$emit('selected-period-maybe');
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
    }

    async createResultPeriod(callback: Function|undefined = undefined) {
        if (!this.canEditPresence) { return; }
        this.selectedPeriod = null;
        this.creatingNew = true;
        this.errorData = null;
        this.checkoutMode = false;
        await this.connector?.createResultPeriod((data: any) => {
            if (data?.status === 'ok') {
                this.createdId = data.id;
                if (callback) {
                    callback(this.createdId);
                }
            }
        });
    }

    removeSelectedPeriod() {
        if (!this.canEditPresence || !this.selectedPeriod) { return; }
        this.errorData = null;
        const selectedPeriod = this.selectedPeriod;
        this.toRemovePeriod = selectedPeriod;
        const index = this.periods.indexOf(selectedPeriod);
        this.connector?.deletePresencePeriod(selectedPeriod.id, (data: any) => {
            this.toRemovePeriod = null;
            if (data?.status === 'ok') {
                this.selectedPeriod = null;
                this.periods.splice(index, 1);
            }
        })
    }

    setSelectedPeriod(periodId: number|null) {
        if (!this.canEditPresence) { return; }
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
        if (!this.canEditPresence) { return; }
        this.errorData = null;
        selectedPeriod.label = label;
        this.connector?.updatePresencePeriod(selectedPeriod.id, label);
    }

    async setSelectedStudentStatus(student: any, selectedPeriod: PresencePeriod, status: number, hasNonCourseStudents = false) {
        if (!this.canEditPresence) { return; }
        this.errorData = null;
        const periodId = selectedPeriod.id;
        student[`period#${periodId}-status`] = status;
        if (!hasNonCourseStudents && (this.statusFilters.length || this.withoutStatusSelected)) {
            this.changeAfterStatusFilters = true;
        }
        const connector = hasNonCourseStudents ? this.connectorNonCourse : this.connector;
        connector?.savePresenceEntry(periodId, student.id, status, function(data: any) {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }

    toggleCheckout(student: any, selectedPeriod: PresencePeriod, hasNonCourseStudents = false) {
        if (!this.canEditPresence) { return; }
        const periodId = selectedPeriod.id;
        if (!student[`period#${periodId}-checked_in_date`]) { return; }
        const connector = hasNonCourseStudents ? this.connectorNonCourse : this.connector;
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
        if (this.statMode === 2 || this.statMode === 3) {
            const data = await this.connector?.loadStatistics() || null;
            this.statistics = data?.statistics || [];
        }
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
    margin: 10px 0;
    max-width: 85ch;
}
.show-periods-btn {
    cursor: pointer;
    font-weight: 500;
    min-width: -moz-fit-content;
    min-width: fit-content;
    text-decoration: none;
}
</style>
