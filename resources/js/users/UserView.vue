<template>
    <div class="p-5">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">View User</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Image</label>
                            <br>
                            <p v-if="model.photo === null"><strong>No Image</strong></p>
                            <img v-if="model.photo !== null" :src="model.photo" class="img-fluid mt-1" alt="User Image">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Firstname</label>
                                    <p><strong>{{ model.first_name }}</strong></p>
                                </div>
                                <div class="form-group">
                                    <label>Lastname</label>
                                    <p><strong>{{ model.last_name }}</strong></p>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <p><strong>{{ model.phone }}</strong></p>
                                </div>
                                <div class="form-group">
                                    <label>Level</label>
                                    <p v-if="parseInt(model.level) === 1"><strong>Administrator</strong></p>
                                    <p v-else><strong>Regular</strong></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <p><strong>{{ model.email }}</strong></p>
                                </div>
                                <div class="form-group">
                                    <label>Birthday</label>
                                    <p><strong>{{ model.format_birthday }}</strong></p>
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <p><strong>{{ model.country_name }}</strong></p>
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <p><strong>{{ model.city_name }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-6 col-md-6">
                                <div class="form-group text-right">
                                    <router-link v-if="parseInt(level) === 1" class="btn btn-success" :to="{name: 'users.createOrEdit', query: {id: id}}" title="Edit">
                                        EDIT
                                    </router-link>
                                    <button v-if="parseInt(level) === 1" type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDeleteUser" title="Delete">
                                        DELETE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDeleteUser" tabindex="-1" role="dialog" aria-labelledby="modalDeleteUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeleteUserLabel">
                            DELETE USER
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding-top: 0; padding-bottom: 0;">
                        <p class="text-center">
                            <br>
                            Are you sure want to delete <strong>{{ model.fullname }}</strong> ?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" v-on:click="deleteUser()" data-dismiss="modal">YES</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { unAuthorize } from '../unauthorize'

    export default {
        data: function () {
            return {
                headers: {
                    Authorization: this.$cookies.get('token'),
                    'login-type': 0
                },
                level: this.$store.getters.level,
                id: this.$route.params.id,
                model: {},
            }
        },
        methods: {
            getData() {
                const headers = this.headers
                axios.get('/api/users/' + this.id, {headers})
                    .then((res) => {
                        this.model = res.data.data
                        if (this.model.photo !== null) {
                            this.model.photo = '/storage/' + this.model.photo
                        }
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            deleteUser() {
                const headers = this.headers
                let loader = this.loading()
                let formData = new FormData()

                formData.append('_method', 'DELETE')
                var url = '/api/users/' + this.id
                axios.post(url, formData, {headers})
                    .then((res) => {
                        toastr.success('Successfully Deleted User', process.env.MIX_APP_NAME)
                        loader.hide()
                        this.$router.go(-1)
                    })
                    .catch((err) => {
                        loader.hide()
                        unAuthorize(err)
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
            back() {
                this.$router.go(-1)
            }
        },
        mounted() {
            this.getData()
        }
    }
</script>
