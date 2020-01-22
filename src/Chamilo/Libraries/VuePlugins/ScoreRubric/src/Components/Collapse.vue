<template>
    <div class="collapse-header">
        <div>
            <button v-on:click="$emit('toggle-collapse')" class="btn btn-sm btn-secondary">
                <i class="fa fa-2x"
                   :class="caretClass" aria-hidden="true"></i>
            </button>
        </div>
        <slot></slot>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../ScoreRubricStore";

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
    .collapse-header {
        display: flex;
        flex-direction: row;
    }

    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-secondary:focus {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
    }
</style>
