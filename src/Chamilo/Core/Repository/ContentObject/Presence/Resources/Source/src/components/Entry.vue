<i18n>
{
    "en": {
        "search": "Find",
        "last-name": "Last name",
        "first-name": "First name",
        "official-code": "Official code",
        "total": "Total",
        "stop-edit-mode": "Stop editing"
    },
    "nl": {
        "search": "Zoeken",
        "last-name": "Familienaam",
        "first-name": "Voornaam",
        "official-code": "Officiële code",
        "total": "Totaal",
        "stop-edit-mode": "Sluit editeren af"
    }
}
</i18n>

<template>
    <div>
        <div class="u-flex" style="margin-bottom: 15px; gap: 15px;" :style="!!selectedPeriod ? 'align-items: flex-end;justify-content: space-between' : 'align-items: center;'">
            <div class="action-bar input-group">
                <b-form-input class="form-group action-bar-search" v-model="globalSearchQuery" @input="onFilterChanged"
                              type="text" :placeholder="$t('search')" debounce="750" autocomplete="off" style="box-shadow: none"></b-form-input>
                <div class="input-group-btn">
                    <button name="clear" class="btn btn-default" value="clear" @click="onFilterCleared">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
            <div v-if="!!selectedPeriod">
                <a @click="selectedPeriod = null" style="cursor: pointer">{{ $t('stop-edit-mode') }}</a>
            </div>
        </div>
        <div class="u-flex" style="flex-direction: row-reverse; gap: 8px">
            <div v-if="!selectedPeriod && periods.length >= 1" style="padding: 10px 0">
                <a style="cursor: pointer" @click="createResultPeriod('')"><i aria-hidden="true" class="fa fa-plus"></i> Nieuwe periode</a>
            </div>
            <div>
                <div style="position: relative">
                    <b-table ref="table" bordered :items="itemsProvider" :fields="fields" class="mod-presence mod-entry"
                             :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :per-page="pagination.perPage"
                             :current-page="pagination.currentPage" :filter="globalSearchQuery" no-sort-reset>
                        <template #head(fullname)>
                            <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">{{ $t('last-name') }}</a>
                            <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">{{ $t('first-name') }}</a>
                        </template>
                        <template #head(official_code)>{{ $t('official-code') }}</template>
                        <template #cell(fullname)="student">
                            {{ student.item.lastname.toUpperCase() }}, {{ student.item.firstname }}
                        </template>
                        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="data">
                            <div class="u-txt-truncate" :title="data.label">
                                <a @click="setSelectedPeriod(fieldKey.id)" style="cursor: pointer">{{ data.label }}</a>
                            </div>
                        </template>
                        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`cell(${fieldKey.key})`]="{ item }">
                            <div class="result-wrap">
                                <div class="color-code" :class="[getStatusColorForStudent(item, fieldKey.id) || 'mod-none']">
                                    <span>{{ getStatusCodeForStudent(item, fieldKey.id) }}</span>
                                </div>
                            </div>
                        </template>
                        <template #head(period)>
                            <b-input ref="selectedPeriodLabel" type="text" required debounce="750" v-model="selectedPeriodLabel" style="font-weight: normal;"></b-input>
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
                            <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                        </template>
                        <template #cell(period-result)="student">
                            <div class="result-wrap">
                                <div class="color-code" :class="[getStatusColorForStudent(student.item) || 'mod-none']">
                                    <span>{{ getStatusCodeForStudent(student.item) }}</span>
                                </div>
                            </div>
                        </template>
                        <template #head(period-plh)>
                            <a @click="createResultPeriod('P1')" style="cursor: pointer">P1</a>
                        </template>
                        <template #cell(period-plh)>
                            <div class="result-wrap">
                                <div class="color-code mod-none"></div>
                            </div>
                        </template>
                    </b-table>
                    <div class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
                </div>
                <div class="pagination-container u-flex u-justify-content-end">
                    <b-pagination v-model="pagination.currentPage" :total-rows="pagination.total" :per-page="pagination.perPage"
                                  aria-controls="data-table"></b-pagination>
                    <ul class="pagination">
                        <li class="page-item active"><a class="page-link">{{ $t('total') }} {{ pagination.total }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {Presence, PresencePeriod, PresenceStatus, PresenceStatusDefault} from '../types';
import APIConfig from '../connect/APIConfig';
import Connector from '../connect/Connector';

@Component({
    name: 'entry'
})
export default class Entry extends Vue {
    connector: Connector | null = null;
    periods: PresencePeriod[] = [];
    selectedPeriod: PresencePeriod | null = null;
    students: any[] = [];

    sortBy = 'lastname';
    sortDesc = false;
    pagination = {
        currentPage: 1,
        perPage: 5, // 20
        total: 0
    };
    globalSearchQuery = '';
    requestCount = true;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: Object, default: null}) readonly presence!: Presence|null;

    get selectedPeriodLabel(): string {
        return this.selectedPeriod?.label || '';
    }

    set selectedPeriodLabel(label: string) {
        if (!this.selectedPeriod) { return; }
        this.selectedPeriod.label = label;
        if (label === '') {
            const el = this.$refs['selectedPeriodLabel'] as HTMLFormElement;
            if (el && !el.checkValidity()) {
                el.reportValidity();
            }
            return;
        }
        this.connector?.updatePresencePeriod(this.selectedPeriod.id, label);
    }

    get isSaving() {
        return this.connector?.isSaving || false;
    }

    setSelectedPeriod(id: number) {
        const selectedPeriod = this.periods.find((p: any) => p.id === id) || null;
        this.selectedPeriod = selectedPeriod || null;
    }

    get dynamicFieldKeys(): any {
        return this.periods.map((period: any) => ({key: `period#${period.id}`, id: period.id}));
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
        const {periods, students} = data;
        this.periods = periods;
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
        if (!this.selectedPeriod) { return false; }
        return this.getStudentStatusForPeriod(student, this.selectedPeriod.id) === status;
    }

    async createResultPeriod(label: string) {
        const data = await this.connector?.createResultPeriod(label);
        if (data.status === 'ok') {
            (this.$refs.table as any).refresh();
        }
    }

    async setSelectedStudentStatus(student: any, status: number) {
        if (!this.selectedPeriod) { return; }
        const periodId = this.selectedPeriod.id;
        student[`period#${periodId}-status`] = status;
        const data = await this.connector?.savePresenceEntry(periodId, student.id, status);
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

    get fields() {
        if (!this.students.length) {
            return [
                {key: 'fullname', sortable: false, label: 'Student'},
                {key: 'official_code', sortable: true}
            ];
        }
        if (!this.periods.length) {
            return [
                {key: 'fullname', sortable: false, label: 'Student'},
                {key: 'official_code', sortable: true},
                {key: 'period-plh', sortable: false}
            ];
        }
        if (!!this.selectedPeriod) {
            return [
                {key: 'fullname', sortable: false, label: 'Student'},
                {key: 'official_code', sortable: true},
                {key: 'period', sortable: false, label: this.selectedPeriodLabel, variant: 'period'},
                {key: 'period-result', sortable: false, label: '', variant: 'result mod-save'}
            ];
        }
        return [
            {key: 'fullname', sortable: false, label: 'Student'},
            {key: 'official_code', sortable: true},
            ...this.periods.map((period: any) => ({key: `period#${period.id}`, sortable: false, label: period.label, variant: 'result'}))
        ];
/*        return [
            {key: 'fullname', sortable: false, label: 'Student'},
            {key: 'official_code', sortable: true},
            ...this.pastPeriods.map((period: any) => ({key: `period#${period.id}`, sortable: false, label: period.label, thClass: 'tbl-no-sort', variant: 'result'})),
            {key: 'period', sortable: false, label: this.lastLabelField , thClass: 'tbl-no-sort', variant: 'period'},
            {key: 'period-result', sortable: false, label: '', thClass: 'tbl-no-sort', variant: 'result'}
        ];*/
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