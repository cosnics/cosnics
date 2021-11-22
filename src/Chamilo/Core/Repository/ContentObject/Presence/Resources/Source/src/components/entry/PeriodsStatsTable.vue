<i18n>
{
    "en": {
        "period": "Period",
        "without-status": "Without status"
    },
    "nl": {
        "period": "Periode",
        "without-status": "Zonder status"
    }
}
</i18n>

<template>
    <b-table :id="id" ref="table" bordered :busy.sync="isBusy" :items="items" :fields="fields" class="mod-presence mod-entry" tbody-tr-class="table-body-row" no-sort-reset>
        <template #head(period-stats)>{{ $t('period') }}</template>
        <template #cell(period-stats)="{item}">
            {{item.label || getPlaceHolder(item.id)}}
        </template>

        <!-- PRESENCE STATUSES -->
        <template v-for="status in presenceStatuses" v-slot:[`head(status-${status.id})`]="">
            <div class="color-code" :class="[status.color]" :title="getPresenceStatusTitle(status)" style="width:fit-content"><span>{{ status.code }}</span></div>
        </template>
        <template #head(status-none)>
            <div class="color-code grey-100"><span>{{ $t('without-status') }}</span></div>
        </template>
        <template v-if="statistics.length" v-for="status in [...presenceStatuses, null]" v-slot:[`cell(status-${status && status.id || 'none'})`]="{item}">
            <template v-for="count in [getPeriodStats(item, status)]">
                <div v-if="count" class="color-code" style="width:fit-content;margin: 0 auto;background:#f9f9f9">
                    <span style="font-variant: initial;font-size:13px">{{ count }}</span>
                </div>
                <span v-else class="u-block u-text-center" style="color:#a9b9bc">0</span>
            </template>
        </template>
    </b-table>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {Presence, PresencePeriod, PresenceStatus, PresenceStatusDefault} from '../../types';

@Component({
    name: 'periods-stats-table'
})
export default class PeriodsStatsTable extends Vue {
    @Prop({type: String, default: '' }) readonly id!: string;
    @Prop({type: Boolean, default: false }) readonly isBusy!: boolean;
    @Prop({type: Object, default: null}) readonly selectedPeriod!: PresencePeriod|null;
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: Array, default: () => []}) readonly periods!: PresencePeriod[];
    @Prop({type: Object, default: null }) readonly presence!: Presence|null;
    @Prop({type: Array, default: () => []}) readonly statistics!: any[];

    get items() {
        return [...this.periodsReversed, {id: null, label: 'Gem./periode'}];
    }

    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }

    get presenceStatuses(): PresenceStatus[] {
        return this.presence?.statuses || [];
    }

    get fields() {
        const statusFields = this.presenceStatuses.map(status => ({key: 'status-' + status.id, sortable: false}));
        statusFields.push({key: 'status-none', sortable: false});

        return [
            {key: 'period-stats', sortable: false},
            ...statusFields
        ];
    }

    getPlaceHolder(periodId: number) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }

    getPeriodStats(periodItem: any, status: PresenceStatus|null) {
        if (!this.periods.length) { return 0; }
        if (periodItem.id === null) {
            const sum: number = this.periods.map(p => this.getPeriodStats(p, status)).reduce((v1, v2) => v1 + v2, 0);
            return parseFloat((sum / this.periods.length).toFixed(1));
        }
        const stat = this.statistics.find(s => s.period_id === periodItem.id && s.choice_id === (status?.id || null));
        return stat?.count || 0;
    }

    getPresenceStatus(statusId: number): PresenceStatus | undefined {
        return this.presenceStatuses.find(status => status.id === statusId);
    }

    getPresenceStatusTitle(status: PresenceStatus): string {
        if (status.type !== 'custom') {
            return this.statusDefaults.find(statusDefault => statusDefault.id === status.id)?.title || '';
        }
        return status.title || '';
    }
}
</script>
