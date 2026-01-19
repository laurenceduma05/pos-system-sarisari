<script setup>
    import axios from 'axios';
    import { onMounted, ref, computed, watch } from 'vue';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';
    import { formatDate } from '../../helper.js';
    import { getLink } from '../../helper.js';
    import { getBankColor } from '../../helper.js';
 
    const appointmentStatus = ref([]);

    const startDate = ref(null);  // Start date filter
    const endDate = ref(null);    // End date filter

    // const cheques = ref({data:[]});
    const teachings = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = 7;
    const selectedStatus = ref();
    const quotaIssuedToday = ref();
    const quotaIssuedDueToday = ref();
    const totalChequeIssuedToday = ref();
    const checkPayables = ref(false);

    const getTeachings = (status, page = 1) => {
        selectedStatus.value = status;

        const params = {
            per_page: perPage,
            page: page,
        };

        axios.get('/teachings', {
            params: params,
        })
        .then((response) => {
            teachings.value = response.data['teachings'];
            teachings.value.current_page = response.data.pagination.current_page;
            teachings.value.last_page = response.data.pagination.last_page;
            teachings.value.total = response.data.pagination.total;
        })
    }

    const changePage = (page) => {
        if (page < 1 || page > teachings.value.last_page) return; // Prevent invalid page numbers
        teachings.value.current_page = page;
        getTeachings(selectedStatus.value, page);
    };

    const pageNumbers = computed(() => {
        const pages = [];
        const totalPages = teachings.value.last_page;
        const currentPage = teachings.value.current_page;
        const range = 2; // Number of pages to display before and after the current page
    
        for (let i = currentPage - range; i <= currentPage + range; i++) {
        if (i > 0 && i <= totalPages) pages.push(i);
        }
        return pages;
    });

    const clearDateFilters = () => {
        startDate.value = null;
        endDate.value = null;
        getCheques(); // Fetch cheques without date filters
    };

    const deleteTeaching = (id) => {
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

                axios.delete(`/teachings/${id}`)
                .then((response) => {
                    teachings.value.data = teachings.value.data.filter(song => song.id !== id);

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
        getTeachings();
    })

    watch(checkPayables, (newValue) => {
    });

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Teachings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Teachings</li>
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
                            <router-link to="/admin/teachings/create">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New
                                    teaching
                                </button>
                            </router-link>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Teaching Title</th>
                                        <th scope="col">Uploader</th>
                                        <th scope="col">Dscriptions</th>
                                        <th scope="col">Link</th>
                                        <th scope="col">File type</th>
                                        <th scope="col">Audience</th>
                                        <th scope="col">Created at</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(teaching, index) in teachings.data" :key="teaching.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ teaching.title }}</td>
                                        <td>{{ teaching.uploader }}</td>
                                        <td>{{ teaching.descriptions }}</td>
                                        <td>
                                            <a :href="getLink(teaching.link)" target="_blank">{{ teaching.link }}</a>
                                        </td>
                                        <td>{{ teaching.file_type }}</td>
                                        <td>{{ teaching.audience }}</td>
                                        <td>{{ formatDate(teaching.created_at) }}</td>     
                                        <td class="option-icons">
                                            <router-link :to="`/admin/teaching/${teaching.id}/edit`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>

                                            <a @click="deleteCheque(teaching.id)" href="#">
                                                <i class="fa fa-trash text-danger mr-2"></i>
                                            </a>
                                            <router-link :to="`/admin/teachings/view`">
                                                <i class="fa fa-eye"></i>
                                            </router-link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pagination-info d-flex justify-content-between align-items-center mt-2">
                                <!-- Showing X to Y of Z Results -->
                                <span>Showing {{ (teachings.current_page - 1) * perPage + 1 }} to 
                                    {{ teachings.current_page * perPage < teachings.total ? teachings.current_page * perPage : teachings.total }} 
                                    of {{ teachings.total }} results</span>
                                
                                <!-- Page Links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: teachings.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: teachings.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(teachings.current_page - 1)">Prev</a>
                                        </li>
                                        
                                        <!-- Dynamically generate page numbers -->
                                        <li v-for="page in pageNumbers" :key="page" class="page-item" :class="{ active: page === teachings.current_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: teachings.current_page === teachings.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(teachings.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: teachings.current_page === teachings.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(teachings.last_page)">Last</a>
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
<style scoped>
    .quota-container {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        max-width: 400px;
        /* margin: 20px auto; */
        font-family: 'Arial', sans-serif;
        /* box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); */
    }

    .quota-label {
        font-size: 1.2rem;
        color: #333;
        font-weight: bold;
        margin-right: 10px;
    }

    .quota-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #3498db; /* You can change the color here */
    }

    .quota-container:hover {
        background-color: #f0f8ff;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .due-today {
        background-color: #f56762; /* Or any other style you prefer */
        color: white;
    }

    .paid {
        background-color: #4fd172;
        color: white;  /* Optional: Change text color for better visibility */
    }

    .border-group {
        border: 1px solid #ccc; /* You can change the color and thickness */
        padding: 10px;  /* Optional: adds some space inside the border */
        border-radius: 5px;  /* Optional: adds rounded corners */
    }

    .date-filter {
        color: gray;
    }

    .option-icons {
        background-color: #f5eeed;
    }

</style>
