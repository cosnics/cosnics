<template>
    <div class="modal-wrapper">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" v-model="category.title" autocomplete="off">
                <button class="btn-close" @click="$emit('close')" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <input type="color" v-model="category.color">
            </div>
        </div>
        <div class="modal-overlay" @click="$emit('close')"></div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {Category} from '../domain/GradeBook';

@Component({
    components: { }
})
export default class CategorySettings extends Vue {
    @Prop({type: Object, required: true}) readonly category!: Category;
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
    z-index: 1;
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
</style>