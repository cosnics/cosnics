<template>
    <div class="btn-group btn-group-sm" role="group" aria-label="Actie">
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

</style>
