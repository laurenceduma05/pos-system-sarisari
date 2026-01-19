<script setup>
    import axios from 'axios';
    import { reactive, onMounted, ref, computed } from 'vue';
    import { useRouter, useRoute } from 'vue-router'; // this will use to redirecting page!
    import { useToastr } from '@/toastr'; // this is for success messages
    import { Form } from 'vee-validate'; // this will use to validate form!
    import flatpickr from 'flatpickr'; // this is use for the appointment start time and endtime behavior etc..
    import 'flatpickr/dist/themes/dark.css';
    import { formatAmount } from '../../helper.js';
    import Swal from 'sweetalert2';

    const router = useRouter();
    const route = useRoute();
    const toastr = useToastr();
    const form = reactive({
        quota_limit: '',
        day_start: '',
    });

    const editQuotaLimit = ref();

    const handleSubmit = (values, actions) => {
        if (editMode.value) {
            editQuota(values, actions);
        } else {
            createQuota(values, actions);
        }
    }

    const createQuota = (values, actions) => {
        axios.post('/quotas/create', form)
        .then((response) => {
            router.push('/admin/quotas');
            toastr.success('Quotas created successfully!');
        })
        .catch((error) => {
            actions.setErrors(error.response.data.errors);
        })
    }

    const editQuota = (values, actions) => {
        axios.put(`/quotas/${route.params.id}/edit`, form)
        .then((response) => {
            router.push('/admin/quotas');
            toastr.success('Quotas updated successfully!');
        })
        .catch((error) => {
            if (error.response && error.response.status === 400) {
                Swal.fire({
                    title: 'Update Quota Warning',
                    text: "There's an existing quota for that date",
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok, got it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.close();
                    }
                })
            } else {
                actions.setErrors(error.response.data.errors);
            }
        });
    }

    const getQuota = () => {
        axios.get(`/quotas/${route.params.id}/edit`)
        .then(({data}) => {
            editQuotaLimit.value = formattedAmount(data.quota_limit);
            form.quota_limit = data.quota_limit;
            form.day_start = data.day_start;
        })
    }

    const formattedAmount = (amount) => {
        if (!amount) return '';
        return parseFloat(amount).toLocaleString();
    }

    // Computed property for formatted value
    const formattedQuotaLimit = computed({
        get() {
            return formattedAmount(form.quota_limit);
        },
        set(newValue) {
            // Remove commas and convert back to a number
            const parsedValue = parseFloat(newValue.replace(/,/g, ""));
            form.quota_limit = isNaN(parsedValue) ? 0 : parsedValue;
        }
    });

    const editMode = ref(false);

    const goBack = () => {
        this.router.go(-1);
    }

    onMounted(() => {

        if (route.name === '/admin.quotas.edit') {
            editMode.value = true;
            getQuota();
        }

        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "y-m-d",
            defaultHour: 10,
            minDate: new Date(),
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
                        <span v-else>Create</span>
                        Quota
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/quota">Quotas</router-link>
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
                                            <label for="title">Quota Limit</label>
                                            <input v-model="formattedQuotaLimit" type="text" :class="{ 'is-invalid': errors.quota_limit }" class="form-control" id="quota_limit" placeholder="Enter Quota Limit">
                                            <span class="invalid-feedback">{{ errors.quota_limit }}</span>
                                        </div>
                                    </div>
                                    <!-- <input v-model="form.client_id" type="text" style="display: none;" class="form-control" id="client_id" placeholder="Enter Title"> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="day_start">Day Start</label>
                                            <input 
                                                id="day_start" 
                                                v-model="form.day_start" 
                                                class="form-control flatpickr" 
                                                @change="getCheques"
                                                placeholder="SELECT DATE"
                                            />
                                            <span class="invalid-feedback">{{ errors.day_start }}</span>
                                        </div>
                                    </div>
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
