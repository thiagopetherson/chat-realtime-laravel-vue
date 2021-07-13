// Configurações do Vuex
import Vuex from 'vuex'

export default new Vuex.Store({
    state: {
        user: {}
    },
    mutations: {

    },
    actions: {
        userStateAction: () => {
            console.log('invocado')
        }
    }
})
