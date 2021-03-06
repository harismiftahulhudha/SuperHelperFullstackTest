<template>
    <div class="p-5">
        <div class="row mb-4">
            <div class="col-md-6">
                <h3>List City</h3>
            </div>
            <div class="col-md-6">
                <router-link :to="{ name: 'cities.createOrEdit' }" class="btn btn-primary float-md-right" title="Add New Account">
                    Add New City
                </router-link>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <form @submit.prevent="searchData()" autocomplete="off">
                    <div class="form-group">
                        <label for="query">Name</label>
                        <input id="query" type="text" class="form-control" v-model="query">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">SEARCH</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">NAME</th>
                            <th scope="col">COUNTRY</th>
                            <th scope="col">ACTION</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="!isLoaded">
                            <td colspan="3">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border mr-3" role="status" aria-hidden="true"></div>
                                    <strong>Loading...</strong>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="city, index in data" v-else>
                            <td>{{ city.name }}</td>
                            <td>{{ city.country_name }}</td>
                            <td>
                                <router-link class="btn btn-outline-info btn-sm" :to="{name: 'cities.createOrEdit', query: {id: city.id}}" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </router-link>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modalDeleteCity" v-on:click="getModel(index)" title="Delete">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row" style="justify-content: center;" v-if="pagination.lastPage > 1">
                    <nav aria-label="PaginationData">
                        <ul class="pagination">
                            <li class="page-item" v-bind:class="{disabled: pagination.currentPage === pagination.firstPage}">
                                <a class="page-link" v-on:click="getPagination(pagination.firstPage)" aria-label="Previous">
                                    <i class="fa fa-step-backward"></i>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="{disabled: pagination.currentPage === pagination.firstPage}">
                                <a class="page-link" v-on:click="getPagination(pagination.prevPage)" aria-label="Previous">
                                    <i class="fa fa-backward"></i>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="{ active: pagination.currentPage === page }" v-for="page in pagination.pages">
                                <a class="page-link" v-on:click="getPagination(page)">{{ page }}</a>
                            </li>
                            <li class="page-item" v-bind:class="{disabled: pagination.currentPage === pagination.lastPage}">
                                <a class="page-link" v-on:click="getPagination(pagination.nextPage)" aria-label="Next">
                                    <i class="fa fa-forward"></i>
                                </a>
                            </li>
                            <li class="page-item" v-bind:class="{disabled: pagination.currentPage === pagination.lastPage}">
                                <a class="page-link" v-on:click="getPagination(pagination.lastPage)" aria-label="Next">
                                    <i class="fa fa-step-forward"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDeleteCity" tabindex="-1" role="dialog" aria-labelledby="modalDeleteCityLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeleteCityLabel">
                            DELETE CITY
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding-top: 0; padding-bottom: 0;">
                        <p class="text-center">
                            <br>
                            Are you sure want to delete <strong>{{ city.name }}</strong> ?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" v-on:click="deleteCity()" data-dismiss="modal">YES</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../app'
    import { paginate } from '../pagination'
    import { unAuthorize } from '../unauthorize'

    export default {
        data: function () {
            return {
                level: this.$store.getters.level,
                pagination: {
                    currentPage: 1,
                    firstPage: 1,
                    lastPage: 0,
                    nextPage: 0,
                    prevPage: 0,
                    offsetPage: 0,
                    pages: []
                },
                headers: {
                    Authorization: this.$cookies.get('token'),
                    'login-type': 0
                },
                index: -1,
                city: {},
                data: [],
                query: this.$route.query.query,
                page: this.$route.query.page,
                isLoaded: false
            }
        },
        beforeRouteUpdate(to, from, next) {
            this.query = to.query.query
            this.page = to.query.page
            this.getData(false)
            next()
        },
        methods: {
            getPagination(page) {
                const params = {}
                if (this.query !== '') {
                    params.query = this.query
                }
                if (page === 1 || page === '1') {
                    this.page = undefined
                } else {
                    this.page = page
                    params.page = page
                }
                this.$router.push({name: "cities.list", query: params})
            },
            searchData() {
                const params = {}
                if (this.query !== '') {
                    params.query = this.query
                }
                this.$router.push({name: "cities.list", query: params})
            },
            getData() {
                this.isLoaded = false
                const headers = this.headers
                const params = {}
                if (this.page !== undefined) {
                    params.page = this.page
                }
                if (this.query !== undefined) {
                    params.query = this.query
                } else {
                    this.query = ''
                }
                axios.get('/api/cities', {params, headers})
                    .then((res) => {
                        this.data = res.data.data
                        this.isLoaded = true
                        if (this.data.length === 0) {
                            this.resetPagination()
                        } else {
                            this.pagination.currentPage = res.data.current_page
                            this.pagination.lastPage = res.data.last_page
                            if (res.data.prev_page_url !== null) {
                                this.pagination.prevPage = res.data.prev_page_url.split("page=")[1]
                            }
                            if (res.data.next_page_url !== null) {
                                this.pagination.nextPage = res.data.next_page_url.split("page=")[1]
                            }
                            this.pagination.pages = paginate(res.data.total, this.pagination.currentPage, res.data.per_page, 5).pages
                        }
                    })
                    .catch((err) => {
                        unAuthorize(err)
                    })
            },
            resetPagination() {
                this.pagination.currentPage = 1
                this.pagination.firstPage = 1
                this.pagination.lastPage = 0
                this.pagination.nextPage = 0
                this.pagination.prevPage = 0
                this.pagination.offsetPage = 0
                this.pagination.pages = []
            },
            deleteCity() {
                const headers = this.headers
                let loader = this.loading()
                let formData = new FormData()

                formData.append('_method', 'DELETE')
                var url = '/api/cities/' + this.city.id
                axios.post(url, formData, {headers})
                    .then((res) => {
                        toastr.success('Successfully Deleted City', process.env.MIX_APP_NAME)
                        this.data.splice(this.index, 1)
                        this.index = -1
                        loader.hide()
                    })
                    .catch((err) => {
                        loader.hide()
                        unAuthorize(err)
                    })
            },
            getModel(index) {
                this.index = index
                this.city = this.data[index]
            },
            loading() {
                let loader = this.$loading.show({
                    // Optional parameters
                    container: this.$refs.formContainer,
                    canCancel: false,
                    onCancel: this.onCancel
                });
                return loader;
            }
        },
        mounted() {
            this.getData()
            EventBus.$emit('checkMenu', this.$route.path)
        }
    }
</script>
