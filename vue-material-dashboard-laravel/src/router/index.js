import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import SignIn from "../views/SignIn.vue";
import SignUp from "../views/SignUp.vue";
import Login from "../views/examples-api/Login.vue";
import Signup from "../views/examples-api/Signup.vue";
import Customers from "@/views/examples-api/Customers.vue";
import Policies from "@/views/examples-api/Policies.vue";
import CreatePolicy from "@/views/examples-api/CreatePolicy.vue";
import CreateCustomer from "@/views/examples-api/CreateCustomer.vue";

const routes = [
  {
    path: "/",
    name: "/",
    redirect: "/login",
  },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: "/sign-in",
    name: "SignIn",
    component: SignIn,
  },
  {
    path: "/signup",
    name: "Signup",
    component: Signup,
  },
  {
    path: "/sign-up",
    name: "SignUp",
    component: SignUp,
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/customers",
    name: "Customers",
    component: Customers,
    meta: { requiresAuth: true }, // Protect this route
  },
  {
    path: "/customers/create",
    name: "customer-create",
    component: CreateCustomer,
    meta: { requiresAuth: true }, // Protect this route
  },
  {
    path: "/edit/customer/:customerId",
    name: "edit-customer",
    component: CreateCustomer,
    props: (route) => ({ customerId: parseInt(route.params.customerId) || null }),
    meta: { requiresAuth: true }, // Protect this route
  },
  {
    path: "/policies",
    name: "Policies",
    component: Policies,
    meta: { requiresAuth: true }, // Protect this route
  },
  {
    path: "/policies/update/:policyId?",
    name: "policy-edit",
    component: CreatePolicy,
    props: (route) => ({ policyId: parseInt(route.params.policyId) || null }),
    meta: { requiresAuth: true }, // Protect this route
  },
  {
    path: "/policies/create",
    name: "policy-create",
    component: CreatePolicy,
    meta: { requiresAuth: true }, // Protect this route
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

// Add navigation guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!JSON.parse(localStorage.getItem('user_free'));
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ path: "/login" });
  } else {
    next();
  }
});

export default router;
