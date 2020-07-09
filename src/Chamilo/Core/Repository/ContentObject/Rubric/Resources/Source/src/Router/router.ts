import Vue from 'vue';
import VueRouter from 'vue-router';

import RubricDemoHome from '../Views/RubricDemoHome.vue';
import RubricBuilderDemoWrapper from '../Views/RubricBuilderDemoWrapper.vue';
import RubricEntryWrapper from '../Views/RubricEntryWrapper.vue';
import RubricResultWrapper from '../Views/RubricResultWrapper.vue';
import ScoreRubricView from '../Components/ScoreRubricView.vue';
import LevelsView from '../Components/LevelsView.vue';
import RubricBuilderFull from '../Views/RubricBuilderFull.vue';

Vue.use(VueRouter);

const builderRoutes = [
  {
    path: '/',
    name: 'Builder',
    component: ScoreRubricView,
  },
  {
    path: '/levels',
    name: 'BuilderLevels',
    component: LevelsView,
  },
  {
    path: '/full-view',
    name: 'BuilderFull',
    component: RubricBuilderFull,
  }
];

const demoRoutes = [
  {
    path: '/',
    name: 'Home',
    component: RubricDemoHome,
    props: true
  },
  {
    path: '/builder',
    component: RubricBuilderDemoWrapper,
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

const routers = {
  getRouter: (name: string, mode: string = 'hash') => {
    if (['demo', 'builder'].indexOf(name) === -1) {
      throw new Error(`No router with name '${name}' available.`);
    }
    return new VueRouter({
      mode,
      linkExactActiveClass: 'active-link',
      routes: (name === 'builder') ? builderRoutes : demoRoutes
    });
  }
};

export default routers;
