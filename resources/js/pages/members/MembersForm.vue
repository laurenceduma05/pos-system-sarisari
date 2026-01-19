<script setup>
    import axios from 'axios';
    import { reactive, onMounted, ref } from 'vue';
    import { useRouter, useRoute } from 'vue-router'; // this will use to redirecting page!
    import { useToastr } from '@/toastr'; // this is for success messages
    import { Form } from 'vee-validate'; // this will use to validate form!
    import flatpickr from 'flatpickr'; // this is use for the appointment start time and endtime behavior etc..
    import 'flatpickr/dist/themes/dark.css';
    import Swal from 'sweetalert2';
    import { formatAmount } from '../../helper.js';

    const router = useRouter();
    const route = useRoute();
    const toastr = useToastr();
    const form = reactive({
        child_firstname: '',
        child_middlename: '',
        child_lastname: '',
        child_dob: '',
        child_pob: '',
        child_gender: '',
        father_fullname: '',
        mother_fullname: '',
        contact_number: '',
        address: '',
        date_of_dedication: '',
        venue: '',
        officiating_pastor: '',
        sponsors: [''],
    });

    const handleSubmit = (values, actions) => {
        if (editMode.value) {
            console.log('edit child dedication values', values);
            editChildDedication(values, actions);
        } else {
            console.log('test create');
            createChildDedication(values, actions);
        }
    }

    const createChildDedication = (values, actions) => {

        axios.post('/child-dedications/create', form)
        .then((response) => {
            console.log('test this');
            router.push('/admin/child-dedications');
            toastr.success('Child Dedication created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
    }

    const editChildDedication = (values, actions) => {

        axios.put(`/child-dedications/${route.params.id}/edit`, form)
        .then((response) => {
            router.push('/admin/child-dedications');
            toastr.success('Child dedication updated successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        });
    }


    const editMode = ref(false);

    const goBack = () => {
        this.router.go(-1);
    }

    const getChildDedication = () => { // for getting the data of child dedication and put it to the fields

        axios.get(`/child-dedication/${route.params.id}/edit`)
        .then((response) => {
            console.log('sponsors',JSON.parse(response.data.sponsors));
            form.child_firstname = response.data.child_firstname;
            form.child_middlename = response.data.child_middlename;
            form.child_lastname = response.data.child_lastname;
            form.child_dob = response.data.child_dob;
            form.child_pob = response.data.child_pob;
            form.child_gender = response.data.child_gender;
            form.father_fullname = response.data.father_fullname;
            form.mother_fullname = response.data.mother_fullname;
            form.contact_number = response.data.contact_number;
            form.address = response.data.address;
            form.date_of_dedication = response.data.date_of_dedication;
            form.venue = response.data.venue;
            form.officiating_pastor = response.data.officiating_pastor;
            form.sponsors = JSON.parse(response.data.sponsors);
        });
    }

    onMounted(() => {

        if (route.name === '/admin.child-dedication.edit') {
            editMode.value = true;
            getChildDedication();
        }

        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "y-m-d h:i K",
            defaultHour: 10,
        });

    });

    const addSponsor = () => { // adding sponsor field
        console.log('adding sponsor');
        form.sponsors.push('');
    }

    const removeSponsor = (index) => { // removing sponsor field
        form.sponsors.splice(index, 1);
    } 

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Add</span>
                        New Child Dedication Record
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/teachings">Child dedication</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                           <!-- <Form @submit.prevent="$event => handleSubmit()"> -->
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <!-- start | Child Information -->
                                  <h5>Child Information</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="child_firstname">Firstname</label>
                                            <input v-model="form.child_firstname" type="text" :class="{ 'is-invalid': errors.child_firstname }" name="child_firstname" class="form-control" id="child_firstname" placeholder="Enter firstname">
                                            <span class="invalid-feedback">{{ errors.child_firstname }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="child_middlename">Middlename</label>
                                            <input v-model="form.child_middlename" type="text" :class="{ 'is-invalid': errors.child_middlename }" class="form-control" id="child_middlename" placeholder="Enter middlename">
                                            <span class="invalid-feedback">{{ errors.child_middlename }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="child_lastname">Lastname</label>
                                            <input v-model="form.child_lastname" type="text" :class="{ 'is-invalid': errors.child_lastname }" class="form-control" id="child_lastname" placeholder="Enter lastname">
                                            <span class="invalid-feedback">{{ errors.child_lastname }}</span>
                                        </div>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="child_dob">Date of birth</label>
                                            <input v-model="form.child_dob" type="date" :class="{ 'is-invalid': errors.child_dob }" class="form-control" id="child_dob">
                                            <span class="invalid-feedback">{{ errors.child_dob }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="child_pob">Place of birth</label>
                                            <input v-model="form.child_pob" type="text" :class="{ 'is-invalid': errors.child_pob }" class="form-control" id="child_pob" placeholder="Enter place of birth">
                                            <span class="invalid-feedback">{{ errors.child_pob }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="child_gender">Gender</label>
                                        <div class="form-group">
                                           <div class="form-check form-check-inline">
                                                <input v-model="form.child_gender" class="form-check-input" type="radio" name="child_gender" id="gender_male" value="Male">
                                                <label class="form-check-label" for="inlineRadio1">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="form.child_gender" class="form-check-input" type="radio" name="child_gender" id="gender_female" value="Female">
                                                <label class="form-check-label" for="inlineRadio2">Female</label>
                                            </div>
                                            <span class="invalid-feedback">{{ errors.child_gender }}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <!-- end | Child Information -->

                                <!-- start | Parent / Guardian Information -->
                                <h5>Parent / Guardian Information</h5>
                                <hr>
                                 <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="father_fullname">Father's fullname</label>
                                            <input v-model="form.father_fullname" type="text" :class="{ 'is-invalid': errors.father_fullname }" class="form-control" id="father_fullname" placeholder="Enter father's fullname">
                                            <span class="invalid-feedback">{{ errors.father_fullname }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mother_fullname">Mother's fullname</label>
                                            <input v-model="form.mother_fullname" type="text" :class="{ 'is-invalid': errors.mother_fullname }" class="form-control" id="mother_fullname" placeholder="Enter mother's fullname">
                                            <span class="invalid-feedback">{{ errors.mother_fullname }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contact_number">Contact number</label>
                                            <input v-model="form.contact_number" type="text" :class="{ 'is-invalid': errors.contact_number }" class="form-control" id="contact_number" placeholder="Enter contact number">
                                            <span class="invalid-feedback">{{ errors.contact_number }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input v-model="form.address" type="text" :class="{ 'is-invalid': errors.address }" class="form-control" id="address" placeholder="Enter address">
                                            <span class="invalid-feedback">{{ errors.address }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- start | Dedication Details -->
                                <hr>
                                <h5>Dedication Details</h5>
                                <hr>
                                 <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date_of_dedication">Date of Dedication</label>
                                            <input v-model="form.date_of_dedication" type="date" :class="{ 'is-invalid': errors.date_of_dedication }" class="form-control" id="date_of_dedication" placeholder="Enter Date of Dedication">
                                            <span class="invalid-feedback">{{ errors.date_of_dedication }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="venue">Venue / Church Branch</label>
                                            <input v-model="form.venue" type="text" :class="{ 'is-invalid': errors.venue }" class="form-control" id="venue" placeholder="Enter Venue / Church Branch">
                                            <span class="invalid-feedback">{{ errors.venue }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="officiating_pastor">Officiating Minister / Pastor</label>
                                            <input v-model="form.officiating_pastor" type="text" :class="{ 'is-invalid': errors.officiating_pastor }" class="form-control" id="officiating_pastor" placeholder="Enter Officiating Minister / Pastor">
                                            <span class="invalid-feedback">{{ errors.officiating_pastor }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sponsors">Godparents / Sponsors</label>
                                            <div v-for="(sponsor, index) in form.sponsors" :key="index">
                                                <div class="input-group mb-3">
                                                    <input 
                                                        v-model="form.sponsors[index]" 
                                                        type="text" 
                                                        :class="{ 'is-invalid': errors.sponsors }" 
                                                        class="form-control mt-2" 
                                                        id="sponsors" 
                                                        :placeholder="`Sponsors ${index + 1}`"
                                                    >
                                                    <a class="text-danger m-2" id="button-addon2" @click="removeSponsor(index)" style="cursor: pointer;">remove</a>
                                                </div>
                                            </div>
                                            <span class="invalid-feedback">{{ errors.sponsors }}</span>
                                            <a class="mt-2 cursor-pointer" @click="addSponsor" style="cursor: pointer;">
                                                + Add Sponsor
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                                <router-link to="/admin/child-dedications">
                                    <button type="button" class="btn btn-secondary ml-2">Cancel</button>
                                </router-link>
                            </Form>
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
</style>
