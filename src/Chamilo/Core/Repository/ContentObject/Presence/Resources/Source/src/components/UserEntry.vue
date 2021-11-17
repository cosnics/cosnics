<i18n>
{
    "en": {
        "legend": "Legend",
        "period": "Period",
        "checked-in": "Checked in",
        "checked-out": "Checked out"
    },
    "nl": {
        "legend": "Legende",
        "period": "Periode",
        "checked-in": "Ingechecked",
        "checked-out": "Uitgechecked"
    }
}
</i18n>

<template>
    <div class="u-flex">
        <div class="w-max-content">
            <div class="u-relative">
                <div class="u-flex u-align-items-baseline u-flex-wrap u-gap-small-3x m-legend">
                    <span class="lbl-legend">{{ $t('legend') }}:</span>
                    <legend-item v-for="status in presenceStatuses" :title="getPresenceStatusTitle(status)" :label="status.code" :color="status.color" />
                </div>
                <b-table-simple class="mod-presence mod-user" v-if="student">
                    <b-thead>
                        <b-tr class="table-body-row">
                            <b-th>{{ $t('period') }}</b-th>
                            <b-th>Status</b-th>
                            <b-th>{{ $t('checked-in') }}</b-th>
                            <b-th v-if="presence && presence.has_checkout">{{ $t('checked-out') }}</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <student-details v-for="period in periodsReversed" class="table-body-row"
                                         :has-checkout="presence && presence.has_checkout"
                                         :show-status="true"
                                         :period-title="period.label || getPlaceHolder(period.id)"
                                         :title="getStatusTitleForStudent(period.id)"
                                         :label="getStatusCodeForStudent(period.id)"
                                         :color="getStatusColorForStudent(period.id)"
                                         :check-in-date="student[`period#${period.id}-checked_in_date`]"
                                         :check-out-date="student[`period#${period.id}-checked_out_date`]"/>
                    </b-tbody>
                </b-table-simple>
            </div>
            <error-display v-if="errorData" @close="errorData = null">
                <span v-if="errorData.code === 500">{{ errorData.message }}</span>
                <span v-else-if="!!errorData.type">{{ $t('error-' + errorData.type) }}</span>
            </error-display>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {Presence, PresencePeriod, PresenceStatus, PresenceStatusDefault} from '../types';
import APIConfig from '../connect/APIConfig';
import Connector, {ConnectorErrorListener} from '../connect/Connector';
import LegendItem from './entry/LegendItem.vue';
import StudentDetails from './entry/StudentDetails.vue';
import ErrorDisplay from './ErrorDisplay.vue';

@Component({
    name: 'user-entry',
    components: {StudentDetails, LegendItem, ErrorDisplay}
})
export default class UserEntry extends Vue {
    statusDefaults: PresenceStatusDefault[] = [];
    presence: Presence | null = null;
    connector: Connector | null = null;
    periods: PresencePeriod[] = [];
    student: any = {};
    errorData: string|null = null;

    @Prop({type: APIConfig, required: true}) readonly apiConfig!: APIConfig;

    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }

    getPresenceStatus(statusId: number): PresenceStatus | undefined {
        return this.presenceStatuses.find(status => status.id === statusId);
    }

    getStudentStatusForPeriod(periodId: number) {
        return this.student[`period#${periodId}-status`];
    }

    getStatusCodeForStudent(periodId: number): string {
        return this.getPresenceStatus(this.getStudentStatusForPeriod(periodId))?.code || '';
    }

    getStatusColorForStudent(periodId: number): string {
        return this.getPresenceStatus(this.getStudentStatusForPeriod(periodId))?.color || '';
    }

    getStatusTitleForStudent(periodId: number): string {
        const status = this.getPresenceStatus(this.getStudentStatusForPeriod(periodId));
        return status ? this.getPresenceStatusTitle(status) : '';
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

    async load(): Promise<void> {
        const presenceData : any = await this.connector?.loadPresence();
        if (presenceData) {
            this.statusDefaults = presenceData['status-defaults'];
            this.presence = presenceData.presence;
        }
        const {periods, students} = await this.connector?.loadPresenceEntries({});
        this.periods = periods;
        this.student = students[0];
    }

    setError(data: any) : void {
        this.errorData = data;
    }

    mounted(): void {
        this.connector = new Connector(this.apiConfig);
        this.connector.addErrorListener(this as ConnectorErrorListener);
        this.load();
    }
}
</script>

<style>
.table.mod-user {
    width: max-content;
}

.table.mod-presence.mod-user {
    border-top: 1px solid #ebebeb;
}

.table.mod-presence.mod-user th, .table.mod-presence.mod-user td {
    min-width: 150px;
}

.table.mod-presence.mod-user th:nth-child(2), .table.mod-presence.mod-user td:nth-child(2) {
    min-width: unset;
    text-align: center;
}
</style>
<style scoped>
.w-max-content {
    width: max-content;
}

.m-legend {
    margin: 5px 8px 20px;
}

.m-errors {
    margin: 10px 0;
    max-width: 85ch;
}

.lbl-legend {
    color: #507177;
}
</style>
