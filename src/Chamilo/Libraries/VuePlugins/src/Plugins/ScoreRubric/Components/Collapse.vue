<template>
    <div class="collapse-header">
        <div>
            <button v-on:click="$emit('toggle-collapse')" class="btn btn-sm btn-secondary">
                <i class="fa fa-2x pull-left caret"
                   :class="caretClass" aria-hidden="true"></i>
            </button>
        </div>
        <slot></slot>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "@/Plugins/ScoreRubric/ScoreRubricStore";

    @Component({
        components: {}
    })
    export default class Collapse extends Vue {
        @Prop()
        collapsed!: boolean;

        @Prop()
        title!: string;

        get caretClass() {
            return {
                "fa-caret-down": !this.collapsed,
                "fa-caret-right": this.collapsed
            }
        }
    }


</script>

<style scoped>
    .caret {
        cursor: pointer;
    }

    .collapse-header {
        display: flex;
        flex-direction: row;
    }
</style>
