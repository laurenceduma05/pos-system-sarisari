<script setup>
    import axios from 'axios';
    import { reactive, onMounted, ref } from 'vue';
    import { useRouter, useRoute } from 'vue-router';
    import { useToastr } from '@/toastr';
    import { Form } from 'vee-validate';
    import flatpickr from 'flatpickr';
    import 'flatpickr/dist/themes/dark.css';
    import Swal from 'sweetalert2';
    import { formatAmount } from '../../helper.js';

    const router = useRouter();
    const route = useRoute();
    const toastr = useToastr();

    const form = reactive({
        title: '',
        by: '',
        details: '',
        link: '',
        start_time: '',
        end_time: '',
    });

    const available_quota = ref();
    const quota_message = ref();
    const editMode = ref(false);
    const isLoading = ref(false);
    const validationErrors = ref({});

    const handleSubmit = (values, actions) => {
        validationErrors.value = {};
        if (editMode.value) {
            editEvent(values, actions);
        } else {
            createEvent(values, actions);
        }
    }

    const createEvent = async (values, actions) => {
        isLoading.value = true;
        try {
            console.log('Form data before submit:', form);

            // Simple date formatting - remove seconds if causing issues
            const submitData = {
                title: form.title,
                by: form.by,
                details: form.details,
                link: form.link,
                start_time: form.start_time,
                end_time: form.end_time,
            };

            console.log('Submitting data:', submitData);

            const response = await axios.post('/events/create', submitData);

            toastr.success('Event created successfully!');
            router.push('/admin/events');
        } catch (error) {
            console.error('Full error object:', error);
            console.error('Error response:', error.response);

            if (error.response && error.response.data.errors) {
                validationErrors.value = error.response.data.errors;
                console.log('Validation errors:', validationErrors.value);

                // Show all validation errors
                Object.values(error.response.data.errors).forEach(errorArray => {
                    toastr.error(errorArray[0]);
                });
            } else if (error.response && error.response.data.error) {
                toastr.error(error.response.data.error);
            } else if (error.response && error.response.data.message) {
                toastr.error(error.response.data.message);
            } else {
                toastr.error('Failed to create event: ' + error.message);
            }
        } finally {
            isLoading.value = false;
        }
    }

    const editEvent = async (values, actions) => {
        isLoading.value = true;
        try {
            const submitData = {
                title: form.title,
                by: form.by,
                details: form.details,
                link: form.link,
                start_time: form.start_time,
                end_time: form.end_time,
            };

            const response = await axios.put(`/events/${route.params.id}/edit`, submitData);

            toastr.success('Event updated successfully!');
            router.push('/admin/events');
        } catch (error) {
            console.error('Error updating event:', error);
            if (error.response && error.response.data.errors) {
                validationErrors.value = error.response.data.errors;
                Object.values(error.response.data.errors).forEach(errorArray => {
                    toastr.error(errorArray[0]);
                });
            } else {
                toastr.error(error.response?.data?.message || 'Failed to update event');
            }
        } finally {
            isLoading.value = false;
        }
    }

    const getEvent = async (id) => {
        try {
            const response = await axios.get(`/events/${id}/edit`);
            Object.assign(form, response.data);
        } catch (error) {
            console.error('Error fetching event:', error);
            toastr.error('Failed to load event data');
            router.push('/admin/events');
        }
    }

    onMounted(() => {
        if (route.name === 'admin.events.edit' && route.params.id) {
            editMode.value = true;
            getEvent(route.params.id);
        }

        // Initialize date pickers with simpler configuration
        flatpickr(".start-time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i", // Remove seconds
            defaultHour: 10,
            time_24hr: true,
            allowInput: true
        });

        flatpickr(".end-time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i", // Remove seconds
            defaultHour: 17,
            time_24hr: true,
            allowInput: true
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
                        Event
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/events">Events</router-link>
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
                            <!-- Debug info -->
                            <div v-if="Object.keys(validationErrors).length > 0" class="alert alert-danger">
                                <strong>Validation Errors:</strong>
                                <ul class="mb-0">
                                    <li v-for="(errors, field) in validationErrors" :key="field">
                                        <strong>{{ field }}:</strong> {{ errors[0] }}
                                    </li>
                                </ul>
                            </div>

                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Event Title *</label>
                                            <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': validationErrors.title }" id="title" placeholder="Enter Event Title">
                                            <div class="invalid-feedback" v-if="validationErrors.title">
                                                {{ validationErrors.title[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="by">Event By *</label>
                                            <input v-model="form.by" type="text" class="form-control" :class="{ 'is-invalid': validationErrors.by }" id="by" placeholder="Enter Event by">
                                            <div class="invalid-feedback" v-if="validationErrors.by">
                                                {{ validationErrors.by[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_time">Start Time *</label>
                                            <input v-model="form.start_time" type="text" class="form-control start-time" :class="{ 'is-invalid': validationErrors.start_time }" id="start_time" placeholder="Select Start Time">
                                            <div class="invalid-feedback" v-if="validationErrors.start_time">
                                                {{ validationErrors.start_time[0] }}
                                            </div>
                                            <small class="form-text text-muted">Format: YYYY-MM-DD HH:MM</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_time">End Time *</label>
                                            <input v-model="form.end_time" type="text" class="form-control end-time" :class="{ 'is-invalid': validationErrors.end_time }" id="end_time" placeholder="Select End Time">
                                            <div class="invalid-feedback" v-if="validationErrors.end_time">
                                                {{ validationErrors.end_time[0] }}
                                            </div>
                                            <small class="form-text text-muted">Format: YYYY-MM-DD HH:MM</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="details">Event Details *</label>
                                            <textarea v-model="form.details" class="form-control" :class="{ 'is-invalid': validationErrors.details }" id="details" rows="4" placeholder="Enter event details"></textarea>
                                            <div class="invalid-feedback" v-if="validationErrors.details">
                                                {{ validationErrors.details[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="link">Event Link</label>
                                            <span class="text-muted small ml-2">(Google Meet, Zoom, or other relevant links)</span>
                                            <input v-model="form.link" type="text" class="form-control" :class="{ 'is-invalid': validationErrors.link }" id="link" placeholder="Enter event link">
                                            <div class="invalid-feedback" v-if="validationErrors.link">
                                                {{ validationErrors.link[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" :disabled="isLoading">
                                            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></span>
                                            <span v-if="editMode && !isLoading">Update Event</span>
                                            <span v-if="!editMode && !isLoading">Create Event</span>
                                            <span v-if="isLoading">Processing...</span>
                                        </button>
                                        <router-link to="/admin/events" class="btn btn-secondary ml-2">
                                            Cancel
                                        </router-link>
                                    </div>
                                </div>
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
        color: #3498db;
    }

    .quota-container:hover {
        background-color: #f0f8ff;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .bg-red {
        background-color: #dc3545 !important;
    }

    .text-white {
        color: white !important;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
</style>
