import Vue from 'vue';
import VueRouter from 'vue-router';

import RubricDemoHome from '../Views/RubricDemoHome.vue';
import RubricBuilderWrapper from '../Views/RubricBuilderWrapper.vue';
import RubricEntryWrapper from '../Views/RubricEntryWrapper.vue';
import RubricResultWrapper from '../Views/RubricResultWrapper.vue';
import ScoreRubricView from '../Components/ScoreRubricView.vue';
import LevelsView from '../Components/LevelsView.vue';
import RubricBuilderFull from '../Views/RubricBuilderFull.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Home',
    component: RubricDemoHome,
    props: true
  },
  {
    path: '/builder',
    component: RubricBuilderWrapper,
    children: [
      {
        path: '',
        name: 'Builder',
        component: ScoreRubricView,
      },
      {
        path: 'levels',
        name: 'BuilderLevels',
        component: LevelsView,
      },
      {
        path: 'full-view',
        name: 'BuilderFull',
        component: RubricBuilderFull,
      }
    ]
  },
  {
    path: '/entry',
    name: 'Entry',
    component: RubricEntryWrapper,
    props: true
  },
  {
    path: '/results',
    name: 'Results',
    component: RubricResultWrapper,
    props: true
  }
];

const router = new VueRouter({
  mode: 'history',
  linkExactActiveClass: 'active-link',
  routes
});

export default router;
