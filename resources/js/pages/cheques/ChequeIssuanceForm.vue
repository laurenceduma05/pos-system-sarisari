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
        business_name: '',
        bank: '',
        cheque_number: '',
        amount: '',
        status: '',
        remarks: '',
        due_date: '',
    });

    const available_quota = ref();
    const quota_message = ref();

    const handleSubmit = (values, actions) => {
        if (editMode.value) {
            editCheque(values, actions);
        } else {
            createCheque(values, actions);
        }
    }

    const createCheque = (values, actions) => {
        axios.post('/cheques/create', form)
        .then((response) => {
            router.push('/admin/cheques');
            toastr.success('Cheque created successfully!');
        })
        .catch((error) => {
            if (error.response && error.response.status === 400) {
                Swal.fire({
                    title: 'Quota limit reached',
                    text: "Try to adjust the amount or your limit!",
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
        })
    }

    const isQuotaFull = async () => {
        try {
            const response = await axios.get('/quotas/check-full')
        } catch (error) {
            if (error.response && error.response.status === 400) {
                Swal.fire({
                    title: 'Quota limit reached today',
                    text: "Try to adjust your limit today!",
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
            }
        }
        
    }

    const getTodayAvailbleQuota = async () => {
        try {
            const response = await axios.get('/quotas/available-quota')

            if (response.data['available_quota']) {
                available_quota.value = response.data['available_quota'];
            } else {
                quota_message.value = response.data['message'];
            }

        } catch (error) {
            if (error.response && error.response.status === 400) {
                console.error('An error occured!');
            }
        }
        
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

    const clients = ref();

    const getCheque = () => {
        axios.get(`/cheques/${route.params.id}/edit`)
        .then(({data}) => {
            form.business_name = data.business_name;
            form.bank = data.bank;
            form.cheque_number = data.cheque_number;
            form.amount = data.amount;
            form.status = data.status;
            form.remarks = data.remarks;
            form.due_date = data.due_date;
        })
    }
    const editMode = ref(false);

    const goBack = () => {
        this.router.go(-1);
    }

    onMounted(() => {

        isQuotaFull();
        getTodayAvailbleQuota();

        if (route.name === '/admin.cheques.edit') {
            editMode.value = true;
            getCheque();
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
                        <span v-else>Issue</span>
                        Cheque
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/cheques">Cheques</router-link>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="title">Business Name</label>
                                            <input v-model="form.business_name" type="text" :class="{ 'is-invalid': errors.business_name }" class="form-control" id="business_name" placeholder="Enter Business Name">
                                            <span class="invalid-feedback">{{ errors.business_name }}</span>
                                        </div>
                                    </div>
                                    <!-- <input v-model="form.client_id" type="text" style="display: none;" class="form-control" id="client_id" placeholder="Enter Title"> -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank">Bank Name</label>
                                            <select v-model="form.bank" id="bank" class="form-control" :class="{ 'is-invalid': errors.bank }">
                                                <option value="BDO">BDO</option>
                                                <option value="BPI">BPI</option>
                                                <option value="RCBC">RCBC</option>
                                                <option value="METROBANK">METROBANK</option>
                                                <option value="SECURITY BANK">SECURITY BANK</option>
                                                <option value="UNION BANK">UNION BANK</option>
                                            </select>
                                            <span class="invalid-feedback">{{ errors.bank }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cheque_number">Cheque Number</label>
                                            <input v-model="form.cheque_number" type="text" :class="{ 'is-invalid': errors.cheque_number }" class="form-control" id="cheque_number" placeholder="Enter last 4 digits of cheque">
                                            <span class="invalid-feedback">{{ errors.cheque_number }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="time">Amount</label>
                                            <input v-model="form.amount" type="text" :class="{ 'is-invalid': errors.amount }" class="form-control" id="amount" placeholder="Enter Amount">
                                            <span class="invalid-feedback">{{ errors.amount }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select v-model="form.status" id="status" class="form-control" :class="{ 'is-invalid': errors.status }">
                                                <option value="Payables">Payables</option>
                                                <option value="Paid">Paid</option>
                                            </select>
                                            <span class="invalid-feedback">{{ errors.status }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="due_date">Due Date</label>
                                                <input 
                                                    type="date" 
                                                    id="due_date" 
                                                    v-model="form.due_date" 
                                                    :class="{ 'is-invalid': errors.cheque_number }"
                                                    class="form-control" 
                                                    @change="getCheques"
                                                />
                                            <span class="invalid-feedback">{{ errors.due_date }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea v-model="form.remarks" class="form-control" id="remarks" rows="3"
                                        placeholder="Enter Remarks"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <router-link to="/admin/cheques">
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
