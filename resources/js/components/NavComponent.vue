<template>
    <div>
        <nav class="navbar fixed-top navbar-expand-md navbar-dark shadow-sm navbar-custom background-blue">
            <a class="navbar-brand" style="cursor: pointer; font-size: 1.66rem;" id="sidebarCollapse" v-bind:class="{ active: url === '/' }" href="#">
                {{ appName }}
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item" v-bind:class="{ active: url === '/' }" v-if="token === null">
                        <router-link class="nav-link" :to="{name: 'auth.login' }">LOGIN</router-link>
                    </li>
                    <li class="nav-item" v-bind:class="{ active: url === '/register' }" v-if="token === null">
                        <router-link class="nav-link" :to="{name: 'auth.register' }">REGISTER</router-link>
                    </li>
                    <li class="nav-item" v-bind:class="{ active: url === '/users/list' }" v-if="token !== null">
                        <router-link class="nav-link" :to="{name: 'users.list' }">USER</router-link>
                    </li>
                    <li class="nav-item" v-bind:class="{ active: url === '/countries/list' }" v-if="token !== null && parseInt(level) === 1">
                        <router-link class="nav-link" :to="{name: 'countries.list' }">COUNTRY</router-link>
                    </li>
                    <li class="nav-item" v-bind:class="{ active: url === '/cities/list' }" v-if="token !== null && parseInt(level) === 1">
                        <router-link class="nav-link" :to="{name: 'cities.list' }">CITY</router-link>
                    </li>
                    <li class="nav-item" v-bind:class="{ active: url === '/profile' }" v-if="token !== null">
                        <router-link class="nav-link" :to="{name: 'profile' }">EDIT PROFIL</router-link>
                    </li>
                    <li class="nav-item" v-if="token !== null">
                        <a class="nav-link" href="#" v-on:click="logout()">LOGOUT</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</template>

<script>
    import { EventBus } from '../app'

    export default {
        data: function () {
            return {
                id: this.$store.getters.id,
                email: this.$store.getters.email,
                level: this.$store.getters.level,
                token: this.$store.getters.token,
                url: this.$route.path,
                unAuthorize: false,
                appName:  process.env.MIX_APP_NAME,
            }
        },
        created() {
        },
        methods: {
            onLoggedIn() {
                EventBus.$on('loggedIn', data => {
                    this.unAuthorize = false
                    this.id = this.$store.getters.id
                    this.email = this.$store.getters.email
                    this.level = this.$store.getters.level
                    this.token = this.$store.getters.token
                })
            },
            onCheckMenu() {
                EventBus.$on('checkMenu', data => {
                    this.url = data
                })
            },
            onUnAuthorize() {
                EventBus.$on('unAuthorization', data => {
                    if (!this.unAuthorize) {
                        this.unAuthorize = data
                        this.logout('unAuthorize')
                    }
                })
            },
            logout(type = 'default') {
                let loader = this.loading()
                var headers = {
                    Authorization: this.token,
                    'login-type': 0
                }
                axios.get('/api/logout', {headers})
                    .then((res) => {
                        loader.hide()
                        this.token = null
                        this.id = null
                        this.email = null
                        this.level = null
                        this.$toastr.success('Selamat Tinggal Tuan', process.env.MIX_APP_NAME)

                        this.$store.commit('setToken', null)
                        this.$store.commit('setId', null)
                        this.$store.commit('setEmail', null)
                        this.$store.commit('setLevel', null)
                        this.$cookies.remove('token')
                        this.$cookies.remove('id')
                        this.$cookies.remove('email')
                        this.$cookies.remove('level')

                        this.$router.push({ name: 'auth.login' })
                    })
                    .catch((err) => {
                        loader.hide()
                        this.token = null
                        this.id = null
                        this.email = null
                        this.level = null
                        this.$toastr.error('Token Tidak Berlaku !', process.env.MIX_APP_NAME)

                        this.$store.commit('setToken', null)
                        this.$store.commit('setId', null)
                        this.$store.commit('setEmail', null)
                        this.$store.commit('setLevel', null)
                        this.$cookies.remove('token')
                        this.$cookies.remove('id')
                        this.$cookies.remove('email')
                        this.$cookies.remove('level')

                        this.$router.push({ name: 'auth.login' })
                    })
            },
            loading() {
                let loader = this.$loading.show({
                    // Optional parameters
                    container: this.$refs.formContainer,
                    canCancel: false,
                    onCancel: this.onCancel,
                });
                return loader;
            }
        },
        mounted() {
            if (this.token === undefined) {
                this.token = null
            }
            this.onLoggedIn()
            this.onUnAuthorize()
            this.onCheckMenu()
        }
    }
</script>
