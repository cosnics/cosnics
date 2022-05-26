<template>
    <div class="btn-group" style="flex: 1; width: 100%;" :class="{'open': isOpen}" v-click-outside="close">
        <button aria-haspopup="true" :aria-expanded="isOpen" class="btn dropdown-toggle" title="Scores toevoegen" @click="isOpen = !isOpen">
            <span>Scores toevoegen</span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" style="width: 100%" >
            <li role="presentation" v-for="(item, index) in gradedItems" :key="`item-${index}`" @click.stop="">
                <a role="menuitem" href="#" target="_self" class="dropdown-item">
                    <b-form-checkbox :id="`${id}-item-${index}`" :checked="item.checked" :disabled="item.disabled" @change="toggleItem(item, index)" :class="{'is-disabled': item.disabled}">
                        {{ item.name }}
                        <div class="score-breadcrumb-trail">{{ item.breadcrumb }}</div>
                    </b-form-checkbox>
                </a>
            </li>
        </ul>
        <div v-if="gradeItemToRemove" style="position: fixed;inset:0;background: rgba(0, 0, 0, 0.2);z-index:1000;" @click.stop="">
            <div class="modal-content">
                <div class="modal-content-title">Score '{{ gradeItemToRemove.name }}' verwijderen uit overzicht?</div>
                <div style="display:flex;gap: 10px;margin-top:20px">
                    <button class="btn btn-default btn-sm" @click="remove">Verwijder</button>
                    <button class="btn btn-default btn-sm" @click="cancel">Annuleer</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import ClickOutside from 'vue-click-outside';
import {GradeItem} from '../domain/GradeBook';

@Component({
    directives: { ClickOutside }
})
export default class GradesDropdown extends Vue {
    private isOpen = false;
    private gradeItemToRemove: GradeItem|null = null;

    @Prop({type: String, default: ''}) readonly id!: string;
    @Prop({type: Array, required: true}) readonly gradedItems!: GradeItem[];

    // eslint-disable-next-line no-unused-vars
    toggleItem(item: GradeItem, index: number) {
        if (item.checked) {
            this.gradeItemToRemove = item;
            return;
        }
        this.$emit('toggle', item, !item.checked);
    }

    open() {
        this.isOpen = true;
    }

    close() {
        this.isOpen = false;
    }

    cancel() {
        if (this.gradeItemToRemove) {
            const index = this.gradedItems.indexOf(this.gradeItemToRemove);
            if (index !== -1) {
                this.$nextTick(() => (document.querySelector(`#${this.id}-item-${index}`) as HTMLInputElement).checked = true);
            }
        }
        this.gradeItemToRemove = null;
    }

    remove() {
        if (this.gradeItemToRemove) {
            this.$emit('toggle', this.gradeItemToRemove, false);
        }
        this.gradeItemToRemove = null;
    }
}
</script>

<style scoped>
.dropdown-menu {
    max-height: 400px;
    overflow-y: auto;
    overflow-x: hidden;
}
.dropdown-menu >>> input[type=checkbox] {
    float: left;
    margin-right: .5em;
}
.dropdown-menu >>> label {
    font-weight: 400;
    white-space: normal;
}
.dropdown-menu >>> .dropdown-item {
    padding: 3px 10px;
}
.score-breadcrumb-trail {
    font-size: 1.2rem;
}
.btn.dropdown-toggle {
    align-items: center;
    display: flex;
    justify-content: space-between;
    width: 100%;
}
.custom-control.is-disabled {
    color: #999;
    font-style: italic;
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
</style>