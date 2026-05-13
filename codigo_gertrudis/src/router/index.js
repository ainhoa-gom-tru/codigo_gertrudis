import Conocenos from '@/views/Conocenos.vue'
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Tienda from '@/views/Tienda.vue'
import Contacto from '@/views/Contacto.vue'
import Companera from '@/views/Companera.vue'
import Login from '@/views/Login.vue'
import Registrarse from '@/views/Registrarse.vue'
import AnadirProducto from '@/views/Admin/AnadirProducto.vue'
import DetallerUsuario from '@/views/Admin/DetallerUsuario.vue'
import GestiónUsuarios from '@/views/Admin/GestiónUsuarios.vue'
import GestiónProductos from '@/views/Admin/GestiónProductos.vue'
import PolíticasPrivacidad from '@/views/Políticas/PolíticasPrivacidad.vue'
import PolíticasCookies from '@/views/Políticas/PolíticasCookies.vue'
import TerminosCondiciones from '@/views/Políticas/TerminosCondiciones.vue'
import Carrito from '@/views/Cliente/Carrito.vue'
import PasarelaPago from '@/views/Cliente/PasarelaPago.vue'
import PanelUsuario from '@/views/Cliente/PanelUsuario.vue'
import Pedidos from '@/views/Cliente/Pedidos.vue'
import AnadirBlog from '@/views/Compis/AnadirBlog.vue'
import Blog from '@/views/Cliente/Blog.vue'
import DetallesEntrada from '@/views/Cliente/DetallesEntrada.vue'
import DetallesProductos from '@/views/DetallesProductos.vue'
import AnadirJuego from '@/views/Desarrollador/AnadirJuego.vue'
import Juegos from '@/views/Cliente/Juegos.vue'
import Juego from '@/views/Cliente/Juego.vue'
import GestionJuegos from '@/views/Desarrollador/GestionJuegos.vue'
import EligeAvatar from '@/views/EligeAvatar.vue'
import Galeria from '@/views/Compis/Galeria.vue'

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
      meta: { hideHeaderAdmin: true, hideNavBar: true, hideNavBarAdmin: true},
      component: HomeView,
    },
    {
      path: '/conocenos',
      name: 'conócenos',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Conocenos,
    },
    {
      path: '/tienda',
      name: 'tienda',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Tienda,
    },
    {
      path: '/companera/:nombre',
      name: 'companera',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Companera
    },
    {
      path: '/contacto',
      name: 'contacto',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Contacto
    },
    {
      path: '/login',
      name: 'iniciar sesion',
      meta: { hideHeader: true, hideFooter: true, hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Login
    },
    {
      path: '/registrarse',
      name: 'registrarse',
      meta: { hideHeader: true, hideFooter: true, hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Registrarse
    },
    {
      path: '/cambiar-avatar',
      name: 'Cambia tu avatar',
      meta: { hideHeader: true, hideFooter: true, hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true, requiresAuth: true},
      component: EligeAvatar
    },
    {
      path: '/gestion-usuarios',
      name: 'gestion de usuarios',
      meta: { hideNavBar: true, hideHeader: true, requiresAuth: true, roles: ['admin'] },
      component: GestiónUsuarios
    },
    {
      path: '/detalles/:usuario',
      name: 'detalles usuario',
      meta: { hideNavBar: true, hideHeader: true, requiresAuth: true, roles: ['admin']},
      component: DetallerUsuario
    },
    {
      path: '/nuevo-producto',
      name: 'nuevo producto',
      meta: { hideNavBar: true, hideHeader: true, requiresAuth: true, roles: ['admin']},
      component: AnadirProducto
    },
    {
      path: '/detalles-producto/:nombre',
      name: 'detalles del producto',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: DetallesProductos
    },
    {
      path: '/gestion-productos',
      name: 'gestion de productos',
      meta: { hideNavBar: true, hideHeader: true, requiresAuth: true, roles: ['admin']},
      component: GestiónProductos
    },
    {
      path: '/carrito',
      name: 'carrito',
      meta: { hideNavBarAdmin: true, hideHeaderAdmin: true, hideHeader: true, hideFooter: true, requiresAuth: true},
      component: Carrito
    },
    {
      path: '/pasarela-pago',
      name: 'pasarela de pago',
      meta: { hideNavBar: true, hideHeader: true, hideFooter: true, hideNavBarAdmin: true, hideHeaderAdmin: true, requiresAuth: true},
      component: PasarelaPago
    },
    {
      path: '/panel-usuario/:usuario',
      name: 'panel del usuario',
      meta: { hideHeader: true, hideFooter: true, hideNavBarAdmin: true, hideHeaderAdmin: true, requiresAuth: true},
      component: PanelUsuario
    },
    {
      path: '/pedidos',
      name: 'pedidos',
      meta: { hideHeader: true, hideFooter: true, hideNavBarAdmin: true, hideHeaderAdmin: true, requiresAuth: true},
      component: Pedidos
    },
    {
      path: '/galeria',
      name: 'galería de fotos',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true, requiresAuth: true, roles: ['compi']},
      component: Galeria
    },
    {
      path: '/anadir-entrada-blog',
      name: 'Añadir entrada de blog',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true, requiresAuth: true, roles: ['compi']},
      component: AnadirBlog
    },
    {
      path: '/blog',
      name: 'Blog',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Blog
    },
    {
      path: '/detalles-entrada/:titulo',
      name: 'detalles de la entrada de blog',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: DetallesEntrada
    },
    {
      path: '/anadir-juego',
      name: 'añadir juego',
      meta: { hideNavBar: true, hideHeader: true, requiresAuth: true, roles: ['desarrollador']},
      component: AnadirJuego
    },
    {
      path: '/juegos',
      name: 'juegos',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Juegos
    },
    {
      path: '/juego/:nombre',
      name: 'juego',
      meta: { hideNavBar: true, hideNavBarAdmin: true, hideHeaderAdmin: true,},
      component: Juego
    },
    {
      path: '/gestion-juegos',
      name: 'gestión de juegos',
      meta: { hideNavBar: true, hideHeader: true, requiresAuth: true, roles: ['desarrollador']},
      component: GestionJuegos
    },
    {
      path: '/politicas-privacidad',
      name: 'políticas de privacidad',
      meta: { hideNavBar: true, hideNavBarAdmin: true},
      component: PolíticasPrivacidad
    },
    {
      path: '/politicas-cookies',
      name: 'políticas de cookies',
      meta: { hideNavBar: true, hideNavBarAdmin: true},
      component: PolíticasCookies
    },
    {
      path: '/terminos-condiciones',
      name: 'términos y condiciones',
      meta: { hideNavBar: true, hideNavBarAdmin: true},
      component: TerminosCondiciones
    },
  ]
})

//obtener el rol para limitar las rutas
router.beforeEach((to, from, next) => {
  //obtenemos el usuario logueado del localsstorage
  const user = JSON.parse(localStorage.getItem('user'))
  const estaLogueado = !!user

  const requiereRoles = to.meta.roles
  const requiereAutorizacion = to.meta.requiresAuth

  // si no requiere autorizacion, que lo deje "pasar"
  if (!requiereAutorizacion) {
    return next()
  }

  // requiere autorización y no está logueado
  if (requiereAutorizacion && !estaLogueado) {
    return next('/login')
  }

  // Si hay roles requeridos
  if (requiereRoles) {
    if (!user || !requiereRoles.includes(user.rol)) {
      return next('/')
    }
  }

  next()
})

export default router