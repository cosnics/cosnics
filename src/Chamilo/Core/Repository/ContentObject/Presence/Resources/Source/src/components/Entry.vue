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
        "legend": "Legend",
        "students-not-in-course": "Students not in course"
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
        "legend": "Legende",
        "students-not-in-course": "Studenten niet in cursus"
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
                <div v-if="!canEditPresence" class="u-flex u-align-items-baseline u-flex-wrap u-gap-small-3x m-legend">
                    <span style="color: #507177">{{ $t('legend') }}:</span>
                    <legend-item v-for="status in presenceStatuses" :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color" />
                </div>
                <entry-table id="table-1" :items="itemsProvider" :periods="periods" :can-edit-presence="canEditPresence" :is-sortable="canEditPresence"
                     :pagination="pagination" :global-search-query="globalSearchQuery" :presence="presence" :status-defaults="statusDefaults" :selected-period="selectedPeriod"
                     :is-saving="isSaving" :is-creating-new-period="creatingNew" :is-period-label-editable="true"
                     :show-remove-period="canEditPresence && !!selectedPeriod"
                     :is-remove-period-disabled="toRemovePeriod === selectedPeriod" :has-registered-students="true"
                     @select="setSelectedPeriod" @clear-select="selectedPeriod = null" @create-period="createResultPeriod($event)"
                     @select-student-status="setSelectedStudentStatus"
                     @toggle-checkout="toggleCheckout"
                     @period-label-changed="selectedPeriodLabel = $event"
                     @remove-selected-period="removeSelectedPeriod" />
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
            <b v-if="nonRegisteredStudents.length">{{ $t('students-not-in-course') }}</b>
            <entry-table id="table-2" :items="nonRegisteredStudents" :periods="periods" :can-edit-presence="canEditPresence" :is-sortable="false"
                 :pagination="pagination" :presence="presence" :status-defaults="statusDefaults" :selected-period="selectedPeriodNonRegistered"
                 :is-saving="isSaving" :is-creating-new-period="creatingNew" :is-create-period-disabled="true"
                 :show-remove-period="false"
                 style="margin-top: 20px"
                 @select="setSelectedPeriodNonRegistered" @clear-select="selectedPeriodNonRegistered = null"
                 @select-student-status="setSelectedStudentStatus"
                 @toggle-checkout="toggleCheckout" />
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
    periods: PresencePeriod[] = [];
    selectedPeriod: PresencePeriod | null = null;
    selectedPeriodNonRegistered: PresencePeriod | null = null;
    toRemovePeriod: PresencePeriod | null = null;
    students: any[] = [];
    nonRegisteredStudents: any[] = [];
    creatingNew = false;
    createdId: number | null = null;
    pageLoaded = false;
    errorData: string|null = null;

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
    requestNonRegisteredStudents = true;

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

    get selectedPeriodLabel(): string {
        return this.selectedPeriod?.label || '';
    }

    set selectedPeriodLabel(label: string) {
        if (!this.selectedPeriod) { return; }
        this.errorData = null;
        this.selectedPeriod.label = label;
        this.connector?.updatePresencePeriod(this.selectedPeriod.id, label);
    }

    toggleCheckout(student: any, selectedPeriod: PresencePeriod) {
        const periodId = selectedPeriod.id;
        if (!student[`period#${periodId}-checked_in_date`]) { return; }
        this.connector?.togglePresenceEntryCheckout(periodId, student.id, (data: any) => {
            if (data?.status === 'ok') {
                student[`period#${periodId}-checked_in_date`] = data.checked_in_date;
                student[`period#${periodId}-checked_out_date`] = data.checked_out_date;
            }
        });
    }

    get isSaving() {
        return this.connector?.isSaving || false;
    }

    setSelectedPeriod(id: number) {
        if (!this.canEditPresence) { return; }
        const selectedPeriod = this.periods.find((p: any) => p.id === id) || null;
        this.selectedPeriod = selectedPeriod || null;
    }

    setSelectedPeriodNonRegistered(id: number) {
        if (!this.canEditPresence) { return; }
        const selectedPeriodNonRegistered = this.periods.find((p: any) => p.id === id) || null;
        this.selectedPeriodNonRegistered = selectedPeriodNonRegistered || null;
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

    async itemsProvider(ctx: any) {
        const selectedPeriod = this.selectedPeriod;
        const parameters = {
            global_search_query: ctx.filter,
            sort_field: ctx.sortBy,
            sort_direction: ctx.sortDesc ? 'desc' : 'asc',
            items_per_page: ctx.perPage,
            page_number: ctx.currentPage,
            request_count: this.requestCount,
            request_non_registered_students: this.requestNonRegisteredStudents
        };
        const data = await this.connector?.loadPresenceEntries(parameters);
        const {periods, students} = data;
        this.periods = periods;
        this.students = students;
        if (data.count !== undefined) {
            this.pagination.total = data.count;
            this.requestCount = false;
        }
        if (this.requestNonRegisteredStudents) {
            if (data['non_registered_students'] !== undefined) {
                this.nonRegisteredStudents = data['non_registered_students'];
            }
            this.requestNonRegisteredStudents = false;
        }
        if (!this.pageLoaded && this.periods.length) {
            this.$emit('selected-period', this.periods[this.periods.length - 1].id);
            this.setSelectedPeriod(this.periods[this.periods.length - 1].id);
            this.pageLoaded = true;
        } else if (this.createdId !== null) {
            this.$emit('selected-period', this.createdId);
            this.setSelectedPeriod(this.createdId);
            this.createdId = null;
            this.creatingNew = false;
        } else if (selectedPeriod) {
            this.$emit('selected-period', selectedPeriod.id);
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

    async createResultPeriod(callback: Function|undefined = undefined) {
        this.selectedPeriod = null;
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

    async setSelectedStudentStatus(student: any, selectedPeriod: PresencePeriod, status: number) {
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
</style>
