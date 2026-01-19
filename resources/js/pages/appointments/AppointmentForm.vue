<script setup>
    import axios from 'axios';
    import { reactive, onMounted, ref } from 'vue';
    import { useRouter, useRoute } from 'vue-router'; // this will use to redirecting page!
    import { useToastr } from '@/toastr'; // this is for success messages
    import { Form } from 'vee-validate'; // this will use to validate form!
    import flatpickr from 'flatpickr'; // this is use for the appointment start time and endtime behavior etc..
    import 'flatpickr/dist/themes/dark.css';

    const router = useRouter();
    const route = useRoute();
    const toastr = useToastr();
    const form = reactive({
        title: '',
        client_id: '',
        start_time: '',
        end_time: '',
        description: '',
    });

    const handleSubmit = (values, actions) => {
        if (editMode.value) {
            editAppointment(values, actions);
        } else {
            createAppointment(values, actions);
        }
    }

    const createAppointment = (values, actions) => {
        axios.post('/api/appointments/create', form)
        .then((response) => {
            router.push('/admin/appointments');
            toastr.success('Appointments created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
    }

    const editAppointment = (values, actions) => {
        axios.put(`/api/appointments/${route.params.id}/edit`, form)
        .then((response) => {
            router.push('/admin/appointments');
            toastr.success('Appointments updated successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        });
    }

    const clients = ref();
    const getClients = () => {
        axios.get('/api/clients')
        .then((response) => {
            clients.value = response.data;
        })
    }

    const getAppointment = () => {
        axios.get(`/api/appointments/${route.params.id}/edit`)
        .then(({data}) => {
            form.title = data.title;
            form.client_id = data.client_id;
            form.start_time = data.formatted_start_time;
            form.end_time = data.formatted_end_time;
            form.description = data.description;
        })
    }
    const editMode = ref(false);

    const goBack = () => {
        this.router.go(-1);
    }

    onMounted(() => {

        if (route.name === '/admin.appointments.edit') {
            editMode.value = true;
            getAppointment();
        }

        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "y-m-d h:i K",
            defaultHour: 10,
        });

        getClients();

    });

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Create</span>
                        Appointment
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/appointments">Appointments</router-link>
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
                                            <label for="title">Title</label>
                                            <input v-model="form.title" type="text" :class="{ 'is-invalid': errors.title }" class="form-control" id="title" placeholder="Enter Title">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <!-- <input v-model="form.client_id" type="text" style="display: none;" class="form-control" id="client_id" placeholder="Enter Title"> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client">Client Name</label>
                                            <select v-model="form.client_id" id="client_id" class="form-control" :class="{ 'is-invalid': errors.client_id }">
                                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.firstname }} {{ client.lastname }}</option>
                                            </select>
                                            <span class="invalid-feedback">{{ errors.client_id }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">Start Time</label>
                                            <input v-model="form.start_time" type="text" :class="{ 'is-invalid': errors.start_time }" class="form-control flatpickr" id="start-time">
                                            <span class="invalid-feedback">{{ errors.start_time }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="time">End Time</label>
                                            <input v-model="form.end_time" type="text" :class="{'is-invalid': errors.end_time}" class="form-control flatpickr" id="end-time">
                                            <span class="invalid-feedback">{{ errors.end_time }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea v-model="form.description" :class="{ 'is-invalid': errors.description }" class="form-control" id="description" rows="3"
                                        placeholder="Enter Description"></textarea>
                                    <span class="invalid-feedback">{{ errors.description }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
