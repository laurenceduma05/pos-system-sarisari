<script setup>
    import axios from 'axios';
    import { onMounted, ref, computed } from 'vue';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';
    import { formatDate } from '../../helper.js';
 

    const startDate = ref(null);  // Start date filter
    const endDate = ref(null);    // End date filter

    // const cheques = ref({data:[]});
    const quotas = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = 7;
    const selectedStatus = ref();

    const getQuotas = (status, page = 1) => {
        selectedStatus.value = status;

        const params = {
            per_page: perPage,
            page: page,
        };

        if (status) {
            params.status = status;
        }

        if (startDate.value) {
            params.start_date = startDate.value;
        }

        if (endDate.value) {
            params.end_date = endDate.value;
        }

        axios.get('/quotas', {
            params: params,
        })
        .then((response) => {
            quotas.value = response.data['quotas'];
            quotas.value.current_page = response.data.pagination.current_page;
            quotas.value.last_page = response.data.pagination.last_page;
            quotas.value.total = response.data.pagination.total;
        })
    }

    const changePage = (page) => {
        if (page < 1 || page > quotas.value.last_page) return; // Prevent invalid page numbers
        quotas.value.current_page = page;
        getQuotas(selectedStatus.value, page);
    };

    const pageNumbers = computed(() => {
        const pages = [];
        const totalPages = quotas.value.last_page;
        const currentPage = quotas.value.current_page;
        const range = 2; // Number of pages to display before and after the current page
    
        for (let i = currentPage - range; i <= currentPage + range; i++) {
        if (i > 0 && i <= totalPages) pages.push(i);
        }
        return pages;
    });

    const deleteQuota = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                axios.delete(`/quotas/${id}`)
                .then((response) => {
                    quotas.value.data = quotas.value.data.filter(cheque => cheque.id !== id);

                    Swal.fire(
                        'Deleted!',
                        'Record has been deleted.',
                        'success'
                    )
                })
            }
        })
    }

    onMounted(() => {
        getQuotas();
    })
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quotas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Quotas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <router-link to="/admin/quotas/create">
                                <button class="btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> Add New Quota</button>
                            </router-link>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Daily Quota Limit</th>
                                        <th scope="col">Issued</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Day Start</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(quota, index) in quotas.data" :key="quota.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ formatAmount(quota.quota_limit) }}</td>
                                        <td>{{ formatAmount(quota.issued) }}</td>
                                        <!-- <td>{{ formatDate(quota.week_start) }}</td> -->
                                        <!-- <td>
                                            <span class="badge" :class="`badge-${statusColor(quota.status)}`">{{ appointmentStatusName(quota.status) }}</span>
                                        </td> -->
                                        <td>{{ formatDate(quota.created_at) }}</td>
                                        <td>{{ formatDate(quota.day_start) }}</td>
                                        <td>
                                            <router-link :to="`/admin/quotas/${quota.id}/edit`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>

                                            <a @click="deleteQuota(quota.id)" href="#">
                                                <i class="fa fa-trash text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pagination-info d-flex justify-content-between align-items-center mt-2">
                                <!-- Showing X to Y of Z Results -->
                                <span>Showing {{ (quotas.current_page - 1) * perPage + 1 }} to 
                                    {{ quotas.current_page * perPage < quotas.total ? quotas.current_page * perPage : quotas.total }} 
                                    of {{ quotas.total }} results</span>
                                
                                <!-- Page Links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: quotas.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: quotas.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(quotas.current_page - 1)">Prev</a>
                                        </li>
                                        
                                        <!-- Dynamically generate page numbers -->
                                        <li v-for="page in pageNumbers" :key="page" class="page-item" :class="{ active: page === quotas.current_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: quotas.current_page === quotas.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(quotas.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: quotas.current_page === quotas.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(quotas.last_page)">Last</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
