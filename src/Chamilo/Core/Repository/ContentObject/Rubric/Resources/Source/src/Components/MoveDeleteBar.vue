<template>
    <div class="btn-group btn-group-xs btn-group-actions" role="group" aria-label="Actie">
        <button v-if="index > 0" class="btn btn-secondary"
                v-on:click="$emit('move-up')"
                v-b-popover.hover.top="'Verplaats naar boven'"><i
                class="fa fa-arrow-up" aria-hidden="true"></i></button>
        <button class="btn btn-danger"
                v-on:click="$emit('remove')"
                v-b-popover.hover.top="'Verwijder'">
            <i
                    class="fa fa-minus-circle" aria-hidden="true"></i>
        </button>

        <button v-if="index < maxIndex"
                class="btn btn-secondary"
                v-on:click="$emit('move-down')"
                v-b-popover.hover.top="'Verplaats naar beneden'"><i
                class="fa fa-arrow-down" aria-hidden="true"></i></button>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";

    @Component({
        components: {}
    })
    export default class MoveDeleteBar extends Vue {
        @Prop()
        index!: number;

        @Prop()
        maxIndex!:number;

    }


</script>

<style scoped>

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

    .btn-group-actions .btn {
        height: 100%;
        width: 34px;
    }

    .btn-group-actions {
        margin-left: 0.25rem;
        margin-right: -0.75rem;
        min-width: 70px;
    }

</style>
