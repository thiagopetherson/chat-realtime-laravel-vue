// Configurações do Vuex
import Vuex from 'vuex'
import createPersistedState from "vuex-persistedstate"
import axios from 'axios'

export default new Vuex.Store({
    state: {
        user: {},
        dark: false
    },
    mutations: {
        setUserState: (state, value) => state.user = value,
        setDarkState: (state, value) => state.dark = value
    },
    actions: {
        userStateAction: (context) => {
            axios.get('api/users/me').then(response => {
                const userResponse = response.data.user
                context.commit('setUserState', userResponse) // Disparando e chamando a Mutation
            })
        },
        darkStateAction: (context, value) => {
            context.commit('setDarkState', value.value) // Disparando e chamando a Mutation
            console.log(value.value)
        }
    },
    getters: {
        getDark: (state) => {
            return state.dark;
        }
    },
    plugins:[createPersistedState()]
})
