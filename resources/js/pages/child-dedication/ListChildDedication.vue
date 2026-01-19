<script setup>
    import axios from 'axios';
    import { onMounted, ref, computed, watch } from 'vue';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';
    import { formatDate } from '../../helper.js';
    import { getLink } from '../../helper.js';
    import { getBankColor, formatSponsors, capitalizeFirstLetter } from '../../helper.js';
 
    const appointmentStatus = ref([]);

    const startDate = ref(null);  // Start date filter
    const endDate = ref(null);    // End date filter

    // const cheques = ref({data:[]});
    const dedications = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = 7;
    const selectedStatus = ref();
    const quotaIssuedToday = ref();
    const quotaIssuedDueToday = ref();
    const totalChequeIssuedToday = ref();
    const checkPayables = ref(false);

    const getChildDedications = (status, page = 1) => {
        const params = {
            per_page: perPage,
            page: page,
        };

        axios.get('/child-dedications', {
            params: params,
        })
        .then((response) => {
            // let sponsors = response.data['child_dedications'].data;
            // let sponsors = JSON.stringify(response.data['child_dedications']);
            // console.log('sponsors ',sponsors); debugger;
            dedications.value = response.data['child_dedications'];
            dedications.value.current_page = response.data.pagination.current_page;
            dedications.value.last_page = response.data.pagination.last_page;
            dedications.value.total = response.data.pagination.total;
        })
    }

    const changePage = (page) => {
        if (page < 1 || page > dedications.value.last_page) return; // Prevent invalid page numbers
        dedications.value.current_page = page;
        getdedications(selectedStatus.value, page);
    };

    const pageNumbers = computed(() => {
        const pages = [];
        const totalPages = dedications.value.last_page;
        const currentPage = dedications.value.current_page;
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

    onMounted(() => {
        getChildDedications();
    })

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Child Dedication</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Child Dedication</li>
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
                            <router-link to="/admin/child-dedication/create">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New
                                    record
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
                                        <th scope="col">Fullname</th>
                                        <th scope="col">Date of Birth</th>
                                        <th scope="col">Place of Birth</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Father's Fullname</th>
                                        <th scope="col">Mother's Fullname</th>
                                        <th scope="col">Contact Number</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Date of Dedication</th>
                                        <th scope="col">Venue</th>
                                        <th scope="col">Officiating Pastor</th>
                                        <th scope="col">Sponsor(s)</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(dedication, index) in dedications.data" :key="dedication.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ capitalizeFirstLetter(dedication.child_firstname )}} {{ capitalizeFirstLetter(dedication.child_lastname) }}</td>
                                        <td>{{ dedication.child_dob }}</td>
                                        <td>{{ capitalizeFirstLetter(dedication.child_pob) }}</td>
                                        <td>{{ dedication.child_gender }}</td>
                                        <td>{{ dedication.father_fullname }}</td>
                                        <td>{{ dedication.mother_fullname }}</td>
                                        <td>{{ dedication.contact_number }}</td>
                                        <td>{{ capitalizeFirstLetter(dedication.address) }}</td>
                                        <td>{{ dedication.date_of_dedication }}</td>
                                        <td>{{ capitalizeFirstLetter(dedication.venue) }}</td>
                                        <td>{{ capitalizeFirstLetter(dedication.officiating_pastor) }}</td>
                                        <td>
                                            {{ formatSponsors(dedication.sponsors) }}
                                        </td>
                                        <td class="option-icons">
                                            <router-link :to="`/admin/child-dedication/${dedication.id}/edit`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>

                                            <a @click="deleteCheque(dedication.id)" href="#">
                                                <i class="fa fa-trash text-danger mr-2"></i>
                                            </a>
                                            <router-link :to="`/admin/dedications/view`">
                                                <i class="fa fa-eye"></i>
                                            </router-link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pagination-info d-flex justify-content-between align-items-center mt-2">
                                <!-- Showing X to Y of Z Results -->
                                <span>Showing {{ (dedications.current_page - 1) * perPage + 1 }} to 
                                    {{ dedications.current_page * perPage < dedications.total ? dedications.current_page * perPage : dedications.total }} 
                                    of {{ dedications.total }} results</span>
                                
                                <!-- Page Links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: dedications.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: dedications.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(dedications.current_page - 1)">Prev</a>
                                        </li>
                                        
                                        <!-- Dynamically generate page numbers -->
                                        <li v-for="page in pageNumbers" :key="page" class="page-item" :class="{ active: page === dedications.current_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: dedications.current_page === dedications.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(dedications.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: dedications.current_page === dedications.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(dedications.last_page)">Last</a>
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
