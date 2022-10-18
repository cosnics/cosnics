<template>
  <div id="app">
    <Main :api-config="apiConfig"></Main>
    <div v-if="displayServerResponse" id="server-response"></div>
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
        @Prop({type: Boolean, default: false}) readonly displayServerResponse!: boolean;
    }
</script>
