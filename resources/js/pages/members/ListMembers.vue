<script setup>
    import axios from 'axios';
    import { onMounted, ref, computed, watch } from 'vue';
    import Swal from 'sweetalert2';
    import { formatDate, roleType } from '../../helper.js';
    import { capitalizeFirstLetter } from '../../helper.js';

    const members = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
    const perPage = 7;

    const getMembersRecords = (status, page=1) => {
        const params = {
            per_page: perPage,
            page: page,
        };

        axios.get('/members', {params: params,})
            .then((response) => {
                members.value = response.data['members_record'];
                members.value.current_page = response.data.pagination.current_page;
                members.value.last_page = response.data.pagination.last_page;
                members.value.total = response.data.pagination.total;
            });
    }

    const changePage = (page) => {
        if (page < 1 || page > members.value.last_page) return; // Prevent invalid page numbers
        members.value.current_page = page;
        getMembersRecords(selectedStatus.value, page);
    };

    const pageNumbers = computed(() => {
        const pages = [];
        const totalPages = members.value.last_page;
        const currentPage = members.value.current_page;
        const range = 2; // Number of pages to display before and after the current page
    
        for (let i = currentPage - range; i <= currentPage + range; i++) {
            if (i > 0 && i <= totalPages) pages.push(i);
        }
        return pages;
    });

    onMounted(() => {
        getMembersRecords();
    })

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Members</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Members</li>
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
                                        <th scope="col">Gender</th>
                                        <th scope="col">Age</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Contact Number</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Outreach Belong</th>
                                        <th scope="col">Member Since</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                  <tbody>
                                    <tr v-for="(member, index) in members.data" :key="member.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ capitalizeFirstLetter(member.firstname )}} {{ capitalizeFirstLetter(member.lastname) }}</td>
                                        <td>{{ capitalizeFirstLetter(member.gender) }}</td>
                                        <td>{{ member.age }}</td>
                                        <td>{{ capitalizeFirstLetter(member.address) }}</td>
                                        <td>{{ member.contact_number }}</td>
                                        <td>{{ roleType(member.role) }}</td>
                                        <td>{{ member.outreach }}</td>
                                        <td>{{ formatDate(member.member_since) }}</td>
                                        
                                        <td class="option-icons">
                                            <router-link :to="`/admin/member/${member.id}/edit`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>

                                            <a @click="deleteCheque(member.id)" href="#">
                                                <i class="fa fa-trash text-danger mr-2"></i>
                                            </a>
                                            <router-link :to="`/admin/members/view`">
                                                <i class="fa fa-eye"></i>
                                            </router-link>
                                        </td>
                                    </tr>
                                </tbody>
                               
                            </table>
                            <div class="pagination-info d-flex justify-content-between align-items-center mt-2">
                                <!-- Showing X to Y of Z Results -->
                                <span>Showing {{ (members.current_page - 1) * perPage + 1 }} to 
                                    {{ members.current_page * perPage < members.total ? members.current_page * perPage : members.total }} 
                                    of {{ members.total }} results</span>
                                
                                <!-- Page Links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item" :class="{ disabled: members.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: members.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="changePage(members.current_page - 1)">Prev</a>
                                        </li>
                                        
                                        <!-- Dynamically generate page numbers -->
                                        <li v-for="page in pageNumbers" :key="page" class="page-item" :class="{ active: page === members.current_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                                        </li>
                                        
                                        <li class="page-item" :class="{ disabled: members.current_page === members.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(members.current_page + 1)">Next</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: members.current_page === members.last_page }">
                                            <a class="page-link" href="#" @click.prevent="changePage(members.last_page)">Last</a>
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
</style>
