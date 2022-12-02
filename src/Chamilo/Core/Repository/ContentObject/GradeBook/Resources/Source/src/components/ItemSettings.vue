<i18n>
{
    "en": {
        "aabs": "aabs",
        "auth-absent": "Authorized absent",
        "authorized-absence": "In the event of authorized absence",
        "cancel": "Cancel",
        "close": "Close",
        "count-towards-endresult": "Count towards end result",
        "count-towards-endresult-not": "Score does not count towards final result",
        "group-scores": "Group scores",
        "grouped-scores": "Grouped scores",
        "make-visible": "Show score to student",
        "maximum-towards-endresult": "Maximum score (100%) counts towards final result",
        "minimum-towards-endresult": "Minimum score (0%) counts towards final result",
        "no-score-found": "No score found",
        "remove": "Remove",
        "remove-from-overview": "Remove score '{title}' from overview?",
        "settings": "Settings",
        "unauthorized-absence": "In the absence of a score (without authorized absence)",
        "weight": "Weight"
    },
    "nl": {
        "aabs": "gafw",
        "auth-absent": "Gewettigd afwezig",
        "authorized-absence": "Bij gewettigde afwezigheid",
        "cancel": "Annuleren",
        "close": "Sluiten",
        "count-towards-endresult": "Meetellen voor eindresultaat",
        "count-towards-endresult-not": "Score niet meetellen voor het eindresultaat",
        "group-scores": "Scores groeperen",
        "grouped-scores": "Gegroepeerde scores",
        "make-visible": "Score weergeven voor student",
        "maximum-towards-endresult": "Maximale score (100%) meetellen voor het eindresultaat",
        "minimum-towards-endresult": "Minimale score (0%) meetellen voor het eindresultaat",
        "no-score-found": "Geen score gevonden",
        "remove": "Verwijderen",
        "remove-from-overview": "Score '{title}' verwijderen uit overzicht?",
        "settings": "Instellingen",
        "unauthorized-absence": "Bij ontbreken van score (zonder gewettigde afwezigheid)",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div class="modal-wrapper">
        <div class="modal-content">
            <div class="u-flex u-justify-content-between modal-header">
                <div>
                    <input type="text" :value="title" @input="title = $event" autocomplete="off">
                    <button class="btn btn-link" @click="showRemoveItemDialog = true">{{ $t('remove') }}</button>
                </div>
                <button class="btn-close u-ml-auto" @click="$emit('close')" :title="$t('close')"><i class="fa fa-times" aria-hidden="true"></i><span class="sr-only">{{ $t('close') }}</span></button>
            </div>
            <div class="modal-body">
                <template v-if="column.type !== 'standalone'">
                    <h5 v-if="isGrouped">{{ $t('grouped-scores') }}</h5>
                    <ul class="grouped-scores">
                        <li v-for="item in subItems" :key="item.id"><span>{{ item.title }}</span>
                            <div class="score-breadcrumb-trail">{{ item|breadcrumb }}</div>
                        </li>
                    </ul>
                    <div class="ml-20">
                        <button v-if="!(isGrouped || groupButtonPressed)" class="btn btn-default" @click="openGradesDropdown">{{ $t('group-scores') }}</button>
                        <grades-dropdown id="dropdown-settings" ref="dropdown" v-else :graded-items="gradedItems" @toggle="toggleSubItem"></grades-dropdown>
                    </div>
                </template>
                <h5 :class="{'standalone': column.type === 'standalone'}">{{ $t('settings') }}</h5>
                <div class="settings">
                    <div>
                        <input type="checkbox" id="released" v-model="column.released" @input="onGradeColumnChange" class="mr-5">
                        <label class="settings-label u-font-medium" for="released">{{ $t('make-visible') }}</label>
                    </div>
                    <div>
                        <input type="checkbox" id="countForEndResult" v-model="column.countForEndResult" @input="onGradeColumnChange" class="mr-5">
                        <label class="settings-label u-font-medium" for="countForEndResult">{{ $t('count-towards-endresult') }}</label>
                    </div>
                    <div v-if="column.countForEndResult">
                        <div class="mt-10">
                            <label for="weight" class="settings-label u-block">{{ $t('weight') }}:</label>
                            <div class="number-input u-relative">
                                <input type="number" id="weight" :value="gradeBook.getWeight(column.id)|formatNum" @input="setWeight" autocomplete="off">
                                <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                            </div>
                        </div>
                        <div class="mt-20">
                            <label class="settings-label">{{ $t('authorized-absence') }} <div aria-hidden="true" class="color-code amber-700 mi-3" :title="$t('auth-absent')"><span>{{ $t('aabs') }}</span></div>:</label>
                            <div>
                                <input type="radio" name="gafw-option" id="gafw-option1" value="0" v-model.number="column.authPresenceEndResult" @input="onGradeColumnChange" class="mr-5">
                                <label class="u-font-normal" for="gafw-option1">{{ $t('count-towards-endresult-not') }}</label>
                            </div>
                            <div>
                                <input type="radio" name="gafw-option" id="gafw-option2" value="1" v-model.number="column.authPresenceEndResult" @input="onGradeColumnChange" class="mr-5">
                                <label class="u-font-normal" for="gafw-option2">{{ $t('maximum-towards-endresult') }}</label>
                            </div>
                            <div>
                                <input type="radio" name="gafw-option" id="gafw-option3" value="2" v-model.number="column.authPresenceEndResult" @input="onGradeColumnChange" class="mr-5">
                                <label class="u-font-normal" for="gafw-option3">{{ $t('minimum-towards-endresult') }}</label>
                            </div>
                        </div>
                        <div class="mt-20">
                            <label class="settings-label">{{ $t('unauthorized-absence') }} <div class="color-code mod-none mi-3" :title="$t('no-score-found')"><i aria-hidden="true" class="fa fa-question"></i></div>:</label>
                            <div>
                                <input type="radio" name="nogafw-option" id="nogafw-option1" value="0" v-model.number="column.unauthPresenceEndResult" @input="onGradeColumnChange" class="mr-5">
                                <label class="u-font-normal" for="nogafw-option1">{{ $t('count-towards-endresult-not') }}</label>
                            </div>
                            <div>
                                <input type="radio" name="nogafw-option" id="nogafw-option2" value="1" v-model.number="column.unauthPresenceEndResult" @input="onGradeColumnChange" class="mr-5">
                                <label class="u-font-normal" for="nogafw-option2">{{ $t('maximum-towards-endresult') }}</label>
                            </div>
                            <div>
                                <input type="radio" name="nogafw-option" id="nogafw-option3" value="2" v-model.number="column.unauthPresenceEndResult" @input="onGradeColumnChange" class="mr-5">
                                <label class="u-font-normal" for="nogafw-option3">{{ $t('minimum-towards-endresult') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-overlay" @click="$emit('close')"></div>
        <div class="modal-remove" v-if="showRemoveItemDialog" @click.stop="">
            <div class="u-flex u-align-items-center u-justify-content-center modal-remove-content">
                <div>{{ $t('remove-from-overview', {title}) }}</div>
                <div class="u-flex modal-remove-actions">
                    <button class="btn btn-default btn-sm" @click="removeColumn">{{ $t('remove') }}</button>
                    <button class="btn btn-default btn-sm" @click="cancel">{{ $t('cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook, {GradeItem, ColumnId} from '../domain/GradeBook';
import GradesDropdown from './GradesDropdown.vue';
import debounce from 'debounce';

@Component({
    components: { GradesDropdown },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        },
        breadcrumb: function (gradedItem: GradeItem) {
            return gradedItem.breadcrumb.join(' » ');
        }
    }
})
export default class ItemSettings extends Vue {
    private groupButtonPressed = false;
    private showRemoveItemDialog = false;

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
    @Prop({type: [String, Number]}) readonly columnId!: ColumnId;

    constructor() {
        super();
        this.onGradeColumnChange = debounce(this.onGradeColumnChange, 750);
    }

    openGradesDropdown() {
        this.groupButtonPressed = true;
        window.setTimeout(() => {(this.$refs['dropdown'] as unknown as any).open() }, 100);
    }

    get column() {
        return this.gradeBook.getGradeColumn(this.columnId);
    }

    get isGrouped() {
        return this.gradeBook.isGrouped(this.columnId);
    }

    get subItems() {
        return this.gradeBook.getColumnSubItems(this.columnId);
    }

    get title() {
        return this.gradeBook.getTitle(this.columnId);
    }

    set title(event: any) {
        this.gradeBook.setTitle(this.columnId, event.target.value);
        this.onGradeColumnChange();
    }

    setWeight(event: any) {
        const weight = parseFloat(event.target.value);
        this.gradeBook.setWeight(this.columnId, isNaN(weight) ? null : weight);
        this.onGradeColumnChange();
    }

    get gradedItems(): GradeItem[] {
        return this.gradeBook.getGradedItemsFilteredByColumn(this.columnId);
    }

    toggleSubItem(gradeItem: GradeItem, isAdding: boolean) {
        const item = this.gradeBook.gradeItems.find(item => item.id === gradeItem.id)!;
        if (isAdding) {
            this.addSubItem(item);
        } else {
            this.removeSubItem(item);
        }
    }

    addSubItem(item: GradeItem) {
        this.gradeBook.addSubItem(item, this.columnId);
        this.$emit('add-subitem', item, this.columnId);
    }

    removeSubItem(item: GradeItem) {
        this.gradeBook.removeSubItem(item);
        this.$emit('remove-subitem', item, this.columnId);
        if (item.id === this.columnId) {
            this.$emit('close');
        }
    }

    removeColumn() {
        const column = this.column;
        if (column) {
            this.gradeBook.removeColumn(column);
            this.$emit('remove-column', column);
        }
        this.showRemoveItemDialog = false;
        this.$emit('close');
    }

    cancel() {
        this.showRemoveItemDialog = false;
    }

    onGradeColumnChange() {
        this.$emit('change-gradecolumn', this.column!);
    }
}
</script>

<style lang="scss" scoped>
.mt-10 {
    margin-top: 10px;
}

.mt-20 {
    margin-top: 20px;
}

.ml-20 {
    margin-left: 20px;
}

.mr-5 {
    margin-right: 5px;
}

.mi-3 {
    margin-inline: 3px;
}

input[type="text"], input[type="number"] {
    border: 1px solid #ced4da;
    border-radius: .2rem;
    color: #333;
    min-height: 24px;
    padding: 4px 18px 4px 4px;
}

input[type="text"] {
    font-size: 20px;
}

input[type="number"] {
    -moz-appearance: textfield;
    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}

input:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6);
}

label {
    font-size: 1.4rem;
}

h5 {
    border-bottom: 1px dotted #c1d1cf;
    color:#487771;
    font-size: 20px;
    font-variant: all-small-caps;
    padding-bottom: .5rem;

    &:not(.standalone) {
        margin-top: 2rem;
    }
}

.btn-close {
    background: none;
    border: none;
    font-size: 2rem;
}

.grouped-scores {
    margin-bottom: 5px;
    padding-left: 2rem;

    li {
        margin-bottom: .25rem;

        > span {
            font-size: 16px;
        }
    }
}

.settings {
    margin-bottom: 1.5rem;
    margin-left: 2rem;
}

.settings-label {
    color: #245c55;
}

.modal-wrapper {
    inset: 0;
    overflow-y: auto;
    position: fixed;
    z-index: 2;
}

.modal-content {
    background-color: white;
    border-radius: 3px;
    box-shadow: 0 6px 12px #999;
    margin: 20px auto;
    max-width: 800px;
}

.modal-overlay {
    background-color: #000;
    height: 100vh;
    left: 0;
    opacity: .2;
    position: fixed;
    top: 0;
    width: 100vw;
    z-index:-1;
}

.modal-remove {
    background-color: rgba(0, 0, 0, 0.2);
    inset: 0;
    position: fixed;
    z-index: 1000;
}

.modal-remove-actions {
    gap: 10px;
    margin-top: 20px;
}

.modal-remove-content {
    background-color: #fff;
    border-radius: 3px;
    box-shadow: 0 6px 12px #666;
    flex-direction: column;
    height: 150px;
    margin: 120px auto;
    max-width: 90%;
    padding: 20px;
    width: 420px;
}

.number-input {
    width: fit-content;
}

.percent {
    align-items: center;
    background-color: #e9ecef;
    border-left: 1px solid #ced4da;
    color: #5b5f64;
    display: flex;
    font-size: 1rem;
    font-weight: 400;
    inset: 1px 1px 1px auto;
    line-height: 1.5;
    padding: 0.375rem 0.75rem;
    position: absolute;
    text-align: center;
    white-space: nowrap;
    z-index: 1;
}

#dropdown-settings {
    width: 100%;
}

.color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    display: inline-flex;
    height: 20px;
    justify-content: center;
    padding: 2px 4px;
    transition: background 75ms linear,color 75ms linear,opacity 75ms linear;
    width: 40px;
}

.color-code > span {
    font-size: 14px;
    font-variant: all-small-caps;
    font-weight: 900;
    line-height: 12px;
}

.color-code.mod-none {
    background-color: #f8fbfb;
    border-color: #e6ecef;
    justify-content: flex-end;
    width: 30px;

    .fa-question {
        color: #777;
    }
}

.amber-700 {
    --color: #ffa000;
    --text-color: white;
}
</style>