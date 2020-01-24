<template>
    <div class="collapse-header">
        <button v-on:click="$emit('toggle-collapse')" class="btn btn-sm btn-secondary btn-collapse">
            <i class="fa fa-1x level-caret"
               :class="caretClass" aria-hidden="true"></i>
            {{collapsed? "klap open": "klap toe"}}
        </button>
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
        margin-right: 5px;

    }
    .btn-collapse {
        margin-right: 5px;
    }

    .level-caret {
        margin-right: 5px;
    }
</style>
