<template>
    <b-table bordered :items="students" :fields="fields" class="mod-presence mod-entry">
        <template #cell(name)="student">
            {{ student.item.lastname }}, {{ student.item.firstname }}
        </template>
        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="data">
            <div class="u-txt-truncate" :title="data.label">{{ data.label }}</div>
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

    get lastLabelField() {
        return this.periods.find((p: any) => p.id === this.last)?.label || '';
    }

    get pastPeriods() {
        return this.periods.filter((period: any) => period.id !== this.last);
    }

    get dynamicFieldKeys() {
        return this.pastPeriods.map((period: any) => ({key: `period#${period.id}`, id: period.id}));
    }

    get fields() {
        return [
            {key: 'name', sortable: true, label: 'Student'},
            ...this.pastPeriods.map((period: any) => ({key: `period#${period.id}`, sortable: false, label: period.label, variant: 'result'})),
            {key: 'period', sortable: false, label: this.lastLabelField , variant: 'period'},
            {key: 'period-result', sortable: false, label: '', variant: 'result'}
        ];
    }

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: Object, default: null}) readonly presence!: Presence|null;

    async load(): Promise<void> {
        const data = await this.connector?.loadPresenceEntries();
        const {periods, last, students} = data;
        this.periods = periods;
        this.last = last;
        this.students = students;
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

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.load();
    }
}
</script>
