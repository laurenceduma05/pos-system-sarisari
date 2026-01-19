<script setup>
    import axios from 'axios';
    import { onMounted, ref, computed, watch } from 'vue';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';
    import { formatDate } from '../../helper.js';
    import { getBankColor } from '../../helper.js';
    import { getLink } from '../../helper.js';
 
    const appointmentStatus = ref([]);

    const startDate = ref(null);  // Start date filter
    const endDate = ref(null);    // End date filter

    // const cheques = ref({data:[]});
    const songs = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = 7;
    const selectedStatus = ref();
    const quotaIssuedToday = ref();
    const quotaIssuedDueToday = ref();
    const totalChequeIssuedToday = ref();
    const checkPayables = ref(false);

    const getSongs = (status, page = 1) => {
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

        axios.get('/songs', {
            params: params,
        })
        .then((response) => {
            songs.value = response.data['songs'];
            songs.value.current_page = response.data.pagination.current_page;
            songs.value.last_page = response.data.pagination.last_page;
            songs.value.total = response.data.pagination.total;
        })
    }

    const changePage = (page) => {
        if (page < 1 || page > songs.value.last_page) return; // Prevent invalid page numbers
        songs.value.current_page = page;
        getSongs(selectedStatus.value, page);
    };

    const pageNumbers = computed(() => {
        const pages = [];
        const totalPages = songs.value.last_page;
        const currentPage = songs.value.current_page;
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

                axios.delete(`/songs/${id}`)
                .then((response) => {
                    songs.value.data = songs.value.data.filter(song => song.id !== id);

                    Swal.fire(
                        'Deleted!',
                        'Record has been deleted.',
                        'success'
                    )
                })
            }
        })
    }

    const viewSong = (id) => {
        axios.delete(`/songs/${id}`)
    }

    onMounted(() => {
        getSongs();
    })

    watch(checkPayables, (newValue) => {
    });

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Songs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Songs</li>
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
                            <router-link to="/admin/songs/create">
                                <button class="btn btn-primary">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New
                                    Song
                                </button>
                            </router-link>
                        </div>
                        <div class="btn-group border-group">
                            
                            <div class="mr-2">
                                <label class="date-filter">Filter |</label>
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
                                        <th scope="col">Song Title</th>
                                        <th scope="col">By</th>
                                        <th scope="col">Lyrics</th>
                                        <th scope="col">Chords</th>
                                        <th scope="col">Key</th>
                                        <th scope="col">Link</th>
                                        <th scope="col">Created at</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(song, index) in songs.data" :key="song.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ song.title }}</td>
                                        <td>{{ song.by }}</td>
                                        <td>{{ song.lyrics }}</td>
                                        <td>{{ song.chords }}</td>
                                        <td>{{ song.key }}</td>
                                        <td>
                                            <a :href="getLink(song.link)" target="_blank">
                                                {{ song.link }}
                                            </a>
                                        </td>
                                        <td>{{ formatDate(song.created_at) }}</td>
                                        <td class="option-icons">
                                            <router-link :to="`/admin/songs/${song.id}/edit`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>

                                            <a @click="deleteCheque(song.id)" href="#">
                                                <i class="fa fa-trash text-danger mr-2"></i>
                                            </a>
                                            <router-link :to="`/admin/songs/view`">
                                                <i class="fa fa-eye"></i>
                                            </router-link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="pagination-info d-flex justify-content-between align-items-center mt-2">
                                <!-- Showing X to Y of Z Results -->
                                <span>Showing {{ (songs.current_page - 1) * perPage + 1 }} to 
                                    {{ songs.current_page * perPage < songs.total ? songs.current_page * perPage : songs.total }} 
                                    of {{ songs.total }} results</span>
                                
                                <!-- Page Links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: songs.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: songs.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(songs.current_page - 1)">Prev</a>
                                        </li>
                                        
                                        <!-- Dynamically generate page numbers -->
                                        <li v-for="page in pageNumbers" :key="page" class="page-item" :class="{ active: page === songs.current_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: songs.current_page === songs.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(songs.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: songs.current_page === songs.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(songs.last_page)">Last</a>
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
