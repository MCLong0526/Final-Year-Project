// import { useAuthStore } from '@/plugins/store/AuthStore';
// import { storeToRefs } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './routes';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})


// router.beforeEach((to, from, next) => {
//   if (to.matched.some(record => record.meta.requiresAuth)) {
//     // Check if the route requires authentication
//     const auth = useAuthStore();
//     if (!auth.isLoggedIn) {
//       // If not authenticated, redirect to the login page
//       next({ name: 'Login' });
//     }
//      else {
//       // If authenticated, check for role-based access
//       const requiresRole = to.meta.requiresRole;
//       const userRole = auth.user.role;
//       if (requiresRole && userRole !== requiresRole) {
//         // If user's role does not match the required role, redirect to unauthorized page
//         next({ name: 'Unauthorized' });
//       } else {
//         // If user's role matches or no role is required, proceed to the route
//         next();
//       }
//     }
//   } else {
//     // If the route does not require authentication, proceed as usual
//     next();
//   }
// });

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
