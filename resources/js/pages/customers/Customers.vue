<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">POS System - Customers Management</h1>
          <!-- Breadcrumbs -->
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mt-2 mb-0">
              <li class="breadcrumb-item">
                <a href="/admin/dashboard">
                  <i class="fas fa-home"></i> Home
                </a>
              </li>
              <li class="breadcrumb-item">
                <a href="/admin/pos">POS</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Customers
              </li>
            </ol>
          </nav>
        </div>
        <div class="col-sm-6 text-right">
          <button @click="openAddForm" type="button" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Customer
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <!-- Stats -->
      <div class="row mb-3">
        <div class="col-md-3">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>{{ customers.length }}</h3>
              <p>Total Customers</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ totalCreditLimit.toFixed(2) }}</h3>
              <p>Total Credit Limit (₱)</p>
            </div>
            <div class="icon">
              <i class="fas fa-credit-card"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ totalUsedCredit.toFixed(2) }}</h3>
              <p>Total Used Credit (₱)</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-check-alt"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ (totalCreditLimit - totalUsedCredit).toFixed(2) }}</h3>
              <p>Available Credit (₱)</p>
            </div>
            <div class="icon">
              <i class="fas fa-piggy-bank"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Form -->
      <div v-if="showAddForm" class="row mb-3">
        <div class="col-md-12">
          <div class="card card-primary">
            <div
              class="card-header d-flex justify-content-between align-items-center"
            >
              <h3 class="card-title">
                {{ editingId ? "Edit Customer" : "Add New Customer" }}
              </h3>
              <button
                @click="cancelForm"
                type="button"
                class="btn btn-sm btn-secondary ms-auto"
                style="margin-left: auto"
              >
                <i class="fas fa-times"></i> Close
              </button>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label class="small">Full Name *</label>
                  <input
                    v-model="formData.name"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="Customer name"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Email</label>
                  <input
                    v-model="formData.email"
                    type="email"
                    class="form-control form-control-sm"
                    placeholder="customer@example.com"
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Phone *</label>
                  <input
                    v-model="formData.phone"
                    type="tel"
                    class="form-control form-control-sm"
                    placeholder="Phone number"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Credit Limit (₱) *</label>
                  <input
                    v-model.number="formData.credit_limit"
                    type="number"
                    step="0.01"
                    class="form-control form-control-sm"
                    placeholder="0.00"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="small">Address</label>
                <textarea
                  v-model="formData.address"
                  class="form-control form-control-sm"
                  rows="2"
                  placeholder="Customer address..."
                ></textarea>
              </div>
            </div>
            <div class="card-footer">
              <button @click="cancelForm" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Cancel
              </button>
              <button @click="saveCustomer" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> Save
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Customers Table -->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">All Customers</h3>
              <div class="card-tools">
                <input
                  v-model="searchQuery"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Search by name, email, or phone..."
                  style="width: 350px"
                />
              </div>
            </div>
            <div class="card-body">
              <table class="table table-striped table-hover table-sm">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Credit Limit</th>
                    <th>Balance</th>
                    <th>Available</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="customer in filteredCustomers"
                    :key="customer.id"
                    :class="{
                      'table-danger': customer.balance >= customer.credit_limit,
                      'table-warning':
                        customer.balance > customer.credit_limit * 0.7,
                    }"
                  >
                    <td>
                      <strong>{{ customer.name }}</strong>
                    </td>
                    <td>{{ customer.email || "—" }}</td>
                    <td>{{ customer.phone }}</td>
                    <td>{{ customer.credit_limit }}</td>
                    <td>
                      <span
                        :class="{
                          'text-danger':
                            customer.balance > customer.credit_limit * 0.7,
                        }"
                      >
                        {{ customer.balance }}
                      </span>
                    </td>
                    <td>
                      <span
                        :class="{
                          'text-success':
                            customer.balance < customer.credit_limit * 0.5,
                          'text-warning':
                            customer.balance >= customer.credit_limit * 0.5 &&
                            customer.balance < customer.credit_limit,
                          'text-danger':
                            customer.balance >= customer.credit_limit,
                        }"
                      >
                        {{
                          (customer.credit_limit - customer.balance).toFixed(2)
                        }}
                      </span>
                    </td>
                    <td>
                      <button
                        @click="viewCustomer(customer)"
                        class="btn btn-xs btn-primary"
                        title="View Details"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                      <button
                        @click="editCustomer(customer)"
                        class="btn btn-xs btn-info"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        @click="deleteCustomer(customer.id)"
                        class="btn btn-xs btn-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredCustomers.length === 0">
                    <td colspan="7" class="text-center text-muted">
                      No customers found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer Details Modal -->
      <div
        v-if="showDetailModal"
        class="modal show d-block"
        style="background-color: rgba(0, 0, 0, 0.5)"
      >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Customer Details</h5>
              <button
                @click="showDetailModal = false"
                type="button"
                class="close"
              >
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="selectedCustomer" class="row">
                <div class="col-md-6">
                  <p>
                    <strong>Name:</strong>
                    {{ selectedCustomer.name }}
                  </p>
                  <p>
                    <strong>Email:</strong>
                    {{ selectedCustomer.email || "—" }}
                  </p>
                  <p>
                    <strong>Phone:</strong>
                    {{ selectedCustomer.phone }}
                  </p>
                  <p>
                    <strong>Address:</strong>
                    {{ selectedCustomer.address || "—" }}
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Credit Limit:</strong>
                    {{ selectedCustomer.credit_limit }}
                  </p>
                  <p>
                    <strong>Current Balance:</strong>
                    {{ selectedCustomer.balance }}
                  </p>
                  <p>
                    <strong>Available Credit:</strong>
                    {{
                      (
                        selectedCustomer.credit_limit - selectedCustomer.balance
                      ).toFixed(2)
                    }}
                  </p>
                  <p>
                    <strong>Total Purchases:</strong>
                    {{ selectedCustomer.total_purchases || 0 }}
                  </p>
                </div>
              </div>
              <div v-if="customerOrders.length > 0" class="mt-3">
                <h6>Recent Orders</h6>
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Date</th>
                      <th>Total</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="order in customerOrders.slice(0, 5)"
                      :key="order.id"
                    >
                      <td>{{ order.order_number }}</td>
                      <td>{{ formatDate(order.created_at) }}</td>
                      <td>{{ order.total }}</td>
                      <td>
                        <span
                          class="badge"
                          :class="getStatusBadge(order.status)"
                        >
                          {{ order.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button
                @click="showDetailModal = false"
                type="button"
                class="btn btn-secondary"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Customers",
  data() {
    return {
      customers: [],
      customerOrders: [],
      formData: {
        name: "",
        email: "",
        phone: "",
        address: "",
        credit_limit: 0,
      },
      editingId: null,
      showAddForm: false,
      showDetailModal: false,
      selectedCustomer: null,
      searchQuery: "",
    };
  },
  computed: {
    totalCreditLimit() {
      try {
        if (!Array.isArray(this.customers)) return 0;
        return this.customers.reduce(
          (sum, c) => sum + (parseFloat(c.credit_limit) || 0),
          0,
        );
      } catch (e) {
        console.error("Error calculating totalCreditLimit:", e);
        return 0;
      }
    },
    totalUsedCredit() {
      try {
        if (!Array.isArray(this.customers)) return 0;
        return this.customers.reduce(
          (sum, c) => sum + (parseFloat(c.balance) || 0),
          0,
        );
      } catch (e) {
        console.error("Error calculating totalUsedCredit:", e);
        return 0;
      }
    },
    filteredCustomers() {
      if (!this.searchQuery) return this.customers || [];
      const q = this.searchQuery.toLowerCase();
      return (this.customers || []).filter(
        (c) =>
          (c.name && c.name.toLowerCase().includes(q)) ||
          (c.email && c.email.toLowerCase().includes(q)) ||
          (c.phone && c.phone.toLowerCase().includes(q)),
      );
    },
  },
  methods: {
    openAddForm() {
      this.showAddForm = true;
      this.editingId = null;
      this.formData = {
        name: "",
        email: "",
        phone: "",
        address: "",
        credit_limit: 0,
      };
    },
    async fetchCustomers() {
      try {
        const response = await axios.get("/admin/pos/customers");
        // Handle both paginated and simple array responses
        this.customers = response.data.data || response.data || [];

        // Ensure balance is set for all customers
        this.customers = this.customers.map((customer) => ({
          ...customer,
          balance: customer.balance || 0,
          credit_limit: customer.credit_limit || 0,
        }));
      } catch (error) {
        console.error("Error fetching customers:", error);
        this.customers = [];
      }
    },
    async saveCustomer() {
      try {
        // Validate required fields
        if (
          !this.formData.name ||
          !this.formData.phone ||
          !this.formData.credit_limit
        ) {
          alert(
            "Please fill in all required fields (Name, Phone, Credit Limit)",
          );
          return;
        }

        if (this.editingId) {
          const response = await axios.put(
            `/admin/pos/customers/${this.editingId}`,
            this.formData,
          );
          console.log("Update response:", response);
        } else {
          const response = await axios.post(
            "/admin/pos/customers",
            this.formData,
          );
          console.log("Create response:", response);
        }

        // Refresh the customer list
        await this.fetchCustomers();
        this.cancelForm();

        // Show success message
        const message = this.editingId
          ? "Customer updated successfully!"
          : "Customer added successfully!";
        alert(message);
      } catch (error) {
        console.error("Error saving customer:", error);
        const errorMessage =
          error.response?.data?.message ||
          error.response?.data?.errors?.email?.[0] ||
          error.message;
        alert("Error saving customer: " + errorMessage);
      }
    },
    editCustomer(customer) {
      this.editingId = customer.id;
      this.formData = { ...customer };
      this.showAddForm = true;
    },
    async viewCustomer(customer) {
      this.selectedCustomer = customer;
      this.showDetailModal = true;
      try {
        const response = await axios.get(
          `/admin/pos/customers/${customer.id}/orders`,
        );
        // Handle both paginated and simple array responses
        this.customerOrders = response.data.data || response.data || [];
      } catch (error) {
        console.error("Error fetching customer orders:", error);
        this.customerOrders = [];
      }
    },
    async deleteCustomer(id) {
      if (confirm("Are you sure you want to delete this customer?")) {
        try {
          await axios.delete(`/admin/pos/customers/${id}`);
          this.fetchCustomers();
        } catch (error) {
          console.error("Error deleting customer:", error);
          alert(
            "Error deleting customer: " + error.response?.data?.message ||
              error.message,
          );
        }
      }
    },
    cancelForm() {
      this.showAddForm = false;
      this.editingId = null;
      this.formData = {
        name: "",
        email: "",
        phone: "",
        address: "",
        credit_limit: 0,
      };
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    getStatusBadge(status) {
      const badges = {
        completed: "badge-success",
        pending: "badge-warning",
        cancelled: "badge-danger",
      };
      return badges[status] || "badge-secondary";
    },
  },
  mounted() {
    console.log("Customers component mounted");
    this.fetchCustomers().catch((err) => {
      console.error("Failed to fetch customers on mount:", err);
    });
  },
};
</script>

<style scoped>
.btn-xs {
  padding: 0.25rem 0.4rem;
  font-size: 0.875rem;
  margin: 0 2px;
}

.table-danger {
  background-color: #f8d7da !important;
}

.table-warning {
  background-color: #fff3cd !important;
}

.modal.d-block {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1050;
}

.modal-dialog {
  margin-top: 5vh;
}

.breadcrumb-item a {
  color: #007bff;
  text-decoration: none;
}

.breadcrumb-item a:hover {
  color: #0056b3;
  text-decoration: underline;
}

.breadcrumb-item.active {
  color: #6c757d;
}
</style>
