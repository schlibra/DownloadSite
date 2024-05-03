import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import List from "@/views/List.vue";
import File from "@/views/File.vue";
import Login from "@/views/Login.vue";
import Register from "@/views/Register.vue";
import User from "@/views/User.vue";
import Upload from "@/views/Upload.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/list',
      name: 'list',
      component: List
    },
    {
      path: '/file',
      name: 'file',
      component: File
    },
    {
      path: '/s/:id',
      name: "share",
      component: File
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    },
    {
      path: '/user',
      name: 'user',
      component: User
    },
    {
      path: "/file/upload",
      name: "upload",
      component: Upload
    }
  ]
})

export default router
