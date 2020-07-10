<template>
    <router-view></router-view>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';

    @Component({
        name: 'rubric-demo-wrapper'
    })
    export default class RubricDemoWrapper extends Vue {

    }
</script>
<style lang="scss">
    @include chamilo();
    @include loader();
    @include app();
    @include table-app();
</style>
