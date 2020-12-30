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

const guardAdminAuthenticated = (to, from, next) => {
    if (store.getters.token !== null && parseInt(store.getters.level) === 0) {
        toastr.error('Access is denied because you are not admin', process.env.MIX_APP_NAME)
        next({ path: '/users/list' })
    } else {
        next()
    }
}

import Login from './auth/Login'
import UserList from './users/UserList'
import UserView from './users/UserView'
import UserCreateOrEdit from './users/UserCreateOrEdit'
import CountryList from './countries/CountryList'
import CountryCreateOrEdit from './countries/CountryCreateOrEdit'
import CityList from './cities/CityList'
import CityCreateOrEdit from './cities/CityCreateOrEdit'
import Profile from './profile/Profile'

const routes = [
    {path: '/', name: 'auth.login', component: Login, beforeEnter: guardUserAuthenticated},
    {path: '/users/list', name: 'users.list', component: UserList, beforeEnter: guardAuthenticated},
    {path: '/users/view/:id', name: 'users.view', component: UserView, beforeEnter: guardAuthenticated},
    {path: '/users/createOrEdit', name: 'users.createOrEdit', component: UserCreateOrEdit, beforeEnter: guardAuthenticated},

    {path: '/countries/list', name: 'countries.list', component: CountryList, beforeEnter: guardAdminAuthenticated},
    {path: '/countries/createOrEdit', name: 'countries.createOrEdit', component: CountryCreateOrEdit, beforeEnter: guardAdminAuthenticated},

    {path: '/cities/list', name: 'cities.list', component: CityList, beforeEnter: guardAdminAuthenticated},
    {path: '/cities/createOrEdit', name: 'cities.createOrEdit', component: CityCreateOrEdit, beforeEnter: guardAdminAuthenticated},

    {path: '/profile', name: 'profile', component: Profile, beforeEnter: guardAuthenticated},
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
