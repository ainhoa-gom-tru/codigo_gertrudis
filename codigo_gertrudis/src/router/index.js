import Conocenos from '@/views/Conocenos.vue'
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Tienda from '@/views/Tienda.vue'
import Contacto from '@/views/Contacto.vue'
import Companera from '@/views/Companera.vue'
import Login from '@/views/Login.vue'
import PolíticasPrivacidad from '@/views/PolíticasPrivacidad.vue'
import Registrarse from '@/views/Registrarse.vue'
import AnadirProducto from '@/views/Admin/AnadirProducto.vue'
import DetallerUsuario from '@/views/Admin/DetallerUsuario.vue'
import GestiónUsuarios from '@/views/Admin/GestiónUsuarios.vue'
import EligeAvatar from '@/views/EligeAvatar.vue'
import GestiónProductos from '@/views/Admin/GestiónProductos.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/conocenos',
      name: 'conócenos',
      component: Conocenos,
    },
    {
      path: '/tienda',
      name: 'tienda',
      component: Tienda,
    },
    {
      path: '/companera/:nombre',
      name: 'companera',
      component: Companera
    },
    {
      path: '/contacto',
      name: 'contacto',
      component: Contacto
    },
    {
      path: '/login',
      name: 'iniciar sesion',
      meta: { hideHeader: true, hideFooter: true },
      component: Login
    },
    {
      path: '/registrarse',
      name: 'registrarse',
      meta: { hideHeader: true, hideFooter: true },
      component: Registrarse
    },
    {
      path: '/cambiar-avatar',
      name: 'Cambia tu avatar',
      meta: { hideHeader: true, hideFooter: true, hideNavBar: true, hideHeaderAdmin: true},
      component: EligeAvatar
    },
    {
      path: '/gestion-usuarios',
      name: 'gestion de usuarios',
      component: GestiónUsuarios
    },
    {
      path: '/detalles/:usuario',
      name: 'detalles usuario',
      component: DetallerUsuario
    },
    {
      path: '/nuevo-producto',
      name: 'nuevo producto',
      component: AnadirProducto
    },
    {
      path: '/gestion-productos',
      name: 'gestion de productos',
      component: GestiónProductos
    },
    {
      path: '/politicas-privacidad',
      name: 'políticas de privacidad',
      component: PolíticasPrivacidad
    },
  ]
})

export default router