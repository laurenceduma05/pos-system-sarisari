<script setup>
    import axios from 'axios';
    import { ref, onMounted, reactive, watch } from 'vue';
    import { Form, Field, FormContextKey } from 'vee-validate';
    import * as yup from 'yup';
    import { useToastr } from '../../toastr.js';
    import UserListItem from './UserListItem.vue';
    import { roleType } from '../../helper.js';
    import { debounce } from 'lodash';
    import { Bootstrap4Pagination } from 'laravel-vue-pagination';
    import Swal from 'sweetalert2';

    const toastr = useToastr();
    const users = ref({data:[]});
    const editing = ref(false);
    const formValues = ref();
    const form = ref(null);

    const getUsers = (page = 1) => {
        axios.get(`/api/users?page=${page}`, {
            params: {
                query: searchQuery.value,
            }
        })
        .then((response) => {
            users.value = response.data;
            selectedUsers.value = [];
            selectAll.value = false;
        })
    }

    const createUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().required().min(8),
    });

    const editUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().notRequired().test('password','Password must be minimum of 8 characters', function(value) {
            if (!!value) {
                const schema = yup.string().min(8);
                return schema.isValidSync(value);
            }
            return true;
        })
    });

    const createUser = (values, { resetForm, setErrors }) => {
        axios.post('/api/users', values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success("User Created Successfully!");
        })

        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        })
    };

    const addUser = () => {
        form.value.resetForm();
        editing.value = false;
        $('#userFormModal').modal('show');
    }

    const editUser = (user) => {
        editing.value = true;
        form.value.resetForm();
        $('#userFormModal').modal('show');
        formValues.value = {
            id: user.id,
            name: user.name,
            email: user.email
        };
    }

    const updateUser = (values, { setErrors }) => {
        // console.log("values", values); debugger;
        axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            // console.log(typeof users.value);
            const index = users.value.data.findIndex(user => user.id === response.data.id);
            users.value.data[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User Update Successfully!');

        }).catch((error) => {
            setErrors(error.response.data.errors)
            console.log(error);
        });
    }


    const handleSubmit = (values, actions) => {
        if (editing.value) {
            updateUser(values, actions);
        } else {
            createUser(values, actions);
        }
    }

    const searchQuery = ref(null);
    // const search = () => {
    //     axios.get('/api/users/search', {
    //         params: {
    //             query: searchQuery.value
    //         }
    //     })
    //     .then(response => {
    //         users.value = response.data;
    //     })
    //     .catch(error => {
    //         console.log(error);
    //     })
    // }

    watch(searchQuery, debounce(() => {
        // search();
        getUsers();
    }, 300));

    const selectedUsers = ref([]);
    const toggleSelection = (user) => {
        const index = selectedUsers.value.indexOf(user.id);
        if (index === -1) {
            selectedUsers.value.push(user.id);
        }else{
            selectedUsers.value.splice(index, 1);
        }
        console.log(selectedUsers.value);
    }

    const userIdBeingDeleted = ref(null);

    const confirmUserDeletion = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        })
        .then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/api/users/${id}`)
                .then(() => {
                    users.value.data = users.value.data.filter(user => user.id !== id);

                    Swal.fire(
                        'Deleted!',
                        'User has been deleted.',
                        'success'
                    )
                })
            }
        })
    };

    // const deleteUser = () => {
    //     axios.delete(`/api/users/${userIdBeingDeleted.value}`)
    //     .then(() => {
    //         $('#deleteUserModal').modal('hide');
    //         users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value);
    //         toastr.success('User Deleted Successfully!');
    //     });
    // }

    const bulkDelete = () => {
        // put confirm message before bulk deletion
        axios.delete('/api/users/', {
            data: {
                ids: selectedUsers.value
            }
        })
        .then(response => {
            users.value.data = users.value.filter(user => !selectedUsers.value.data.includes(user.id));
            selectedUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        })
    };

    const selectAll = ref(false);
    const selectAllUsers = () => {
        if (selectAll.value) {
            selectedUsers.value = users.value.data.map(user => user.id);
        } else {
            selectedUsers.value = [];
        }

        console.log(selectedUsers.value);
    };

    onMounted(() => {
        getUsers();
    })

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New User
                    </button>
                    <div v-if="selectedUsers.length > 0" class="d-flex">
                        <button @click="bulkDelete" type="button" class="mb-2 ml-2 btn btn-danger">
                            <i class="fa fa-trash mr-1"></i>
                            Delete Selected
                        </button>
                        <span class="ml-2 mt-2">( Selected {{ selectedUsers.length }} Users)</span>
                    </div>
                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="search...">
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" v-model="selectAll" @change="selectAllUsers"/>
                                </th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                            <UserListItem v-for="(user, index) in users.data"
                                :key="user.id"
                                :user=user
                                :index=index
                                @edit-user="editUser"
                                @confirm-user-deletion="confirmUserDeletion"
                                @toggle-selection="toggleSelection"
                                :select-all="selectAll"
                            />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No records found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" />
        </div>
    </div>

    <div class="modal fade" id="userFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Add New User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" :class="{'is-invalid': errors.name}" id="name"
                                placeholder="Enter Full Name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <Field name="email" type="email" class="form-control" :class="{'is-invalid': errors.email}" id="email"
                                placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>

                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <Field name="password" type="password" class="form-control" :class="{'is-invalid': errors.password}" id="password"
                            placeholder="Password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Delete User</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>
                        Are you sure you want to delete this user?
                    </h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button @click.prevent="deleteUser" type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
