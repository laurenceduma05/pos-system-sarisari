<script setup>
    import { onMounted, ref, computed } from 'vue';
    import axios from 'axios';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';
    import { formatDate } from '../../helper.js';

    const events = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = ref(7);
    const currentPage = ref(1);
    const startDate = ref(null);
    const endDate = ref(null);
    const searchTerm = ref('');
    const isLoading = ref(false);

    // Get events from API
    const getevents = async (page = 1) => {
        isLoading.value = true;
        try {
            const params = {
                per_page: perPage.value,
                page: page,
            };

            if (startDate.value) {
                params.start_date = startDate.value;
            }

            if (endDate.value) {
                params.end_date = endDate.value;
            }

            const response = await axios.get('/events', { params });

            // Handle different response structures
            if (response.data.events && response.data.pagination) {
                events.value.data = response.data.events.data || response.data.events;
                events.value.current_page = response.data.pagination.current_page || 1;
                events.value.last_page = response.data.pagination.last_page || 1;
                events.value.total = response.data.pagination.total || 0;
            } else if (response.data.data) {
                // Laravel pagination default structure
                events.value.data = response.data.data;
                events.value.current_page = response.data.current_page || 1;
                events.value.last_page = response.data.last_page || 1;
                events.value.total = response.data.total || 0;
            } else {
                // Fallback structure
                events.value.data = response.data.events || response.data || [];
                events.value.current_page = page;
                events.value.last_page = 1;
                events.value.total = events.value.data.length;
            }

            currentPage.value = events.value.current_page;
        } catch (error) {
            console.error('Error fetching events:', error);
            events.value = { data: [], current_page: 1, last_page: 1, total: 0 };
        } finally {
            isLoading.value = false;
        }
    }

    // Delete event
    const deleteEvent = (id) => {
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
                axios.delete(`/events/${id}`)
                .then((response) => {
                    getevents(currentPage.value);
                    Swal.fire('Deleted!', 'Event has been deleted.', 'success');
                })
                .catch((error) => {
                    console.error('Error deleting event:', error);
                    Swal.fire('Error!', 'Failed to delete event.', 'error');
                });
            }
        });
    }

    // Clear filters
    const clearFilters = () => {
        startDate.value = null;
        endDate.value = null;
        searchTerm.value = '';
        getevents(1);
    }

    // Client-side search filter
    const filteredEvents = computed(() => {
        if (!searchTerm.value) {
            return events.value.data;
        }

        const term = searchTerm.value.toLowerCase();
        return events.value.data.filter(event =>
            event.title.toLowerCase().includes(term) ||
            event.by.toLowerCase().includes(term) ||
            (event.details && event.details.toLowerCase().includes(term))
        );
    });

    // Pagination computed properties
    const paginatedEvents = computed(() => {
        const start = (currentPage.value - 1) * perPage.value;
        const end = start + perPage.value;
        return filteredEvents.value.slice(start, end);
    });

    const totalPages = computed(() => {
        return Math.ceil(filteredEvents.value.length / perPage.value);
    });

    const pageRange = computed(() => {
        const start = Math.max(1, currentPage.value - 2);
        const end = Math.min(totalPages.value, currentPage.value + 2);
        const pages = [];
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        return pages;
    });

    const changePage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    }

    // Get edit link
    const getEditLink = (id) => {
        return `/admin/events/${id}/edit`;
    }

    onMounted(() => {
        getevents(1);
    });
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Events</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item active">Events</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex">
                    <select v-model="perPage" @change="getevents(1)" class="form-control">
                        <option value="7">7</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>

                    <input type="date" class="form-control ml-2" v-model="startDate" @change="getevents(1)" placeholder="Start Date">

                    <input type="date" class="form-control ml-2" v-model="endDate" @change="getevents(1)" placeholder="End Date">

                    <input type="text" class="form-control ml-2" v-model="searchTerm" placeholder="Search events..." style="min-width: 200px;">

                    <button class="btn btn-secondary ml-2" @click="clearFilters">
                        <i class="fa fa-times"></i> Clear
                    </button>
                </div>

                <div>
                    <router-link to="/admin/events/create">
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-plus-circle mr-1"></i>
                            Add New Event
                        </button>
                    </router-link>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Loading State -->
                    <div v-if="isLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading events...</p>
                    </div>

                    <!-- Events Table -->
                    <table v-else class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Title</th>
                                <th>Event By</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Details</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(event, index) in paginatedEvents" :key="event.id">
                                <td>{{ (currentPage - 1) * perPage + index + 1 }}</td>
                                <td>{{ event.title }}</td>
                                <td>{{ event.by }}</td>
                                <td>{{ formatDate(event.start_time) }}</td>
                                <td>{{ formatDate(event.end_time) }}</td>
                                <td>{{ event.details ? (event.details.length > 50 ? event.details.substring(0, 50) + '...' : event.details) : 'N/A' }}</td>
                                <td>{{ formatDate(event.created_at) }}</td>
                                <td>
                                    <router-link :to="getEditLink(event.id)">
                                        <i class="fa fa-edit mr-2 text-primary"></i>
                                    </router-link>
                                    <a href="#" @click.prevent="deleteEvent(event.id)">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="paginatedEvents.length === 0 && !isLoading">
                                <td colspan="8" class="text-center">No events found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix" v-if="!isLoading && filteredEvents.length > 0">
                    <div class="row">
                        <div class="col-md-6">
                            Showing {{ paginatedEvents.length }} of {{ filteredEvents.length }} events
                            <span v-if="searchTerm">(filtered from {{ events.total }} total events)</span>
                            <span v-else>of {{ events.total }} total events</span>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <ul class="pagination pagination-sm m-0">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">«</a>
                                </li>

                                <li class="page-item" v-for="page in pageRange" :key="page" :class="{ active: currentPage === page }">
                                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                </li>

                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">»</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.page-link {
    cursor: pointer;
}

.table td {
    vertical-align: middle;
}

.fa-edit, .fa-trash {
    cursor: pointer;
}

.fa-edit:hover {
    color: #0056b3 !important;
}

.fa-trash:hover {
    color: #c82333 !important;
}
</style>
