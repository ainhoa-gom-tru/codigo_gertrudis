import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(router)

app.mount('#app')

/*creamos y exportamos una variable con la api para no 
tener que estar escribiendola entera en cada peticion*/
export const ApiUrl = "http://localhost:8001/api.php/";

/**
 * declaramos tres variables para poner tres colores distintos en función de
 * si hay errores, no los hay, o no hay anda escrito
 */
//hay errores
export const estiloRojo = {
  color : "red",
}
//no hay errores
export const estiloVerde = {
  color : "green"
}
//no hay nada escrito
export const estiloNatural = {
    color: "#fcbf00"
}

//ahora hacemos lo mismo pero para los bordes de los inputs
//hay errores
export const bordeRojo = {
    border: "2px solid red"
}
//no hya errores
export const bordeVerde = {
    border: "2px solid green"
}
//no hay nada escrito
export const bordeNatural = {
    border: "2px solid #fcbf00"
}