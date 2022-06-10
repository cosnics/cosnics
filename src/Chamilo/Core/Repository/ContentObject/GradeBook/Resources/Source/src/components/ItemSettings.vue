<template>
    <div style="position: fixed; top: 0; bottom: 0; left: 0; right: 0;z-index:1;overflow-y: auto;">
        <div style="margin: 20px auto;max-width: 800px;background: white;border-radius: 3px;box-shadow: 0 6px 12px #999;">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <div>
                    <input type="text" style="font-size:20px" :value="name" @input="name = $event" autocomplete="off"><!--<h5 style="font-size:20px">{{ gradeBook.getName(itemId) }}</h5>-->
                    <button class="btn btn-link" @click="showRemoveItemDialog = true">Verwijderen</button>
                </div>
                <button class="btn-close" @click="$emit('close')" aria-label="Close" style="margin-left:auto;background:none;border:none;font-size:2rem"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <template v-if="column.type !== 'standalone'">
                    <h5 style="font-size: 20px;color:#487771;font-variant: all-small-caps;padding-bottom: .5rem; border-bottom: 1px dotted #c1d1cf;" v-if="isGrouped">Gegroepeerde scores</h5>
                    <ul style="margin-bottom: 5px;padding-left: 2rem">
                        <li v-for="item in groupedItems" :key="item.id" style="margin-bottom: .25rem"><span style="font-size: 16px">{{ item.name }}</span>
                            <div class="score-breadcrumb-trail">{{ item.breadcrumb }}</div>
                        </li>
                    </ul>
                    <div>
                        <div style="margin: 0 0 0 2rem">
                            <button v-if="!(isGrouped || groupButtonPressed)" class="btn btn-default" @click="openGradesDropdown">Scores groeperen</button>
                            <grades-dropdown id="dropdown-settings" ref="dropdown" v-else :graded-items="gradedItems" @toggle="toggleSubItem"></grades-dropdown>
                        </div>
                    </div>
                </template>
                <h5 style="font-size: 20px;color:#487771;font-variant: all-small-caps;padding-bottom: .5rem; border-bottom: 1px dotted #c1d1cf;" :style="column.type !== 'standalone' ? 'margin-top: 2rem' : ''">Instellingen</h5>
                <div style="margin-left: 2rem; margin-bottom: 1.5rem">
                    <div>
                        <input type="checkbox" id="countForEndResult" v-model="column.countForEndResult">
                        <label for="countForEndResult" style="font-weight: 500; color: #245c55;">Meetellen voor eindresultaat</label>
                    </div>
                    <div v-if="column.countForEndResult">
                        <div style="margin-top: 10px">
                            <label for="weight" style="display:block;font-weight: 500; color: #245c55;">Gewicht:</label>
                            <div style="position: relative;width:fit-content">
                                <input type="number" id="weight" :value="gradeBook.getWeight(column.id)|formatNum" @input="setWeight" autocomplete="off">
                                <div class="percent"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                            </div>
                        </div>
                        <div style="margin-top: 20px">
                            <label style="font-weight: 500; color: #245c55;">Bij gewettigde afwezigheid:</label>
                            <div>
                                <input type="radio" name="gafw-option" id="gafw-option1" value="0" v-model.number="column.authPresenceEndResult">
                                <label for="gafw-option1">Score niet meetellen voor het eindresultaat</label>
                            </div>
                            <div>
                                <input type="radio" name="gafw-option" id="gafw-option2" value="1" v-model.number="column.authPresenceEndResult">
                                <label for="gafw-option2">Maximale score (100%) meetellen voor het eindresultaat</label>
                            </div>
                            <div>
                                <input type="radio" name="gafw-option" id="gafw-option3" value="2" v-model.number="column.authPresenceEndResult">
                                <label for="gafw-option3">Minimale score (0%) meetellen voor het eindresultaat</label>
                            </div>
                        </div>
                        <div style="margin-top: 20px">
                            <label style="font-weight: 500; color: #245c55;">Bij ontbreken van score (zonder gewettigde afwezigheid):</label>
                            <div>
                                <input type="radio" name="nogafw-option" id="nogafw-option1" value="0" v-model.number="column.unauthPresenceEndResult">
                                <label for="nogafw-option1">Score niet meetellen voor het eindresultaat</label>
                            </div>
                            <div>
                                <input type="radio" name="nogafw-option" id="nogafw-option2" value="1" v-model.number="column.unauthPresenceEndResult">
                                <label for="nogafw-option2">Maximale score (100%) meetellen voor het eindresultaat</label>
                            </div>
                            <div>
                                <input type="radio" name="nogafw-option" id="nogafw-option3" value="2" v-model.number="column.unauthPresenceEndResult">
                                <label for="nogafw-option3">Minimale score (0%) meetellen voor het eindresultaat</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="position:fixed; top: 0; left: 0; width: 100vw; height: 100vh;background: #000;opacity: .2;z-index:-1" @click="$emit('close')"></div>
        <div v-if="showRemoveItemDialog" style="position: fixed;inset:0;background: rgba(0, 0, 0, 0.2);z-index:1000;" @click.stop="">
            <div class="modal-content">
                <div class="modal-content-title">Score '{{ column.name }}' verwijderen uit overzicht?</div>
                <div style="display:flex;gap: 10px;margin-top:20px">
                    <button class="btn btn-default btn-sm" @click="removeColumn">Verwijder</button>
                    <button class="btn btn-default btn-sm" @click="cancel">Annuleer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook, {GradeItem, ItemId} from '../domain/GradeBook';
import GradesDropdown from './GradesDropdown.vue';

@Component({
    components: { GradesDropdown },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        }
    }
})
export default class ItemSettings extends Vue {
    private groupButtonPressed = false;
    private showRemoveItemDialog = false;

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
    @Prop({type: [String, Number]}) readonly itemId!: ItemId;

    openGradesDropdown() {
        this.groupButtonPressed = true;
        window.setTimeout(() => {(this.$refs['dropdown'] as unknown as any).open() }, 100);
    }

    get column() {
        return this.gradeBook.getGradeColumn(this.itemId);
    }

    get isGrouped() {
        return this.gradeBook.isGrouped(this.itemId);
    }

    get groupedItems() {
        return this.gradeBook.getGroupItems(this.itemId);
    }

    get name() {
        return this.gradeBook.getName(this.itemId);
    }

    set name(event: any) {
        this.gradeBook.setName(this.itemId, event.target.value);
    }

    setWeight(event: any) {
        const weight = parseFloat(event.target.value);
        this.gradeBook.setWeight(this.itemId, isNaN(weight) ? null : weight);
    }

    get gradedItems(): GradeItem[] {
        return this.gradeBook.getGradedItemsFilteredByItem(this.itemId);
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
        const newGroupId = this.gradeBook.addSubItem(item, this.itemId);
        if (newGroupId !== null) {
            this.$emit('item-settings', newGroupId);
        }
    }

    removeSubItem(item: GradeItem) {
        this.gradeBook.removeGradeItem(item);
        if (item.id === this.itemId) {
            this.$emit('close');
        }
    }

    removeColumn() {
        if (this.column) {
            this.gradeBook.removeColumn(this.column);
        }
        this.showRemoveItemDialog = false;
        this.$emit('close');
    }

    cancel() {
        this.showRemoveItemDialog = false;
    }
}
</script>

<style lang="scss" scoped>
input[type="text"], input[type="number"] {
    border: 1px solid #ced4da;min-height: 24px;color: #333;padding: 4px 18px 4px 4px;font-weight: 400;
    border-radius: .2rem;
}

input[type="radio"], input[type="checkbox"] {
    margin-right: 5px;
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
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%), 0 0 8px rgb(102 175 233 / 60%);
}

label {
    font-size: 1.4rem;
    font-weight: 400;
}

.modal-content {
    align-items: center;
    background-color: #fff;
    border-radius: 3px;
    box-shadow: 0 6px 12px #666;
    display: flex;
    flex-direction: column;
    height: 150px;
    justify-content: center;
    margin: 120px auto;
    max-width: 90%;
    padding: 20px;
    width: 420px;
}

.percent {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #5b5f64;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border-left: 1px solid #ced4da;
    z-index: 1;

    position: absolute;
    top: 1px;
    right: 1px;
    bottom: 1px;
}
</style>