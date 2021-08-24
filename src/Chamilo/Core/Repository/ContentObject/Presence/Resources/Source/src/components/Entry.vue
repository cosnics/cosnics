<template>
    <div>
        <div class="u-flex" style="margin-bottom: 15px">
            <div class="action-bar input-group">
                <b-form-input class="form-group action-bar-search" v-model="globalSearchQuery" @input="onFilterChanged"
                              type="text" placeholder="Search" debounce="750" autocomplete="off"></b-form-input>
                <div class="input-group-btn">
                    <button name="clear" class="btn btn-default" value="clear" @click="onFilterCleared">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
        <div style="position: relative">
            <b-table bordered :items="itemsProvider" :fields="fields" class="mod-presence mod-entry"
                     :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :per-page="pagination.perPage"
                     :current-page="pagination.currentPage" :filter="globalSearchQuery">
                <template #head(fullname)="data">
                    <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">{{ 'Lastname'|trans({}, 'Chamilo\\Core\\User')|upper }}</a>
                    <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">{{ 'Firstname'|trans({}, 'Chamilo\\Core\\User') }}</a>
                </template>
                <template #cell(fullname)="student">
                    {{ student.item.lastname.toUpperCase() }}, {{ student.item.firstname }}
                </template>
                <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="data">
                    <div class="u-txt-truncate" @click.stop="" :title="data.label" style="pointer-events: all">{{ data.label }}</div>
                </template>
                <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`cell(${fieldKey.key})`]="{ item }">
                    <div class="result-wrap">
                        <div class="color-code" :class="[getStatusColorForStudent(item, fieldKey.id) || 'mod-none']">
                            <span>{{ getStatusCodeForStudent(item, fieldKey.id) }}</span>
                        </div>
                    </div>
                </template>
                <template #cell(period)="student">
                    <div class="u-flex u-gap-small u-flex-wrap">
                        <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code"
                                :class="[status.color, { 'is-selected': hasSelectedStudentStatus(student.item, status.id) }]"
                                @click="setSelectedStudentStatus(student.item, status.id)"
                                :aria-pressed="hasSelectedStudentStatus(student.item, status.id) ? 'true': 'false'"><span>{{ status.code }}</span></button>
                    </div>
                </template>
                <template #head(period-result)="">
                </template>
                <template #cell(period-result)="student">
                    <div class="result-wrap">
                        <div class="color-code" :class="[getStatusColorForStudent(student.item) || 'mod-none']">
                            <span>{{ getStatusCodeForStudent(student.item) }}</span>
                        </div>
                    </div>
                </template>
            </b-table>
            <div class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
        </div>
        <div class="pagination-container u-flex u-justify-content-end">
            <b-pagination v-model="pagination.currentPage" :total-rows="pagination.total" :per-page="pagination.perPage"
                          aria-controls="data-table"></b-pagination>
            <ul class="pagination">
                <li class="page-item active"><a class="page-link">{{ 'Total'|trans({}, appContext) }} {{ pagination.total }}</a></li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {Presence, PresenceStatus, PresenceStatusDefault} from '../types';
import APIConfig from '../connect/APIConfig';
import Connector from '../connect/Connector';

@Component({
    name: 'entry'
})
export default class Entry extends Vue {
    connector: Connector | null = null;
    periods: any[] = [];
    students: any[] = [];
    last: number = -1;

    sortBy = 'lastname';
    sortDesc = false;
    pagination = {
        currentPage: 1,
        perPage: 3,
        total: 0
    };
    globalSearchQuery = '';
    requestCount = true;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: Object, default: null}) readonly presence!: Presence|null;

    get lastLabelField() {
        return this.periods.find((p: any) => p.id === this.last)?.label || '';
    }

    get pastPeriods() {
        return this.periods.filter((period: any) => period.id !== this.last);
    }

    get dynamicFieldKeys() {
        return this.pastPeriods.map((period: any) => ({key: `period#${period.id}`, id: period.id}));
    }

    async itemsProvider(ctx: any) {
        const parameters = {
            global_search_query: ctx.filter,
            sort_field: ctx.sortBy,
            sort_direction: ctx.sortDesc ? 'desc' : 'asc',
            items_per_page: ctx.perPage,
            page_number: ctx.currentPage,
            request_count: this.requestCount
        };
        const data = await this.connector?.loadPresenceEntries(parameters);
        const {periods, last, students} = data;
        this.periods = periods;
        this.last = last;
        this.students = students;
        if (data.count !== undefined) {
            this.pagination.total = data.count;
            this.requestCount = false;
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
        return this.getStudentStatusForPeriod(student, this.last) === status;
    }

    setSelectedStudentStatus(student: any, status: number) {
        student[`period#${this.last}-status`] = status;
    }

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    getPresenceStatus(statusId: number): PresenceStatus | undefined {
        return this.presenceStatuses.find(status => status.id === statusId);
    }

    getStatusCodeForStudent(student: any, periodId: number|undefined = undefined): string {
        return this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId || this.last))?.code || '';
    }

    getStatusColorForStudent(student: any, periodId: number|undefined = undefined): string {
        return this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId || this.last))?.color || '';
    }

    get fields() {
        return [
            {key: 'fullname', sortable: false, label: 'Student'},
            {key: 'official_code', sortable: true},
            ...this.pastPeriods.map((period: any) => ({key: `period#${period.id}`, sortable: false, label: period.label, thClass: 'tbl-no-sort', variant: 'result'})),
            {key: 'period', sortable: false, label: this.lastLabelField , thClass: 'tbl-no-sort', variant: 'period'},
            {key: 'period-result', sortable: false, label: '', thClass: 'tbl-no-sort', variant: 'result'}
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
    }
}
</script>
