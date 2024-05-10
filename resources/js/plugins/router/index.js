// import { useAuthStore } from '@/plugins/store/AuthStore';
// import { storeToRefs } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './routes';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// router.beforeEach(async (to, from, next) => {
//   const authStore = useAuthStore();
//   const { isLoggedIn } = storeToRefs(authStore);

//   if (to.meta.requiresAuth && !isLoggedIn.value) {
//     // If the route requires authentication and the user is not logged in, redirect to the login page
//     next({ name: 'error-unauthorized' });
//   } else {
//     // Continue to the requested route
//     next();
//   }
// });

export default function (app) {
  app.use(router)
}
export { router };
