<template>
    <div id="app-wrapper" class="container-fluid">
        <app></app>
    </div>
</template>

<script lang="ts">
import {Component, Vue} from 'vue-property-decorator';
import App from './App.vue';

@Component({
    components: {
        App
    },
})
export default class AppWrapper extends Vue {
    
}
</script>
<style>
  .chamilo-header {
      min-height: 78px;
      display: flex;
      flex-direction: row;
      overflow: hidden;
  }
  .chamilo-header .start {
      background-image: url("/images/chamilo-start.png");
      width: 168px;
  }
  .chamilo-header .middle {
      background-image: url("/images/chamilo-middle.png");
      flex: 1;
  }
  .chamilo-header .end {
      background-image: url("/images/chamilo-end.png");
      width: 713px;
  }
  @media (max-width: 900px) {
      .chamilo-header {
          min-height: 51px;
      }
      .chamilo-header .start {
          background-image: url("/images/chamilo-start-alt.png");
          width: 176px;
      }
      .chamilo-header .middle {
          background-image: url("/images/chamilo-middle-alt.png");
      }
      .chamilo-header .end {
          background-image: url("/images/chamilo-end-alt.png");
          width: 70px;
      }
  }
  .container-breadcrumb {
      background-color: #f5f5f5;
      margin-bottom: 20px;
  }
  .container-breadcrumb ol.breadcrumb {
      margin-bottom: 0;
      padding-left: 0;
      padding-right: 0;
  }
  footer.chamilo-footer {
      margin-top: 20px;
      padding: 15px 0;
      font-size: 90%;
      text-align: right;
      border-top: 1px solid #dddddd;
  }
</style>
