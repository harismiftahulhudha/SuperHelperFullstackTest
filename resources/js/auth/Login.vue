<template>
    <div class="p-5">
        <div class="container">
            <div class="card shadow-sm col-md-8 offset-md-2 col-sm-12">
                <div class="p-4">
                    <h3 class="mb-4">LOGIN</h3>
                    <form @submit.prevent="login" autocomplete="off">
                        <div class="form-group">
                            <label for="username">Email</label>
                            <input type="text" class="form-control" v-model="email" id="username" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" v-model="password" id="password">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">LOGIN</button>

                        <div class="form-group mt-2">
                            <label class="form-check-label">Don't have account ?</label>
                            <router-link style="color: blue; text-decoration: underline;" :to="{name: 'auth.register' }">Register Here</router-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {EventBus} from '../app'

export default {
    data: function () {
        return {
            token: '',
            email: '',
            password: ''
        }
    },
    created() {
    },
    methods: {
        login() {
            this.$store.commit('setToken', null)
            this.$store.commit('setId', null)
            this.$store.commit('setEmail', null)
            this.$store.commit('setLevel', null)
            this.$cookies.remove('token')
            this.$cookies.remove('id')
            this.$cookies.remove('email')
            this.$cookies.remove('level')
            let loader = this.loading()
            const model = {
                email: this.email,
                password: this.password,
                login_type: 0,
            }
            axios.post('/api/login', model)
                .then((res) => {
                    loader.hide()
                    this.$toastr.success('Welcome Sir', process.env.MIX_APP_NAME)
                    this.$store.commit('setToken', res.data.data.token)
                    this.$store.commit('setId', res.data.data.id)
                    this.$store.commit('setEmail', res.data.data.email)
                    this.$store.commit('setLevel', res.data.data.level)
                    this.$cookies.set('token', 'Bearer ' + res.data.data.token)
                    this.$cookies.set('id', res.data.data.id)
                    this.$cookies.set('email', res.data.data.email)
                    this.$cookies.set('level', res.data.data.level)

                    EventBus.$emit('loggedIn', res.data.data)

                    this.$router.push({name: 'users.list'})
                })
                .catch((err) => {
                    loader.hide()
                    const msg = err.response.data.msg
                    if (msg.email !== undefined) {
                        this.$toastr.error(msg.username[0], 'Email')
                    }
                    if (msg.password !== undefined) {
                        this.$toastr.error(msg.password[0], 'Password')
                    }
                    if (msg.login_type !== undefined) {
                        this.$toastr.error(msg.login_type[0], 'Login Type')
                    }
                })
        },
        loading() {
            let loader = this.$loading.show({
                // Optional parameters
                container: this.$refs.formContainer,
                canCancel: false,
                onCancel: this.onCancel
            });
            return loader;
        },
    },
    mounted() {
        EventBus.$emit('checkMenu', this.$route.path)
    }
}
</script>
