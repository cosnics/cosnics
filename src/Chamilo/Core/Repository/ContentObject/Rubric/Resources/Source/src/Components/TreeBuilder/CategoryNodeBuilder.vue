<template>
    <div>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon0">Titel</span>
        <input class="form-control mb-2"
               v-model="category.title"
               placeholder="Titel"/>
        </div>
        <strong>Kies een kleur</strong>
        <swatches v-model="category.color" inline></swatches>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import Category from "../../Domain/Category";
    import Swatches from 'vue-swatches'

    // Import the styles too, globally
    import "vue-swatches/dist/vue-swatches.min.css"

    @Component({
        components: {Swatches}
    })
    export default class CategoryNodeBuilder extends Vue {
        @Prop()
        category!: Category;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }
    }
</script>

<style scoped>
</style>
