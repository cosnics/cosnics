import Vue from 'vue';
import VueRouter from 'vue-router';

import RubricDemoHome from '../Views/RubricDemoHome.vue';
import RubricBuilderDemoWrapper from '../Views/RubricBuilderDemoWrapper.vue';
import RubricPreviewDemoWrapper from '../Views/RubricPreviewDemoWrapper.vue';
import RubricEntryDemoWrapper from '../Views/RubricEntryDemoWrapper.vue';
import RubricResultDemoWrapper from '../Views/RubricResultDemoWrapper.vue';
import ScoreRubricView from '../Components/Builder/Rubric/ScoreRubricView.vue';
import LevelsView from '../Components/Builder/Levels/LevelsView.vue';
import RubricBuilderFull from '../Views/RubricBuilderFull.vue';
import RubricBuilderPreview from '../Views/RubricBuilderPreview.vue';

Vue.use(VueRouter);

const builderRoutes = [
  {
    path: '/',
    name: 'BuilderPreview',
    component: RubricBuilderPreview,
  },
  {
    path: '/builder',
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
        name: 'BuilderPreview',
        component: RubricBuilderPreview
      },
      {
        path: 'builder',
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
    path: '/preview',
    name: 'Preview',
    component: RubricPreviewDemoWrapper
  },
  {
    path: '/entry',
    name: 'Entry',
    component: RubricEntryDemoWrapper,
    props: true
  },
  {
    path: '/results',
    name: 'Results',
    component: RubricResultDemoWrapper,
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
