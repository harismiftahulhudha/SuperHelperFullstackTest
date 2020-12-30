<template>
    <div>
        <nav class="navbar fixed-top navbar-expand-md navbar-dark shadow-sm navbar-custom background-blue">
            <a class="navbar-brand" style="cursor: pointer; font-size: 1.66rem;" id="sidebarCollapse" v-bind:class="{ active: url === '/' }" href="#">
                {{ appName }}
            </a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item font-gilroy-bold font-0" v-bind:class="{ active: url === '/' }" v-if="token === null">
                        <router-link class="nav-link" :to="{name: 'auth.login' }">LOGIN</router-link>
                    </li>
                    <li class="nav-item ont-gilroy-bold font-0" v-if="token !== null">
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
        }
    }
</script>
