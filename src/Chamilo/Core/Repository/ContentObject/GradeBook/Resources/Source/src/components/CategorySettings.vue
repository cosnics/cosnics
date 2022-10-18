<i18n>
{
    "en": {
        "cancel": "Cancel",
        "close": "Close",
        "remove": "Remove",
        "remove-category": "Remove category?"
    },
    "nl": {
        "cancel": "Annuleren",
        "close": "Sluiten",
        "remove": "Verwijderen",
        "remove-category": "Categorie verwijderen?"
    }
}
</i18n>
<template>
    <div class="modal-wrapper">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <input type="text" v-model="category.title" autocomplete="off" @input="onCategoryChange">
                    <button class="btn btn-link" @click="showRemoveItemDialog = true">{{ $t('remove') }}</button>
                </div>
                <button class="btn-close" @click="$emit('close')" :aria-label="$t('close')"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <input type="color" v-model="category.color" @input="onCategoryChange">
            </div>
        </div>
        <div class="modal-overlay" @click="$emit('close')"></div>
        <div class="modal-remove" v-if="showRemoveItemDialog" @click.stop="">
            <div class="modal-remove-content">
                <div>{{ $t('remove-category') }}</div>
                <div class="modal-remove-actions">
                    <button class="btn btn-default btn-sm" @click="removeCategory">{{ $t('remove') }}</button>
                    <button class="btn btn-default btn-sm" @click="cancel">{{ $t('cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook, {Category} from '../domain/GradeBook';
import debounce from 'debounce';

@Component({
    components: { }
})
export default class CategorySettings extends Vue {
    private showRemoveItemDialog = false;

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
    @Prop({type: Object, required: true}) readonly category!: Category;

    constructor() {
        super();
        this.onCategoryChange = debounce(this.onCategoryChange, 750);
    }

    onCategoryChange() {
        this.$emit('change-category', this.category);
    }

    removeCategory() {
        const category = this.category;
        this.gradeBook.removeCategory(category);
        this.$emit('remove-category', category);
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
    border: 1px solid #ced4da;
    border-radius: .2rem;
    color: #333;
    font-weight: 400;
    min-height: 24px;
    padding: 4px 18px 4px 4px;
}

input[type="text"] {
    font-size: 20px;
}

input:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6);
}

.btn-close {
    background: none;
    border: none;
    font-size: 2rem;
    margin-left: auto;
}

.modal-wrapper {
    inset: 0;
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

.modal-header {
    display: flex;
    justify-content: space-between;
}

.modal-overlay {
    background: #000;
    height: 100vh;
    left: 0;
    position:fixed;
    opacity: .2;
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
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.modal-remove-content {
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