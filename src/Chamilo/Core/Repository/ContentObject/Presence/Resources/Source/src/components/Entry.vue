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
        <div class="u-flex" style="margin-bottom: 15px; max-width: fit-content">
            <div class="action-bar input-group">
                <b-form-input class="form-group action-bar-search" v-model="globalSearchQuery" @input="onFilterChanged"
                              type="text" :placeholder="$t('search')" debounce="750" autocomplete="off" style="box-shadow: none"></b-form-input>
                <div class="input-group-btn">
                    <button name="clear" class="btn btn-default" value="clear" @click="onFilterCleared">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="u-flex" style="flex-direction: row-reverse; gap: 8px">
            <div style="padding: 10px 0">
                <a style="cursor: pointer" @click="createResultPeriod('')"><i aria-hidden="true" class="fa fa-plus"></i> Nieuwe periode</a>
            </div>
            <div>
                <div style="position: relative">
                    <b-table ref="table" bordered :items="itemsProvider" :fields="fields" class="mod-presence mod-entry"
                             :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :per-page="pagination.perPage"
                             :current-page="pagination.currentPage" :filter="globalSearchQuery" no-sort-reset>
                        <template slot="table-colgroup" v-if="!!selectedPeriod">
                            <col>
                            <col>
                            <template v-for="period in periods">
                                <col v-if="period === selectedPeriod" style="border: 1px double #aec2cb">
                                <col v-else>
                            </template>
                        </template>
                        <template #head(fullname)>
                            <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">{{ $t('last-name') }}</a>
                            <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">{{ $t('first-name') }}</a>
                        </template>
                        <template #head(official_code)>{{ $t('official-code') }}</template>
                        <template #cell(fullname)="{item}">
                            {{ item.lastname.toUpperCase() }}, {{ item.firstname }}
                        </template>
                        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="{label}">
                            <div role="button" tabindex="0" @keyup.enter="setSelectedPeriod(fieldKey.id)" @click="setSelectedPeriod(fieldKey.id)" class="select-period-btn u-txt-truncate" :title="label">
                                <span v-if="label">{{ label }}</span>
                                <span v-else style="font-style: italic">{{ getPlaceHolder(fieldKey.id) }}</span>
                            </div>
                        </template>
                        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`cell(${fieldKey.key})`]="{item}">
                            <div class="result-wrap">
                                <div class="color-code" :class="[getStatusColorForStudent(item, fieldKey.id) || 'mod-none']">
                                    <span>{{ getStatusCodeForStudent(item, fieldKey.id) }}</span>
                                </div>
                            </div>
                        </template>
                        <template #head(period-entry)>
                            <div>
                                <b-input type="text" debounce="750" autocomplete="off" :placeholder="getPlaceHolder(selectedPeriod.id)" v-model="selectedPeriodLabel" style="font-weight: normal;height:30px;padding:6px;"></b-input>
                                <div style="width: 15px">
                                    <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                                </div>
                            </div>
                            <button :title="$t('stop-edit-mode')" class="selected-period-close-btn" @click="selectedPeriod = null"><i aria-hidden="true" class="fa fa-times"></i><span class="sr-only">{{ $t('stop-edit-mode') }}</span></button>
                        </template>
                        <template #cell(period-entry)="{item}">
                            <div class="u-flex u-gap-small u-flex-wrap">
                                <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code"
                                        :class="[status.color, { 'is-selected': hasSelectedStudentStatus(item, status.id) }]"
                                        @click="setSelectedStudentStatus(item, status.id)"
                                        :aria-pressed="hasSelectedStudentStatus(item, status.id) ? 'true': 'false'"><span>{{ status.code }}</span></button>
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
    createdId: number | null = null;
    pageLoaded = false;

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
        this.connector?.updatePresencePeriod(this.selectedPeriod.id, label);
    }

    getPlaceHolder(periodId: number) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
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
        const data = await this.connector?.createResultPeriod();
        if (data.status === 'ok') {
            this.createdId = data.id;
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
        return [
            {key: 'fullname', sortable: false, label: 'Student'},
            {key: 'official_code', sortable: true},
            ... this.periods.map(period => {
                const key = period === this.selectedPeriod ? 'period-entry' : `period#${period.id}`;
                const variant = period === this.selectedPeriod ? 'period' : 'result';
                return {key, sortable: false, label: period.label, variant};
            })
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
