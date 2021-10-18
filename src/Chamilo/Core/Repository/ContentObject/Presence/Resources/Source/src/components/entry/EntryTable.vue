<i18n>
{
    "en": {
        "last-name": "Last name",
        "first-name": "First name",
        "official-code": "Official code",
        "new-period": "New period",
        "remove-period": "Remove period",
        "checked-out": "Checked out",
        "not-checked-out": "Not checked out",
        "checkout-mode": "Checkout mode",
        "stop-edit-mode": "Stop editing",
        "no-students-found": "No students found"
    },
    "nl": {
        "last-name": "Familienaam",
        "first-name": "Voornaam",
        "official-code": "Officiële code",
        "new-period": "Nieuwe periode",
        "remove-period": "Verwijder periode",
        "checked-out": "Uitgechecked",
        "not-checked-out": "Niet uitgechecked",
        "checkout-mode": "Uitcheckmodus",
        "stop-edit-mode": "Sluit editeren af",
        "no-students-found": "Geen studenten gevonden"
    }
}
</i18n>

<template>
    <b-table ref="table" :foot-clone="isRemovePeriodButtonShown" bordered :busy.sync="isBusy" :items="items" :fields="fields" class="mod-presence mod-entry"
             :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :per-page="pagination.perPage" :current-page="pagination.currentPage"
             :filter="globalSearchQuery" no-sort-reset :show-empty="!isBusy">
        <!-- COLUMNS -->
        <template slot="table-colgroup" v-if="canEditPresence">
            <col>
            <col>
            <col>
            <col v-if="!!selectedPeriod && !isCreatingNewPeriod && isFullyEditable">
            <col v-else-if="isCreatingNewPeriod" class="bd-selected-period">
            <template v-if="!!selectedPeriod || isCreatingNewPeriod" v-for="period in periodsReversed">
                <col v-if="!isCreatingNewPeriod && period === selectedPeriod" class="bd-selected-period">
                <col v-else>
            </template>
        </template>

        <template #empty="">
            {{ $t('no-students-found') }}
        </template>

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
        <template #foot(fullname)><span></span></template>
        <template #cell(fullname)="{item}">
            <template v-if="!item.tableEmpty">
                {{ item.lastname.toUpperCase() }}, {{ item.firstname }}
            </template>
            <span v-else class="u-font-italic">{{ $t('no-students-found') }}</span>
        </template>

        <!-- OFFICIAL CODE -->
        <template #head(official_code) v-if="isFullyEditable">
            <a class="tbl-sort-option" :aria-sort="getSortStatus('official_code')" @click="sortByNameField('official_code')">{{ $t('official-code') }}</a>
        </template>
        <template #head(official_code) v-else>{{ $t('official-code') }}</template>
        <template #foot(official_code)><span></span></template>

        <!-- CREATE NEW PERIOD BUTTON -->
        <template #head(new_period)>
            <div role="button" tabindex="0" class="select-period-btn mod-new" @keyup.enter="createPeriod" @click="createPeriod" :title="$t('new-period')">
                <i aria-hidden="true" class="fa fa-plus" style="color: #337ab7"></i> <span class="sr-only">{{ $t('new-period') }}</span>
            </div>
        </template>
        <template #foot(new_period)><span></span></template>

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

        <!-- DYNAMIC FIELD KEYS -->
        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`head(${fieldKey.key})`]="{label}">
            <dynamic-field-key :is-editable="canEditPresence" @select="setSelectedPeriod(fieldKey.id)" :class="[{'select-period-btn' : canEditPresence}, 'u-txt-truncate']" :title="label">
                <template v-slot>
                    <span v-if="label">{{ label }}</span>
                    <span v-else class="u-font-italic">{{ getPlaceHolder(fieldKey.id) }}</span>
                </template>
            </dynamic-field-key>
        </template>
        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`cell(${fieldKey.key})`]="{item}">
            <div class="result-wrap">
                <div :title="getStatusTitleForStudent(item, fieldKey.id)" class="color-code u-cursor-default" :class="[getStatusColorForStudent(item, fieldKey.id) || 'mod-none']">
                    <span>{{ getStatusCodeForStudent(item, fieldKey.id) }}</span>
                </div>
            </div>
        </template>
        <template v-for="fieldKey in dynamicFieldKeys" v-slot:[`foot(${fieldKey.key})`]><span></span></template>

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
            <div v-if="presence && presence.has_checkout" class="selected-period-controls" style="display:none">
                <on-off-switch :id="`checkout-${id}`" switch-class="mod-checkout-choice"
                               :on-text="$t('checkout-mode')" :off-text="$t('checkout-mode')" :checked="checkoutMode"
                               @toggle="checkoutMode = !checkoutMode"/>
            </div>
            <button :title="$t('stop-edit-mode')" class="btn btn-default btn-sm selected-period-close-btn" @click="selectedPeriod = null"><i aria-hidden="true" class="fa fa-close"></i><span class="sr-only">{{ $t('stop-edit-mode') }}</span></button>
        </template>
        <template #cell(period-entry)="{item}">
            <template v-if="item.tableEmpty"><div class="color-code mod-none"></div></template>
            <template v-else-if="presence && presence.has_checkout && checkoutMode">
                <on-off-switch v-if="item[`period#${selectedPeriod.id}-checked_in_date`]"
                               :id="item.id" :on-text="$t('checked-out')" :off-text="$t('not-checked-out')"
                               :checked="item[`period#${selectedPeriod.id}-checked_out_date`] > item[`period#${selectedPeriod.id}-checked_in_date`]"
                               switch-class="mod-checkout"
                               @toggle="$emit('toggle-checkout', item, selectedPeriod, hasNonCourseStudents)"/>
                <div v-else :title="getStatusTitleForStudent(item, selectedPeriod.id)" class="color-code u-cursor-default" :class="[getStatusColorForStudent(item, selectedPeriod.id) || 'mod-none']">
                    <span>{{ getStatusCodeForStudent(item, selectedPeriod.id) }}</span>
                </div>
            </template>
            <div v-else class="u-flex u-gap-small u-flex-wrap">
                <button v-for="(status, index) in presenceStatuses" :key="`status-${index}`" class="color-code mod-selectable"
                        :class="[status.color, { 'is-selected': hasSelectedStudentStatus(item, status.id) }]"
                        :title="getPresenceStatusTitle(status)"
                        @click="!hasSelectedStudentStatus(item, status.id) ? $emit('select-student-status', item, selectedPeriod, status.id, hasNonCourseStudents) : null"
                        :aria-pressed="hasSelectedStudentStatus(item, status.id) ? 'true': 'false'"><span>{{ status.code }}</span></button>
            </div>
        </template>
        <template #foot(period-entry)>
            <button class="btn-remove" @click="$emit('remove-selected-period', selectedPeriod)" :disabled="toRemovePeriod === selectedPeriod">{{ $t('remove-period') }}</button>
        </template>
    </b-table>
</template>

<script lang="ts">
import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
import DynamicFieldKey from './DynamicFieldKey.vue'
import OnOffSwitch from '../OnOffSwitch.vue'
import {Presence, PresencePeriod, PresenceStatus, PresenceStatusDefault} from '../../types';

@Component({
    name: 'entry-table',
    components: {OnOffSwitch, DynamicFieldKey}
})
export default class EntryTable extends Vue {
    sortBy = 'lastname';
    sortDesc = false;
    selectedPeriod: PresencePeriod | null = null;
    checkoutMode = false;
    isBusy = false;

    @Prop({type: String, default: '' }) readonly id!: string;
    @Prop() readonly items!: any[];
    @Prop({type: Object, default: () => ({perPage: 0, currentPage: 0, total: 0})}) readonly pagination!: any;
    @Prop({type: Boolean, default: false}) readonly isSaving!: boolean;
    @Prop({type: String, default: ''}) readonly globalSearchQuery!: string;
    @Prop({type: Array, default: () => []}) readonly statusDefaults!: PresenceStatusDefault[];
    @Prop({type: Array, default: () => []}) readonly periods!: PresencePeriod[];
    @Prop({type: Object, default: null }) readonly presence!: Presence|null;
    @Prop({type: Boolean, default: false}) readonly canEditPresence!: boolean;
    @Prop({type: Boolean, default: false}) readonly hasNonCourseStudents!: boolean;
    @Prop({type: Boolean, default: false}) readonly isCreatingNewPeriod!: boolean;
    @Prop({type: Object, default: null }) readonly toRemovePeriod!: PresencePeriod|null;

    get hasResults() {
        return this.pagination.total !== 0;
    }

    get isFullyEditable() {
        return this.canEditPresence && !this.hasNonCourseStudents;
    }

    get isRemovePeriodButtonShown() {
        return this.isFullyEditable && !!this.selectedPeriod && !this.checkoutMode;
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

    setSelectedPeriod(periodId: number) {
        if (!this.canEditPresence) { return; }
        const selectedPeriod = this.periods.find((p: any) => p.id === periodId) || null;
        this.selectedPeriod = selectedPeriod || null;
        this.checkoutMode = false;
    }

    createPeriod() {
        this.selectedPeriod = null;
        this.$emit('create-period', () => {
            (this.$refs.table as any).refresh();
        });
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
        const periodFields = this.periods.map(period => {
            const key = this.canEditPresence && period === this.selectedPeriod ? 'period-entry' : `period#${period.id}`;
            const variant = this.canEditPresence && period === this.selectedPeriod ? 'period' : 'result';
            return {key, sortable: false, label: period.label, variant};
        });
        periodFields.reverse();
        return periodFields;
    }

    get fields() {
        return [
            ...this.userFields,
            this.canEditPresence && !this.hasNonCourseStudents && !this.isCreatingNewPeriod ? {key: 'new_period', sortable: false, label: ''} : null,
            this.isCreatingNewPeriod ? {key: 'period-entry-plh', sortable: false, variant: 'period'} : null,
            ...this.periodFields
        ];
    }

    created() {
        this.$parent.$on('selected-period', (periodId: number) => {
            this.$nextTick(() => {
                this.setSelectedPeriod(periodId);
            });
        });
        this.$parent.$on('selected-period-maybe', () => {
            this.$nextTick(() => {
                if (this.selectedPeriod) {
                    this.setSelectedPeriod(this.selectedPeriod.id);
                }
            });
        });
        this.$parent.$on('period-removed', (periodId: number) => {
            this.$nextTick(() => {
                if (this.selectedPeriod?.id === periodId) {
                    this.selectedPeriod = null;
                }
            })
        });
        this.$parent.$on('creating-new-period', () => {
            this.selectedPeriod = null;
        });
        this.$parent.$on('filters-changed', () => {
            if (this.isFullyEditable) {
                (this.$refs.table as any).refresh();
            }
        });
    }

    @Watch('selectedPeriod')
    onSelectedPeriodChange()
    {
        if (this.isFullyEditable)
        {
            this.$emit('period-change', this.selectedPeriod);
        }
    }
}
</script>

<style scoped>
.bd-selected-period {
    border: 1px double #8ea4b3;
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

