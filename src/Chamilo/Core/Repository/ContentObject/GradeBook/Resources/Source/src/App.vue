<template>
  <div id="app">
    <Main :api-config="apiConfig"></Main>
    <div v-if="debugServerResponse" id="server-response"></div>
  </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Main from './components/Main.vue';
    import APIConfig from './connector/APIConfig';

    @Component({
        components: { Main }
    })
    export default class App extends Vue {
        @Prop({type: Object, default: () => null}) readonly apiConfig!: APIConfig;
        @Prop({type: Boolean, default: false}) readonly debugServerResponse!: boolean;
    }
</script>

<style>
#server-response {
    background-color: #fffcd8;
    border: 1px solid #f2edc0;
    border-radius: 3px;
    bottom: 0;
    color: #555000;
    left: 0;
    margin-top: 20px;
    max-height: 120px;
    overflow-y: auto;
    padding: 10px;
    position: fixed;
    right: 0;
    z-index: 1;
}
</style>
