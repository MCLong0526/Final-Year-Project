
import axios from 'axios';
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './routes';
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

//check whether the user is admin or not
router.beforeEach(async (to, from, next) => {
  const isAuthenticated = true; // Replace with your authentication logic
  let isAdmin = false;
  let isSeller = false;
  let isBuyer = false;

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      return next('/error-unauthorized');
    }

    const token = localStorage.getItem('token');
    if (!token) {
      return next('/login');
    }

    try {
      const response = await axios.get('/api/auth/get-user-by-token', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      isAdmin = response.data.user.roles.some(role => role.name === 'Admin');
      isSeller = response.data.user.roles.some(role => role.name === 'Seller');
      isBuyer = response.data.user.roles.some(role => role.name === 'Buyer');
    } catch (error) {
      console.error('Error fetching user:', error);
      return next('/error-unauthorized');
    }

    if (to.matched.some(record => record.meta.requiresAdmin) && !isAdmin) {
      return next('/error-unauthorized');
    }

    if (to.path === '/dashboard' && !isBuyer) {
      return next('/error-unauthorized');
    }
    
  }

  next();
});

export default function (app) {
  app.use(router)
}
export { router };
