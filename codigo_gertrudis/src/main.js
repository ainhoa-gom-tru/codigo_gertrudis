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
  color : "#8b2c2c",
}
//no hay errores
export const estiloVerde = {
  color : "#19916a"
}
//no hay nada escrito
export const estiloNatural = {
    color: "#ff8800"
}

//ahora hacemos lo mismo pero para los bordes de los inputs
//hay errores
export const bordeRojo = {
    border: "2px solid #8b2c2c"
}
//no hya errores
export const bordeVerde = {
    border: "2px solid #19916a"
}
//no hay nada escrito
export const bordeNatural = {
    border: "2px solid #fcbf00"
}