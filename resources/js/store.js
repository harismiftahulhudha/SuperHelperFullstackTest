import Vue from 'vue'
import Vuex from 'vuex'
import VueCookies from 'vue-cookies'
Vue.use(Vuex)
export default new Vuex.Store({
    state: {
        token: VueCookies.get('token'),
        id: VueCookies.get('id'),
        email: VueCookies.get('email'),
        level: VueCookies.get('level'),
    },
    mutations: {
        setToken(state, payload) {
            if (payload === null) {
                state.token = null
            } else {
                state.token = 'Bearer ' + payload
            }
        },
        setId(state, payload) {
            if (payload === null) {
                state.id = null
            } else {
                state.id = payload
            }
        },
        setEmail(state, payload) {
            if (payload === null) {
                state.email = null
            } else {
                state.email = payload
            }
        },
        setLevel(state, payload) {
            if (payload === null) {
                state.level = null
            } else {
                state.level = payload
            }
        },
    },
    actions: {
        //
    },
    getters: {
        token(state) {
            return state.token
        },
        id(state) {
            return state.id
        },
        email(state) {
            return state.email
        },
        level(state) {
            return state.level
        },
    }
})
