<script setup>
    import axios from 'axios';
    import { onMounted, ref, computed, watch } from 'vue';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';
    import { formatDate } from '../../helper.js';
    import { getBankColor } from '../../helper.js';
 
    const appointmentStatus = ref([]);

    const startDate = ref(null);  // Start date filter
    const endDate = ref(null);    // End date filter

    // const cheques = ref({data:[]});
    const cheques = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = 7;
    const selectedStatus = ref();
    const quotaIssuedToday = ref();
    const quotaIssuedDueToday = ref();
    const totalChequeIssuedToday = ref();
    const checkPayables = ref(false);

    const getCheques = (status, page = 1) => {
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

        axios.get('/cheques', {
            params: params,
        })
        .then((response) => {
            cheques.value = response.data['cheques'];
            cheques.value.current_page = response.data.pagination.current_page;
            cheques.value.last_page = response.data.pagination.last_page;
            cheques.value.total = response.data.pagination.total;
        })
    }

    const changePage = (page) => {
        if (page < 1 || page > cheques.value.last_page) return; // Prevent invalid page numbers
        cheques.value.current_page = page;
        getCheques(selectedStatus.value, page);
    };

    const pageNumbers = computed(() => {
        const pages = [];
        const totalPages = cheques.value.last_page;
        const currentPage = cheques.value.current_page;
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

    const deleteCheque = (id) => {
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

                axios.delete(`/cheques/${id}`)
                .then((response) => {
                    cheques.value.data = cheques.value.data.filter(cheque => cheque.id !== id);

                    Swal.fire(
                        'Deleted!',
                        'Record has been deleted.',
                        'success'
                    )
                })
            }
        })
    }

    const transferCheque = (id) => {
        Swal.fire({
            title: 'Transfer Cheque',
            text: "You're abuot to transfer this cheque to tomorrow",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, transfer it!'
        }).then((result) => {
            if (result.isConfirmed) {

                axios.put(`/cheques/transfer/${id}`)
                .then((response) => {
                    cheques.value.data = cheques.value.data.filter(cheque => cheque.id !== id);

                    Swal.fire(
                        'Transferred!',
                        'Record has been transferred.',
                        'success'
                    )
                    getCheques();
                })
            }
        })
    }

    const getTodayQuotaIssued = async () => {
        try {
            const response = await axios.get('/quotas/today-issued')
            if (response.data) {
                quotaIssuedToday.value = response.data.data;
            }
        } catch (error) {
            
        }
    }

    const getDueTodayQuotaIssued = async () => {
        try {
            const response = await axios.get('/cheques/total-amount-due-today')
            if (response.data) {
                quotaIssuedDueToday.value = response.data.data;
            }
        } catch (error) {
            
        }
    }

    const getTodayChequeIssued = async () => {
        try {
            const response = await axios.get('/cheques/total-amount-today')
            if (response.data) {
                totalChequeIssuedToday.value = response.data.data;
            }
        } catch (error) {
            
        }
    }


    const checkTimeAndUpdate = async () => {
        setInterval(() => {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            console.log('checking of pyables records');
            // if (checkPayables.value === true && currentHour === 16 && currentMinute >= 0 && currentMinute <= 59) {

            if (currentHour === 8 && currentMinute === 0) { // 8:00 am checking
                checkPayables.value === false;
            }

            if (checkPayables.value === false && currentHour === 16 && currentMinute >= 0 && currentMinute <= 59) { // 4:00 PM
                updatePayables();
            }

        }, 600000);
    }

    const updatePayables = async () => {
        try {
            const response = await axios.put('/cheques/update-payables');
            getCheques();
            if (response.data.success) {
                console.log('response', response.data);
                Swal.fire({
                    title: 'Payables updated successfully!',
                    text: "All the payables that is due today is transferred tomorrow",
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok, got it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        checkPayables.value = true;
                        Swal.close();
                    }
                })
            } else {
               
            }
        } catch (error) {
            console.error('Error updating payables:', error);
        }
    }

    onMounted(() => {

        const storedValue = localStorage.getItem('checkPayables');
        if (storedValue !== null) {
            checkPayables.value = storedValue === 'true'; // 'true' string to boolean
        }

        getCheques();
        getTodayQuotaIssued();
        getDueTodayQuotaIssued();
        checkTimeAndUpdate();
        getTodayChequeIssued();
    })

    watch(checkPayables, (newValue) => {
      // Persist the updated value to localStorage
      localStorage.setItem('checkPayables', newValue.toString());
    });

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cheques</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cheques</li>
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
                            <router-link to="/admin/cheques/create">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Issue New
                                    Cheque
                                </button>
                            </router-link>
                            <button class="btn btn-warning ml-2" @click="updatePayables">
                                <i class="fa fa-exchange-alt mr-1"></i> Transfer All Due Today To Tomorrow
                            </button>
                        </div>
                        <div class="btn-group border-group">
                            
                            <div class="mr-2">
                                <label class="date-filter">Filter By Due Date |</label>
                            </div>
                            <div class="mr-2">
                                <label for="startDate" class="mr-2">Start Date</label>
                                <input 
                                    type="date" 
                                    id="startDate" 
                                    v-model="startDate" 
                                    class="form-control" 
                                    @change="getCheques" 
                                />
                            </div>
                            <div>
                                <label for="endDate" class="mr-2">End Date</label>
                                <input 
                                    type="date" 
                                    id="endDate" 
                                    v-model="endDate" 
                                    class="form-control" 
                                    @change="getCheques" 
                                />
                            </div>

                            <div>
                                <a href="#" style="color: red;" @click="clearDateFilters">Clear Date Filters</a>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Business Name</th>
                                        <th scope="col">Bank</th>
                                        <th scope="col">Last 4 Cheque Number</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Remarks</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Issued At</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cheque, index) in cheques.data" :key="cheque.id" :class="{'due-today': cheque.is_due_or_past_due, 'paid': cheque.status === 'Paid'}">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ cheque.business_name }}</td>
                                        <td>
                                            <span class="badge" :class="`badge-${getBankColor(cheque.bank)}`">{{ cheque.bank }}</span>
                                        </td>
                                        <td>{{ cheque.cheque_number }}</td>
                                        <td>{{ formatAmount(cheque.amount) }}</td>
                                        <!-- <td>
                                            <span class="badge" :class="`badge-${statusColor(cheque.status)}`">{{ appointmentStatusName(cheque.status) }}</span>
                                        </td> -->
                                        <td>{{ cheque.status }}</td>
                                        <td>{{ cheque.remarks }}</td>
                                        <td>{{ cheque.is_due_or_past_due ? 'Due Today' : cheque.due_date }}</td>
                                        <td>{{ formatDate(cheque.created_at) }}</td>
                                        <td class="option-icons">
                                            <a @click="transferCheque(cheque.id)" href="#" class="mr-2">
                                                <i class="fa fa-share text-warning"></i>
                                            </a>
                                            <router-link :to="`/admin/cheques/${cheque.id}/edit`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>

                                            <a @click="deleteCheque(cheque.id)" href="#">
                                                <i class="fa fa-trash text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="quota-container mt-2">
                                <span class="quota-label">Cheque Issued Today:</span>
                                <span class="quota-value">{{ totalChequeIssuedToday ? formatAmount(totalChequeIssuedToday) : 0 }}</span>
                            </div>

                            <div class="quota-container mt-2">
                                <span class="quota-label">Total Cheque Due Today:</span>
                                <!-- <span class="quota-value">{{ quotaIssuedToday ? formatAmount(quotaIssuedToday) : 0 }}</span> -->
                                <span class="quota-value">{{ quotaIssuedDueToday ? formatAmount(quotaIssuedDueToday) : 0 }}</span>
                            </div>

                            <div class="pagination-info d-flex justify-content-between align-items-center mt-2">
                                <!-- Showing X to Y of Z Results -->
                                <span>Showing {{ (cheques.current_page - 1) * perPage + 1 }} to 
                                    {{ cheques.current_page * perPage < cheques.total ? cheques.current_page * perPage : cheques.total }} 
                                    of {{ cheques.total }} results</span>
                                
                                <!-- Page Links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: cheques.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: cheques.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(cheques.current_page - 1)">Prev</a>
                                        </li>
                                        
                                        <!-- Dynamically generate page numbers -->
                                        <li v-for="page in pageNumbers" :key="page" class="page-item" :class="{ active: page === cheques.current_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: cheques.current_page === cheques.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(cheques.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: cheques.current_page === cheques.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(cheques.last_page)">Last</a>
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
