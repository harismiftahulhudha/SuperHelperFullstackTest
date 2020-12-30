<template>
    <div class="p-5">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">REGISTER</h3>

                <form @submit.prevent="storeData()" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputFirstname">Firstname</label>
                                <input id="inputFirstname" type="text" class="form-control" v-model="model.first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="inputLastname">Lastname</label>
                                <input id="inputLastname" type="text" class="form-control" v-model="model.last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="inputPhone">Phone</label>
                                <input id="inputPhone" type="text" class="form-control" v-model="model.phone" required>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input id="inputEmail" type="email" class="form-control" v-model="model.email" required>
                            </div>
                            <div class="form-group">
                                <label for="inputBirthday">Birthday</label>
                                <input id="inputBirthday" type="date" class="form-control" v-model="model.birthday" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select class="form-control" id="country" v-model="model.country_id">
                                    <option value="">Choose Country</option>
                                    <option :value="country.id" v-for="country in countries">{{ country.name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <select class="form-control" id="city" v-model="model.city_id">
                                    <option value="">Choose City</option>
                                    <option :value="city.id" v-for="city in cities">{{ city.name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword">Password</label>
                                <input id="inputPassword" type="password" class="form-control" v-model="model.password" required>
                            </div>
                            <div class="form-group">
                                <label for="inputPasswordConfirmation">Password Confirmation</label>
                                <input id="inputPasswordConfirmation" type="password" class="form-control" v-model="model.password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="padding-left: 20px;">
                        <input type="checkbox" class="form-check-input" id="inputIsAdmin" v-model="isAgree">
                        <label class="form-check-label" for="inputIsAdmin">by signing up your agree to privacy policy & term of service</label>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-block">SIGN UP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../app'
    import { unAuthorize } from '../unauthorize'

    export default {
        data: function () {
            return {
                id: this.$route.query.id,
                datepickerOptions: {
                    format: 'DD-MM-YYYY',
                    useCurrent: false,
                },
                countries: [],
                cities: [],
                model: {
                    photo: null,
                    country_id: '',
                    city_id: '',
                    is_admin: 0,
                    is_delete_photo: 0,
                    password: null,
                    password_confirmation: null
                },
                dataFile: '',
                temporaryFile: null,
                previewImage: null,
                title: '',
                isAgree: false
            }
        },
        methods: {
            getCountries() {
                const params = {
                    noPagination: 1
                }
                axios.get('/api/countries', {params})
                    .then((res) => {
                        this.countries = res.data.data
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            getCities() {
                const params = {
                    noPagination: 1
                }
                axios.get('/api/cities', {params})
                    .then((res) => {
                        this.cities = res.data.data
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            storeData() {
                if (!this.isAgree) {
                    toastr.error('Must agree the policy and term of service !', 'Validation')
                    return
                }
                let loader = this.loading()
                let formData = new FormData()

                formData.append('email', this.model.email)
                formData.append('first_name', this.model.first_name)
                formData.append('last_name', this.model.last_name)
                formData.append('phone', this.model.phone)
                formData.append('birthday', this.model.birthday)
                formData.append('country_id', this.model.country_id)
                formData.append('city_id', this.model.city_id)
                formData.append('is_delete_photo', this.is_delete_photo)
                formData.append('login_type', 0)

                if (this.model.photo !== undefined && this.model.photo !== null && this.model.photo !== '') {
                    if (this.temporaryFile === null || this.temporaryFile !== this.model.photo) {
                        formData.append('photo', this.model.photo)
                    }
                }

                formData.append('password', this.model.password)
                formData.append('password_confirmation', this.model.password_confirmation)


                var url = '/api/register'

                axios.post(url, formData)
                    .then((res) => {
                        loader.hide()

                        this.$toastr.success('Successfully Registered', process.env.MIX_APP_NAME)
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
                        unAuthorize(err)
                        var msg = err.response.data.msg
                        if (msg.login_type !== undefined) {
                            toastr.error(msg.login_type[0], 'Validation')
                        }
                        if (msg.email !== undefined) {
                            toastr.error(msg.email[0], 'Validation')
                        }
                        if (msg.first_name !== undefined) {
                            toastr.error(msg.first_name[0], 'Validation')
                        }
                        if (msg.last_name !== undefined) {
                            toastr.error(msg.last_name[0], 'Validation')
                        }
                        if (msg.phone !== undefined) {
                            toastr.error(msg.phone[0], 'Validation')
                        }
                        if (msg.birthday !== undefined) {
                            toastr.error(msg.birthday[0], 'Validation')
                        }
                        if (msg.country_id !== undefined) {
                            toastr.error(msg.country_id[0], 'Validation')
                        }
                        if (msg.city_id !== undefined) {
                            toastr.error(msg.city_id[0], 'Validation')
                        }
                        if (msg.is_delete_photo !== undefined) {
                            toastr.error(msg.is_delete_photo[0], 'Validation')
                        }
                        if (msg.photo !== undefined) {
                            toastr.error(msg.photo[0], 'Validation')
                        }
                        if (msg.password !== undefined) {
                            toastr.error(msg.password[0], 'Validation')
                        }
                        if (msg.password_confirmation !== undefined) {
                            toastr.error(msg.password_confirmation[0], 'Validation')
                        }
                    })
            },
            deleteImage() {
                this.model.photo = null
                this.dataFile = ''
                this.$refs.file.value= null
                this.is_delete_photo = 1
            },
            handleFileUpload() {
                this.model.photo = this.$refs.file.files[0]
                this.dataFile = this.model.photo.name
                this.is_delete_photo = 0
                this.previewImage = URL.createObjectURL(this.model.photo)
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
            back() {
                this.$router.go(-1)
            }
        },
        mounted() {
            this.getCountries()
            this.getCities()
            EventBus.$emit('checkMenu', this.$route.path)
        }
    }
</script>
