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
        by: '',
        lyrics: '',
        chords: '',
        key: '',
        link: '',
    });

    const available_quota = ref();
    const quota_message = ref();

    const handleSubmit = (values, actions) => {
        if (editMode.value) {
            editCheque(values, actions);
        } else {
            createSong(values, actions);
        }
    }

    const createSong = (values, actions) => {
        axios.post('/songs/create', form)
        .then((response) => {
            router.push('/admin/songs');
            toastr.success('Song created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
    }

    const editCheque = (values, actions) => {
        axios.put(`/cheques/${route.params.id}/edit`, form)
        .then((response) => {
            router.push('/admin/cheques');
            toastr.success('Cheque updated successfully!');
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

        if (route.name === '/admin.songs.edit') {
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
                        Song
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/songs">Songs</router-link>
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
                                            <label for="title">Song Title</label>
                                            <input v-model="form.title" type="text" :class="{ 'is-invalid': errors.title }" class="form-control" id="title" placeholder="Enter Song Title">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="by">Song By</label>
                                            <input v-model="form.by" type="text" :class="{ 'is-invalid': errors.by }" class="form-control" id="by" placeholder="Enter Song by">
                                            <span class="invalid-feedback">{{ errors.by }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lyrics">Lyrics</label>
                                             <textarea v-model="form.lyrics" class="form-control" id="lyrics" rows="3" placeholder="Enter lyrics"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lyrics">Chords</label>
                                             <textarea v-model="form.chords" class="form-control" id="chords" rows="3" placeholder="Enter chords"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="time">key</label>
                                            <input v-model="form.key" type="text" :class="{ 'is-invalid': errors.key }" class="form-control" id="key" placeholder="Enter key">
                                            <span class="invalid-feedback">{{ errors.key }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="time">Link</label> <br>
                                            <span>Google docs link, etc.</span>
                                            <input v-model="form.link" type="text" :class="{ 'is-invalid': errors.link }" class="form-control" id="link" placeholder="Enter link">
                                            <span class="invalid-feedback">{{ errors.link }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <router-link to="/admin/songs">
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
