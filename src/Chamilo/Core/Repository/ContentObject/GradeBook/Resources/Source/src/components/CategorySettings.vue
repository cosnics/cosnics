<template>
    <div style="position: fixed; top: 0; bottom: 0; left: 0; right: 0;z-index:1">
        <div style="margin: 20px auto;max-width: 800px;background: white;border-radius: 3px;box-shadow: 0 6px 12px #999;">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <input type="text" style="font-size:20px" v-model="category.name" autocomplete="off"><!--<h5 style="font-size:20px">{{ gradeBook.getName(itemId) }}</h5>-->
                <button class="btn-close" @click="$emit('close')" aria-label="Close" style="margin-left:auto;background:none;border:none;font-size:2rem"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <input type="color" v-model="category.color">
            </div>
        </div>
        <div style="position:fixed; top: 0; left: 0; width: 100vw; height: 100vh;background: #000;opacity: .2;z-index:-1" @click="$emit('close')"></div>
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
    border: 1px solid #ced4da;min-height: 24px;color: #333;padding: 2px 18px 2px 4px;font-weight: 400;
    border-radius: .2rem;
}

input:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%), 0 0 8px rgb(102 175 233 / 60%);
}
</style>