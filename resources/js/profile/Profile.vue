<template>
    <div class="p-5">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Profile</h3>

                <form @submit.prevent="storeData()" autocomplete="off">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" ref="file" class="custom-file-input" id="file-path" v-on:change="handleFileUpload()">
                                        <label class="custom-file-label" for="file-path">{{ dataFile }}</label>
                                    </div>
                                    <div class="input-group-append" v-if="dataFile !== ''">
                                        <button class="btn btn-outline-danger" type="button" v-on:click="deleteImage()">Delete</button>
                                    </div>
                                </div>
                                <img v-if="model.photo !== null" :src="previewImage" class="img-fluid mt-1" alt="User Image">
                            </div>
                        </div>
                        <div class="col-md-8">
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
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inputBirthday">Birthday</label>
                                        <input id="inputBirthday" type="date" class="form-control" v-model="model.birthday" required>
                                    </div>
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
                                        <input id="inputPassword" type="password" class="form-control" v-model="model.password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-md-6 col-md-6">
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-success font-1">SAVE</button>
                                        <button type="button" class="btn btn-danger font-1" v-on:click="back()">CANCEL</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                headers: {
                    Authorization: this.$cookies.get('token'),
                    'login-type': 0
                },
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
                previewImage: null
            }
        },
        methods: {
            getData() {
                const headers = this.headers
                axios.get('/api/profile', {headers})
                    .then((res) => {
                        this.model = res.data.data
                        if (this.model.photo !== null) {
                            this.temporaryFile = this.model.photo
                            this.dataFile = this.model.photo
                            this.previewImage = '/storage/' + this.model.photo
                            this.is_delete_photo = 0
                        } else {
                            this.is_delete_photo = 1
                        }
                        this.model.password = null
                        this.getCountries()
                        this.getCities()
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            getCountries() {
                const headers = this.headers
                const params = {
                    noPagination: 1
                }
                axios.get('/api/countries', {params, headers})
                    .then((res) => {
                        this.countries = res.data.data
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            getCities() {
                const headers = this.headers
                const params = {
                    noPagination: 1
                }
                axios.get('/api/cities', {params, headers})
                    .then((res) => {
                        this.cities = res.data.data
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            storeData() {
                const headers = this.headers
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

                if (this.model.photo !== undefined && this.model.photo !== null && this.model.photo !== '') {
                    if (this.temporaryFile === null || this.temporaryFile !== this.model.photo) {
                        formData.append('photo', this.model.photo)
                    }
                }

                if (this.model.photo !== undefined && this.model.password !== null && this.model.password !== '') {
                    formData.append('password', this.model.password)
                }

                var url = '/api/profile/update'
                formData.append('_method', 'PUT')

                axios.post(url, formData, {headers})
                    .then((res) => {
                        toastr.success('Successfully updated profile', process.env.MIX_APP_NAME)
                        loader.hide()
                        this.$router.go(-1)
                    })
                    .catch((err) => {
                        loader.hide()
                        unAuthorize(err)
                        var msg = err.response.data.msg
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
            this.getData()
            EventBus.$emit('checkMenu', this.$route.path)
        }
    }
</script>
