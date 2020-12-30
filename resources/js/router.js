import Vue from 'vue'
import Router from 'vue-router'
import store from './store'
import toastr from 'toastr'

Vue.use(Router)

const guardAuthenticated = (to, from, next) => {
    if (store.getters.token === null) {
        toastr.error('Access Denied', process.env.MIX_APP_NAME)
        next({ path: '/' })
    } else {
        next()
    }
}
const guardUserAuthenticated = (to, from, next) => {
    if (store.getters.token !== null) {
        toastr.error('Access is denied because you are logged in', process.env.MIX_APP_NAME)
        next({ path: '/users/list' })
    } else {
        next()
    }
}

import Login from './auth/Login'
import UserList from './users/UserList'
import UserView from './users/UserView'
import UserCreateOrEdit from './users/UserCreateOrEdit'

const routes = [
    {path: '/', name: 'auth.login', component: Login, beforeEnter: guardUserAuthenticated},
    {path: '/users/list', name: 'users.list', component: UserList, beforeEnter: guardAuthenticated},
    {path: '/users/view/:id', name: 'users.view', component: UserView, beforeEnter: guardAuthenticated},
    {path: '/users/createOrEdit', name: 'users.createOrEdit', component: UserCreateOrEdit, beforeEnter: guardAuthenticated},
]

export default new Router({
    mode: 'history',
    routes,
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
})
