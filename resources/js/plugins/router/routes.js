export const routes = [
  { path: '/', redirect: '/login' },

  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'test',
        component: () => import('@/pages/test.vue'),
        
      },
      {
        path: 'user-management',
        component: () => import('@/pages/user-management.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'user-profile',
        component: () => import('@/pages/user-profile.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'life-moment-post',
        component: () => import('@/pages/life-moment-post.vue'),
        meta: { requiresAuth: true },
      }
  
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    children: [
      {
        path: 'login',
        component: () => import('@/pages/login.vue'),
      },
      {
        path: 'register',
        component: () => import('@/pages/register.vue'),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
      {
        path: '/error-unauthorized',
        name: 'error-unauthorized',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
