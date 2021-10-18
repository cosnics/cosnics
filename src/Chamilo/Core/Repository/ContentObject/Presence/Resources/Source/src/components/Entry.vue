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
        "without-status": "Without status"
    },
    "nl": {
        "total": "Totaal",
        "error-LoggedOut": "Het lijkt erop dat je uitgelogt bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "export": "Exporteer",
        "legend": "Legende",
        "students-not-in-course": "Studenten niet in cursus",
        "without-status": "Zonder status"
    }
}
</i18n>

<template>
    <div>
        <div v-if="canEditPresence" class="u-flex u-align-items-center u-gap-small-3x m-controls"><!-- u-max-w-fit -->
            <search-bar :search-options="searchOptions" @filter-changed="onFilterChanged" @filter-cleared="onFilterCleared" />
            <div v-if="!!selectedPeriod" class="status-filters u-flex u-gap-small u-align-items-baseline">
                <span style="color: #666; margin-right: 5px;width:max-content"><i class="fa fa-filter" style="margin-right: 2px"></i>Filters:</span>
                <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code mod-selectable"
                        :class="[status.color, {'is-selected': statusFilters.indexOf(status) !== -1}]"
                        :aria-pressed="statusFilters.indexOf(status) !== -1 ? 'true': 'false'"
                        @click="toggleStatusFilters(status)"
                        :title="getPresenceStatusTitle(status)"><span>{{ status.code }}</span></button>
                <button class="color-code mod-selectable grey-100" :class="{'is-selected': withoutStatusSelected }"
                        :aria-pressed="withoutStatusSelected ? 'true' : 'false'"
                        @click="toggleWithoutStatus"><span>{{ $t('without-status') }}</span></button>
            </div>
            <div v-else>
                <a :href="apiConfig.exportURL" class="btn btn-default btn-sm">{{ $t('export') }}</a>
            </div>
        </div>
        <div>
            <div class="u-relative">
                <div v-if="!canEditPresence" class="u-flex u-align-items-baseline u-flex-wrap u-gap-small-3x m-legend">
                    <span style="color: #507177">{{ $t('legend') }}:</span>
                    <legend-item v-for="status in presenceStatuses" :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color" />
                </div>
                <entry-table id="course-students" :items="itemsProvider" :periods="periods"
                             :status-defaults="statusDefaults" :presence="presence" :can-edit-presence="canEditPresence"
                             :global-search-query="globalSearchQuery" :pagination="pagination" :is-saving="isSaving"
                             :is-creating-new-period="creatingNew" :to-remove-period="toRemovePeriod"
                             @create-period="createResultPeriod"
                             @remove-selected-period="removeSelectedPeriod"
                             @period-label-changed="setSelectedPeriodLabel"
                             @select-student-status="setSelectedStudentStatus"
                             @toggle-checkout="toggleCheckout"
                             @period-change="onPeriodChanged" />
                <div v-if="!creatingNew" class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
            </div>
            <div v-if="canEditPresence && pageLoaded && pagination.total > 0" class="pagination-container u-flex u-justify-content-end">
                <b-pagination v-if="!changeAfterStatusFilters" v-model="pagination.currentPage" :total-rows="pagination.total" :per-page="pagination.perPage"
                              aria-controls="data-table"></b-pagination>
                <ul class="pagination">
                    <li class="page-item active">
                        <a class="page-link" v-if="!changeAfterStatusFilters">{{ $t('total') }} {{ pagination.total }}</a>
                        <a class="page-link" v-else @click="refreshFilters" style="cursor: pointer">Refresh</a>
                    </li>
                </ul>
            </div>
            <div v-if="errorData" class="alert alert-danger m-errors">
                <span v-if="errorData.code === 500">{{ errorData.message }}</span>
                <span v-else-if="!!errorData.type">{{ $t(`error-${errorData.type}`) }}</span>
            </div>
            <b v-if="nonCourseStudents.length" style="color: #507177;font-size: 14px;font-weight: 500;">{{ $t('students-not-in-course') }}</b>
            <entry-table v-if="nonCourseStudents.length" id="non-course-students" style="margin-top: 20px" :items="nonCourseStudents" :periods="periods"
                         :status-defaults="statusDefaults" :presence="presence" :can-edit-presence="canEditPresence" :has-non-course-students="true"
                         :is-saving="isSavingNonCourse" :is-creating-new-period="creatingNew"
                         @select-student-status="setSelectedStudentStatus" @toggle-checkout="toggleCheckout" />
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
import EntryTable from './entry/EntryTable.vue';
import OnOffSwitch from './OnOffSwitch.vue';

@Component({
    name: 'entry',
    components: {EntryTable, OnOffSwitch, SearchBar, LegendItem, DynamicFieldKey}
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

    pagination = {
        currentPage: 1,
        perPage: 3,
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
        this.$emit('filters-changed');
    }

    refreshFilters() {
        this.requestCount = true;
        this.$emit('filters-changed');
    }

    toggleWithoutStatus() {
        this.withoutStatusSelected = !this.withoutStatusSelected;
        this.requestCount = true;
        this.$emit('filters-changed');
    }

    onPeriodChanged(period: PresencePeriod|null) {
        this.selectedPeriod = period;
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
        if (!this.pageLoaded && this.periods.length) {
            this.$emit('selected-period', this.periods[this.periods.length - 1].id);
            this.pageLoaded = true;
        } else if (this.createdId !== null) {
            this.$emit('selected-period', this.createdId);
            this.createdId = null;
            this.creatingNew = false;
        } else {
            this.$emit('selected-period-maybe');
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
        this.$emit('creating-new-period');
        this.creatingNew = true;
        this.errorData = null;
        await this.connector?.createResultPeriod((data: any) => {
            if (data?.status === 'ok') {
                this.createdId = data.id;
                if (callback) {
                    callback(this.createdId);
                }
            }
        });
    }

    removeSelectedPeriod(selectedPeriod: PresencePeriod) {
        if (!this.canEditPresence) { return; }
        this.errorData = null;
        this.toRemovePeriod = selectedPeriod;
        const index = this.periods.indexOf(selectedPeriod);
        this.connector?.deletePresencePeriod(selectedPeriod.id, (data: any) => {
            this.toRemovePeriod = null;
            if (data?.status === 'ok') {
                this.$emit('period-removed', selectedPeriod.id);
                this.periods.splice(index, 1);
            }
        })
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
</style>
