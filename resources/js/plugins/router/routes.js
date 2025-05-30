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
        path:'admin-dashboard',
        component: () => import('@/pages/admin-dashboard.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'chat',
        name: 'Chat',
        component: () => import('@/pages/chat.vue'),
        
      },
      {
        path: 'chat/:user',
        name: 'chat',
        component: () => import('@/pages/chat.vue'),
        props: true
      },
      {
        path: 'user-management',
        component: () => import('@/pages/user-management.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'life-moment-post/:relatedId?',
        name: 'life-moment-post',
        component: () => import('@/pages/life-moment-post.vue'),
        meta: { requiresAuth: true },
        props: true
      },
      {
        path: 'user-profile',
        component: () => import('@/pages/user-profile.vue'),
        meta: { requiresAuth: true },
      },

      {
        path: 'selling-item',
        component: () => import('@/pages/selling-item.vue'),
        meta: { requiresAuth: true },
      },
      {
        path:'provide-service',
        component: () => import('@/pages/provide-services.vue'),
        meta: { requiresAuth: true },
      },
      {
        path:'sales-items',
        name:'sales-items',
        component: () => import('@/pages/sales-items.vue'),
        meta: { requiresAuth: true },
      },
      {
        path:'sales-services',
        name:'sales-services',
        component: () => import('@/pages/sales-services.vue'),
        meta: { requiresAuth: true },
      },
      {
        path:'purchase-items',
        name:'purchase-items',
        component: () => import('@/pages/purchase-items.vue'),
        meta: { requiresAuth: true },
      },
      {
        path:'purchase-services',
        name:'purchase-services',
        component: () => import('@/pages/purchase-services.vue'),
        meta: { requiresAuth: true },
      },
      {
        path:'calendar',
        name:'calendar',
        component: () => import('@/pages/calendar.vue'),
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
        name: 'register',
        component: () => import('@/pages/register.vue'),
      },
      {
        path: 'forgot-password',
        component: () => import('@/pages/forgot-password.vue'),
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
];
