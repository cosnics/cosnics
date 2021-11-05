<i18n>
{
    "en": {
        "last-name": "Last name",
        "first-name": "First name",
        "official-code": "Official code",
        "new-period": "New period",
        "checked-out": "Checked out",
        "not-checked-out": "Not checked out",
        "checkout-mode": "Checkout mode",
        "no-results": "No results",
        "not-applicable": "n/a"
    },
    "nl": {
        "last-name": "Familienaam",
        "first-name": "Voornaam",
        "official-code": "Officiële code",
        "new-period": "Nieuwe periode",
        "checked-out": "Uitgechecked",
        "not-checked-out": "Niet uitgechecked",
        "checkout-mode": "Uitcheckmodus",
        "no-results": "Geen resultaten",
        "not-applicable": "n.v.t."
    }
}
</i18n>

<template>
    <b-table :id="id" ref="table" bordered :busy.sync="isBusy" :items="items" :fields="fields" class="mod-presence mod-entry"
             :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :per-page="pagination.perPage" :current-page="pagination.currentPage"
             :filter="globalSearchQuery" no-sort-reset>

        <!-- PICTURE -->
        <template #cell(photo)="{item}" v-if="canEditPresence">
            <img :src="item.photo">
        </template>

        <!-- FULLNAME -->
        <template #head(fullname) v-if="isFullyEditable">
            <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">{{ $t('last-name') }}</a>
            <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">{{ $t('first-name') }}</a>
        </template>
        <template #head(fullname) v-else-if="canEditPresence">{{ $t('last-name') }}, {{ $t('first-name') }}</template>
        <template #head(fullname) v-else>Student</template>
        <template #cell(fullname)="{item, toggleDetails, detailsShowing}">
            <template v-if="!item.tableEmpty">
                <a v-if="!selectedPeriod && !useStatistics" @click="toggleDetails" style="cursor:pointer;text-decoration:none" :style="detailsShowing ? 'font-weight: 700' : ''">{{ item.lastname.toUpperCase() }}, {{ item.firstname }}</a>
                <template v-else>{{ item.lastname.toUpperCase() }}, {{ item.firstname }}</template>
            </template>
            <span v-else></span>
        </template>
        <template #row-details="{item}" v-if="!selectedPeriod && !useStatistics">
            <div class="u-flex u-gap-small-3x u-align-items-center" style="justify-content: flex-start;padding-left: 40px">
                <span style="color:#507177">Stats:</span>
                <div v-for="{status, count} in getStudentAllStats(item)" v-if="count > 0" class="u-flex u-align-items-center u-gap-small">
                    <div class="color-code" :class="[status ? status.color : 'grey-100']"><span>{{ status ? status.code : 'Zonder status' }} <span style="margin-left: 5px;font-variant: initial;font-size:13px">{{ count }}</span></span></div>
                </div>
            </div>
        </template>

        <!-- OFFICIAL CODE -->
        <template #head(official_code) v-if="isFullyEditable">
            <a class="tbl-sort-option" :aria-sort="getSortStatus('official_code')" @click="sortByNameField('official_code')">{{ $t('official-code') }}</a>
        </template>
        <template #head(official_code) v-else>{{ $t('official-code') }}</template>

        <!-- CREATE NEW PERIOD PLACEHOLDER -->
        <template #head(period-entry-plh)>
            <div class="u-flex u-align-items-center u-gap-small">
                <b-input type="text" autocomplete="off" :placeholder="$t('new-period') + '...'" class="u-bg-none u-font-normal u-font-italic u-pointer-events-none ti-label mod-border"></b-input>
                <div class="spin">
                    <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                </div>
            </div>
        </template>
        <template #cell(period-entry-plh)>
            <div class="u-flex u-gap-small u-flex-wrap u-pointer-events-none">
                <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code mod-plh"
                        :class="[status.color]"><span>{{ status.code }}</span></button>
            </div>
        </template>

        <!-- PRESENCE STATUSES -->
        <template v-for="status in presenceStatuses" v-slot:[`head(status-${status.id})`]="">
            <div class="color-code" :class="[status.color]" :title="getPresenceStatusTitle(status)" style="width:fit-content"><span>{{ status.code }}</span></div>
        </template>
        <template #head(status-none)>
            <div class="color-code grey-100"><span>Zonder status</span></div>
        </template>
        <template v-for="status in [...presenceStatuses, null]" v-slot:[`cell(status-${status && status.id || 'none'})`]="{item}">
            <template v-for="count in [getStudentStats(item, status)]">
                <div v-if="count" class="color-code grey-100" style="width:fit-content;margin: 0 auto">
                    <span style="font-variant: initial;font-size:13px">{{ count }}</span>
                </div>
                <span v-else style="text-align:center;display:block;color:#a9b9bc">0</span>
            </template>
        </template>

        <!-- DYNAMIC FIELD KEYS (PERIODS) -->
        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="{label}">
            <dynamic-field-key :is-editable="isFullyEditable" @select="$emit('change-selected-period', fieldKey.id)" :class="[{'select-period-btn' : isFullyEditable}, 'u-txt-truncate']" :title="label">
                <template v-slot>
                    <span v-if="label">{{ label }}</span>
                    <span v-else class="u-font-italic">{{ getPlaceHolder(fieldKey.id) }}</span>
                </template>
            </dynamic-field-key>
        </template>
        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`cell(${fieldKey.key})`]="{item}">
            <PresenceStatusDisplay
                :title="getStatusTitleForStudent(item, fieldKey.id)"
                :label="getStatusCodeForStudent(item, fieldKey.id)"
                :color="getStatusColorForStudent(item, fieldKey.id)"
                :has-checkout="presence && presence.has_checkout"
                :check-in-date="item[`period#${fieldKey.id}-checked_in_date`]"
                :check-out-date="item[`period#${fieldKey.id}-checked_out_date`]"/>
        </template>

        <!-- PRESENCE PERIOD ENTRY -->
        <template #head(period-entry)>
            <div class="u-flex u-align-items-center u-gap-small">
                <b-input v-if="isFullyEditable" type="text" debounce="750" autocomplete="off" :placeholder="getPlaceHolder(selectedPeriod.id)" v-model="selectedPeriodLabel" class="u-font-normal ti-label"></b-input>
                <span v-else-if="selectedPeriodLabel" style="width: 100%">{{ selectedPeriodLabel }}</span>
                <span v-else class="u-font-italic" style="width: 100%">{{ getPlaceHolder(selectedPeriod.id) }}</span>
                <div class="spin">
                    <div v-if="isSaving" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                </div>
            </div>
        </template>
        <template #thead-top="data" v-if="isFullyEditable && selectedPeriod && !useStatistics">
            <b-tr>
                <b-td></b-td>
                <b-td colspan="2"><span style="color: #47686b;font-size: 14px;font-weight: 500;">{{ selectedPeriod.label || getPlaceHolder(selectedPeriod.id) }}</span></b-td>
                <b-td><slot name="slot-top"></slot></b-td>
                <b-td v-if="checkoutMode"></b-td>
            </b-tr>
        </template>
        <template #bottom-row v-if="isFullyEditable && selectedPeriod && !useStatistics">
            <b-td colspan="3"></b-td>
            <b-td><slot name="slot-bottom"></slot></b-td>
            <b-td v-if="checkoutMode"></b-td>
        </template>
        <template #cell(period-entry)="{item}">
            <div v-if="item.tableEmpty" class="u-font-italic">{{ $t('no-results') }}</div>
            <div v-else class="u-flex u-gap-small u-flex-wrap">
                <presence-status-button v-for="(status, index) in presenceStatuses" :key="`status-${index}`"
                                        :status="status" :title="getPresenceStatusTitle(status)"
                                        :is-selected="hasSelectedStudentStatus(item, status.id)" :is-disabled="checkoutMode"
                                        @select="$emit('select-student-status', item, selectedPeriod, status.id, hasNonCourseStudents)"/>
            </div>
        </template>

        <!-- PRESENCE PERIOD CHECKOUT -->
        <template #head(period-checkout)>
            <on-off-switch v-if="isFullyEditable" :id="`checkout-${id}`" switch-class="mod-checkout-choice"
                           :on-text="$t('checkout-mode')" :off-text="$t('checkout-mode')" :checked="checkoutMode"
                           @toggle="$emit('toggle-checkout-mode')"/>
            <span v-else></span>
        </template>
        <template #cell(period-checkout)="{item}" v-if="checkoutMode">
            <span v-if="item.tableEmpty"></span>
            <on-off-switch v-else-if="item[`period#${selectedPeriod.id}-checked_in_date`]"
                           :id="item.id" :on-text="$t('checked-out')" :off-text="$t('not-checked-out')"
                           :checked="item[`period#${selectedPeriod.id}-checked_out_date`] > item[`period#${selectedPeriod.id}-checked_in_date`]"
                           switch-class="mod-checkout"
                           @toggle="$emit('toggle-checkout', item, selectedPeriod, hasNonCourseStudents)"/>
            <span v-else style="color: #999">{{ $t('not-applicable') }}</span>
        </template>
        <template #cell(period-checkout) v-else>{{''}}</template>
    </b-table>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import DynamicFieldKey from './DynamicFieldKey.vue'
import OnOffSwitch from '../OnOffSwitch.vue'
import {Presence, PresencePeriod, PresenceStatus, PresenceStatusDefault} from '../../types';
import PresenceStatusButton from './PresenceStatusButton.vue';
import PresenceStatusDisplay from './PresenceStatusDisplay.vue';

@Component({
    name: 'entry-table',
    components: {PresenceStatusDisplay, PresenceStatusButton, OnOffSwitch, DynamicFieldKey}
})
export default class EntryTable extends Vue {
    sortBy = 'lastname';
    sortDesc = false;
    isBusy = false;

    @Prop({type: String, default: '' }) readonly id!: string;
    @Prop() readonly items!: any[];
    @Prop({type: Object, default: null}) readonly selectedPeriod!: PresencePeriod|null;
    @Prop({type: Object, default: () => ({perPage: 0, currentPage: 0, total: 0})}) readonly pagination!: any;
    @Prop({type: Boolean, default: false}) readonly isSaving!: boolean;
    @Prop({type: Boolean, default: false}) readonly checkoutMode!: boolean;
    @Prop({type: String, default: ''}) readonly globalSearchQuery!: string;
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: Array, default: () => []}) readonly periods!: PresencePeriod[];
    @Prop({type: Object, default: null }) readonly presence!: Presence|null;
    @Prop({type: Boolean, default: false}) readonly canEditPresence!: boolean;
    @Prop({type: Boolean, default: false}) readonly hasNonCourseStudents!: boolean;
    @Prop({type: Boolean, default: false}) readonly isCreatingNewPeriod!: boolean;
    @Prop({type: Array, default: () => []}) readonly statistics!: any[];
    @Prop({type: Boolean, default: false}) readonly useStatistics!: boolean;

    getStudentStats(studentItem: any, status: PresenceStatus|null): number {
        let count = 0;
        this.periods.forEach(p => {
            const statusId = studentItem[`period#${p.id}-status`];
            if ((statusId && status?.id === statusId) || !(statusId || status)) {
                count++;
            }
        });
        return count;
    }

    getStudentAllStats(studentItem: any) {
        const studentStats = [...this.presenceStatuses, null].map(status => ({status, count: 0}));
        this.periods.forEach(p => {
            const statusId = studentItem[`period#${p.id}-status`];
            let stat;
            if (statusId) {
                stat = studentStats.find(stat => stat.status?.id === statusId) || null;
            } else {
                stat = studentStats[studentStats.length - 1];
            }
            if (stat) {
                stat.count++;
            }
        });
        return studentStats;
    }

    get hasResults() {
        return this.pagination.total !== 0;
    }

    get isFullyEditable() {
        return this.canEditPresence && !this.hasNonCourseStudents;
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

    get periodsReversed() {
        const periods = [...this.periods];
        periods.reverse();
        return periods;
    }

    get dynamicFieldKeys(): any {
        return this.periods.map((period: any) => ({key: `period#${period.id}`, id: period.id}));
    }

    get selectedPeriodLabel(): string {
        return this.selectedPeriod?.label || '';
    }

    set selectedPeriodLabel(label: string) {
        if (!this.selectedPeriod) { return; }
        this.$emit('period-label-changed', this.selectedPeriod, label);
    }

    getPlaceHolder(periodId: number) {
        return `P${this.periods.findIndex(p => p.id === periodId) + 1}`;
    }

    getStudentStatusForPeriod(student: any, periodId: number) {
        return student[`period#${periodId}-status`];
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

    getPresenceStatus(statusId: number): PresenceStatus | undefined {
        return this.presenceStatuses.find(status => status.id === statusId);
    }

    hasSelectedStudentStatus(student: any, status: number) {
        if (!this.selectedPeriod) { return false; }
        return this.getStudentStatusForPeriod(student, this.selectedPeriod.id) === status;
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

    getStatusTitleForStudent(student: any, periodId: number|undefined = undefined): string {
        if (periodId === undefined) {
            if (!this.selectedPeriod) { return ''; }
            periodId = this.selectedPeriod.id;
        }
        const status = this.getPresenceStatus(this.getStudentStatusForPeriod(student, periodId));
        return status ? this.getPresenceStatusTitle(status) : '';
    }

    get userFields() {
        return [
            this.canEditPresence ? {key: 'photo', sortable: false, label: '', variant: 'photo'} : null,
            {key: 'fullname', sortable: false, label: 'Student'},
            {key: 'official_code', sortable: false}
        ];
    }

    get periodFields() {
        if (this.isCreatingNewPeriod) { return []; }
        if (this.canEditPresence && !!this.selectedPeriod) {
            return [
                {key: 'period-entry', sortable: false, label: this.selectedPeriod.label, variant: 'period'},
                this.presence && this.presence.has_checkout && (this.isFullyEditable || this.checkoutMode) ? {key: 'period-checkout', sortable: false, variant: 'checkout'} : null
            ];
        }
        const periodFields = this.periods.map(period => ({key: `period#${period.id}`, sortable: false, label: period.label || '', variant: 'result'}));
        periodFields.reverse();
        return periodFields;
    }

    get statusFields() {
        const statusFields = this.presenceStatuses.map(status => ({key: 'status-' + status.id, sortable: false}));
        statusFields.push({key: 'status-none', sortable: false});
        return statusFields;
    }

    get fields() {
        return [
            ...this.userFields,
            ...(this.useStatistics ? this.statusFields : [
                //this.canEditPresence && !this.selectedPeriod && !this.hasNonCourseStudents && !this.isCreatingNewPeriod ? {key: 'new_period', sortable: false, label: ''} : null,
                this.isCreatingNewPeriod ? {key: 'period-entry-plh', sortable: false, variant: 'period'} : null,
                ...this.periodFields
            ])
        ];
    }

    created() {
        this.$parent.$on('refresh', () => {
            if (this.isFullyEditable) {
                (this.$refs.table as any).refresh();
            }
        });
    }
}
</script>

<style scoped>
.bd-selected-period {
/*    border: 1px double #8ea4b3;*/
    border: 1px double #ccc;
}

.spin {
    min-width: 13px;
}

.ti-label {
    height: 30px;
    padding: 6px;
}

.ti-label.mod-border {
    border-color: #e9eaea;
}
</style>
