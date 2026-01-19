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
        title: '',
        descriptions: '',
        link: '',
        audience: [],
        file: null,
       
    });

    const handleFileUpload = (event) => {
        const file = event.target.files[0]; // Get the first file selected
        form.file = file; // Store the file object in the form
    }

    const handleSubmit = (values, actions) => {
        if (editMode.value) {
            editTeaching(values, actions);
        } else {
            createTeaching(values, actions);
        }
    }

    const createTeaching = (values, actions) => {

        const formData = new FormData();
        formData.append('title', form.title);
        formData.append('descriptions', form.descriptions);

        console.log('Audience array before sending:', form.audience);
        // debugger;

        if (form.link) {
            formData.append('link', form.link);
        }

        if (Array.isArray(form.audience) && form.audience.length > 0) {
            form.audience.forEach(audience => {
                formData.append('audience[]', audience); // Append each item separately
            });
        } else {
            console.log('Audience is empty or not an array');
        }

        // debugger;
        if (form.file) {
            formData.append('file', form.file); // Append the file to form data
        }

        axios.post('/teachings/create', formData)
        .then((response) => {
            router.push('/admin/teachings');
            toastr.success('Teaching created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
    }

    const editTeaching = (values, actions) => {
        const formData = new FormData();
        formData.append('title', form.title);
        formData.append('descriptions', form.descriptions);
        formData.append('audience', form.audience);
        if (form.file) {
            formData.append('file', form.file);
        }

        axios.put(`/teachings/${route.params.id}/edit`, formData)
        .then((response) => {
            router.push('/admin/cheques');
            toastr.success('Teaching updated successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        });
    }


    const editMode = ref(false);

    const goBack = () => {
        this.router.go(-1);
    }

    onMounted(() => {

        if (route.name === '/admin.teachings.edit') {
            editMode.value = true;
        }

        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "y-m-d h:i K",
            defaultHour: 10,
        });

    });

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Add</span>
                        Teachings
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/teachings">Teachings</router-link>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Teaching Title</label>
                                            <input v-model="form.title" type="text" :class="{ 'is-invalid': errors.title }" class="form-control" id="title" placeholder="Enter Song Title">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lyrics">Description</label>
                                             <textarea v-model="form.descriptions" class="form-control" id="description" rows="3" placeholder="Enter lyrics"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="audience">Audience</label>
                                        <div>
                                            <input
                                                type="checkbox"
                                                id="junior_pastor"
                                                value="junior_pastor"
                                                v-model="form.audience"
                                            />
                                            <label for="junior_pastor">Junior Pastor</label>
                                        </div>
                                        <div>
                                            <input
                                                type="checkbox"
                                                id="youth_leader"
                                                value="youth_leader"
                                                v-model="form.audience"
                                            />
                                            <label for="youth_leader">Youth Leader</label>
                                        </div>
                                        <div>
                                            <input
                                                type="checkbox"
                                                id="members"
                                                value="members"
                                                v-model="form.audience"
                                            />
                                            <label for="members">Members</label>
                                        </div>
                                    </div>
                                </div>
                                 <!-- File Upload -->
                                 <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="file">Upload File (PDF/Word)</label>
                                            <input type="file" @change="handleFileUpload" class="form-control" id="file">
                                            <span v-if="form.file && form.file.name" class="form-text mt-2">File: {{ form.file.name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="time">Link (optional)</label>
                                            <input v-model="form.link" type="text" :class="{ 'is-invalid': errors.link }" class="form-control" id="link" placeholder="Enter link">
                                            <span class="invalid-feedback">{{ errors.link }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <router-link to="/admin/teachings">
                                    <button type="button" class="btn btn-secondary ml-2">Cancel</button>
                                </router-link>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="available_quota" class="quota-container mt-2 ml-3">
        <span class="quota-label">Available Quota Today:</span>
        <span class="quota-value">{{ formatAmount(available_quota) }}</span>
    </div>
    <div v-if="quota_message" class="quota-container mt-2 ml-3 bg-red">
        <span class="quota-label text-white">{{ quota_message }} !</span>
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
