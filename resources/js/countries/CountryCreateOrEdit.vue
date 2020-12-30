<template>
    <div class="p-5">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">{{ title }} Country</h3>

                <form @submit.prevent="storeData()" autocomplete="off">
                    <div class="form-group">
                        <label for="inputCountry">Name</label>
                        <input id="inputCountry" type="text" class="form-control" v-model="model.name">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">SIMPAN</button>
                        <button type="button" class="btn btn-danger" v-on:click="back()">BATAL</button>
                    </div>
                </form>
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
                id: this.$route.query.id,
                model: {},
                title: '',
            }
        },
        methods: {
            getData() {
                const headers = this.headers
                axios.get('/api/countries/' + this.id, {headers})
                    .then((res) => {
                        this.model = res.data.data
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            storeData() {
                const headers = this.headers
                let loader = this.loading()
                let formData = new FormData()

                formData.append('name', this.model.name)

                var url = '/api/countries'
                if (this.id !== undefined) {
                    url = '/api/countries/' + this.model.id
                    formData.append('_method', 'PUT')
                }
                axios.post(url, formData, {headers})
                    .then((res) => {
                        if (this.id !== undefined) {
                            toastr.success('Successfully edited country', process.env.MIX_APP_NAME)
                        } else {
                            toastr.success('Successfully created country', process.env.MIX_APP_NAME)
                        }
                        loader.hide()
                        this.$router.go(-1)
                    })
                    .catch((err) => {
                        loader.hide()
                        unAuthorize(err)
                        var msg = err.response.data.msg
                        if (msg.name !== undefined) {
                            toastr.error(msg.name[0], 'Validation')
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
            back() {
                this.$router.go(-1)
            }
        },
        mounted() {
            if (this.id !== undefined) {
                this.title = 'Edit'
                this.getData()
            } else {
                this.title = 'Create'
            }
        }
    }
</script>
