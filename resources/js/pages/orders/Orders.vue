<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">POS System - Orders Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button @click="generateReport" class="btn btn-secondary">
            <i class="fas fa-file-pdf"></i> Export Report
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
              <h3>{{ orders.length }}</h3>
              <p>Total Orders</p>
            </div>
            <div class="icon">
              <i class="fas fa-shopping-bag"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ totalSales.toFixed(2) }}</h3>
              <p>Total Sales (₱)</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ completedOrders }}</h3>
              <p>Completed Orders</p>
            </div>
            <div class="icon">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ cancelledOrders }}</h3>
              <p>Cancelled Orders</p>
            </div>
            <div class="icon">
              <i class="fas fa-ban"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label>Status</label>
                  <select v-model="filterStatus" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label>Payment Method</label>
                  <select v-model="filterPaymentMethod" class="form-control">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="mobile">Mobile Payment</option>
                    <option value="credit">Store Credit</option>
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label>From Date</label>
                  <input
                    v-model="filterFromDate"
                    type="date"
                    class="form-control"
                  />
                </div>
                <div class="form-group col-md-3">
                  <label>To Date</label>
                  <input
                    v-model="filterToDate"
                    type="date"
                    class="form-control"
                  />
                </div>
              </div>
              <button @click="clearFilters" class="btn btn-sm btn-secondary">
                <i class="fas fa-redo"></i> Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">All Orders</h3>
              <div class="card-tools">
                <input
                  v-model="searchQuery"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Search by order number, customer..."
                  style="width: 300px"
                />
              </div>
            </div>
            <div class="card-body">
              <table class="table table-striped table-hover table-sm">
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Cashier</th>
                    <th>Items</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="order in filteredOrders"
                    :key="order.id"
                    :class="{
                      'table-danger': order.status === 'cancelled',
                    }"
                  >
                    <td>
                      <strong>{{ order.order_number }}</strong>
                    </td>
                    <td>{{ order.customer?.name || "Walk-in" }}</td>
                    <td>{{ order.user?.name || "—" }}</td>
                    <td>{{ getOrderItemsCount(order) }}</td>
                    <td>{{ order.subtotal }}</td>
                    <td>{{ order.tax }}</td>
                    <td>
                      <strong>{{ order.total }}</strong>
                    </td>
                    <td>
                      <span
                        class="badge"
                        :class="getPaymentBadge(order.payment_method)"
                      >
                        {{ formatPaymentMethod(order.payment_method) }}
                      </span>
                    </td>
                    <td>
                      <span class="badge" :class="getStatusBadge(order.status)">
                        {{ order.status }}
                      </span>
                    </td>
                    <td>{{ formatDate(order.created_at) }}</td>
                    <td>
                      <button
                        @click="viewOrder(order)"
                        class="btn btn-xs btn-primary"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                      <button
                        v-if="order.status !== 'cancelled'"
                        @click="cancelOrder(order.id)"
                        class="btn btn-xs btn-danger"
                      >
                        <i class="fas fa-times"></i>
                      </button>
                      <button
                        v-if="order.status === 'cancelled'"
                        @click="restoreOrder(order.id)"
                        class="btn btn-xs btn-warning"
                      >
                        <i class="fas fa-undo"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredOrders.length === 0">
                    <td colspan="11" class="text-center text-muted">
                      No orders found
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Details Modal -->
      <div
        v-if="showDetailModal"
        class="modal show d-block"
        style="background-color: rgba(0, 0, 0, 0.5)"
      >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Order Details</h5>
              <button
                @click="showDetailModal = false"
                type="button"
                class="close"
              >
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="selectedOrder" class="row mb-3">
                <div class="col-md-6">
                  <p>
                    <strong>Order Number:</strong>
                    {{ selectedOrder.order_number }}
                  </p>
                  <p>
                    <strong>Customer:</strong>
                    {{ selectedOrder.customer?.name || "Walk-in" }}
                  </p>
                  <p>
                    <strong>Cashier:</strong>
                    {{ selectedOrder.user?.name || "—" }}
                  </p>
                  <p>
                    <strong>Date:</strong>
                    {{ formatDate(selectedOrder.created_at) }}
                  </p>
                </div>
                <div class="col-md-6">
                  <p>
                    <strong>Payment Method:</strong>
                    {{ formatPaymentMethod(selectedOrder.payment_method) }}
                  </p>
                  <p>
                    <strong>Status:</strong>
                    <span
                      class="badge"
                      :class="getStatusBadge(selectedOrder.status)"
                    >
                      {{ selectedOrder.status }}
                    </span>
                  </p>
                  <p>
                    <strong>Paid Amount:</strong>
                    {{ selectedOrder.paid_amount }}
                  </p>
                  <p v-if="selectedOrder.change_amount">
                    <strong>Change:</strong>
                    {{ selectedOrder.change_amount }}
                  </p>
                </div>
              </div>

              <h6>Order Items</h6>
              <table class="table table-sm table-bordered">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in selectedOrder.items" :key="item.id">
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.product_sku }}</td>
                    <td>{{ item.price }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.subtotal }}</td>
                  </tr>
                </tbody>
              </table>

              <div class="row">
                <div class="col-md-6 offset-md-6">
                  <table class="table table-sm">
                    <tr>
                      <td><strong>Subtotal:</strong></td>
                      <td class="text-right">
                        {{ selectedOrder.subtotal }}
                      </td>
                    </tr>
                    <tr>
                      <td><strong>Tax:</strong></td>
                      <td class="text-right">{{ selectedOrder.tax }}</td>
                    </tr>
                    <tr v-if="selectedOrder.discount">
                      <td><strong>Discount:</strong></td>
                      <td class="text-right">-{{ selectedOrder.discount }}</td>
                    </tr>
                    <tr class="table-active">
                      <td><strong>Total:</strong></td>
                      <td class="text-right">
                        <strong>{{ selectedOrder.total }}</strong>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

              <p v-if="selectedOrder.notes" class="mt-2">
                <strong>Notes:</strong>
                <br />
                {{ selectedOrder.notes }}
              </p>
            </div>
            <div class="modal-footer">
              <button
                @click="printReceipt"
                type="button"
                class="btn btn-primary"
              >
                <i class="fas fa-print"></i> Print Receipt
              </button>
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
  name: "Orders",
  data() {
    return {
      orders: [],
      showDetailModal: false,
      selectedOrder: null,
      searchQuery: "",
      filterStatus: "",
      filterPaymentMethod: "",
      filterFromDate: "",
      filterToDate: "",
    };
  },
  computed: {
    totalSales() {
      return this.orders
        .filter((o) => o.status === "completed")
        .reduce((sum, o) => sum + parseFloat(o.total || 0), 0);
    },
    completedOrders() {
      return this.orders.filter((o) => o.status === "completed").length;
    },
    cancelledOrders() {
      return this.orders.filter((o) => o.status === "cancelled").length;
    },
    filteredOrders() {
      return this.orders.filter((order) => {
        const matchesSearch =
          !this.searchQuery ||
          order.order_number.includes(this.searchQuery) ||
          (order.customer &&
            order.customer.name
              .toLowerCase()
              .includes(this.searchQuery.toLowerCase()));

        const matchesStatus =
          !this.filterStatus || order.status === this.filterStatus;
        const matchesPayment =
          !this.filterPaymentMethod ||
          order.payment_method === this.filterPaymentMethod;

        const orderDate = new Date(order.created_at)
          .toISOString()
          .split("T")[0];
        const matchesFromDate =
          !this.filterFromDate || orderDate >= this.filterFromDate;
        const matchesToDate =
          !this.filterToDate || orderDate <= this.filterToDate;

        return (
          matchesSearch &&
          matchesStatus &&
          matchesPayment &&
          matchesFromDate &&
          matchesToDate
        );
      });
    },
  },
  methods: {
    async fetchOrders() {
      try {
        const response = await axios.get("/admin/pos/orders");
        this.orders = response.data.data || response.data;
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    },
    viewOrder(order) {
      this.selectedOrder = order;
      this.showDetailModal = true;
    },
    async cancelOrder(orderId) {
      if (confirm("Are you sure you want to cancel this order?")) {
        try {
          await axios.post(`/admin/pos/orders/${orderId}/cancel`);
          this.fetchOrders();
          this.showDetailModal = false;
        } catch (error) {
          console.error("Error cancelling order:", error);
          alert(
            "Error cancelling order: " + error.response?.data?.message ||
              error.message,
          );
        }
      }
    },
    async restoreOrder(orderId) {
      if (confirm("Are you sure you want to restore this order?")) {
        try {
          await axios.post(`/admin/pos/orders/${orderId}/restore`);
          this.fetchOrders();
          this.showDetailModal = false;
        } catch (error) {
          console.error("Error restoring order:", error);
          alert(
            "Error restoring order: " + error.response?.data?.message ||
              error.message,
          );
        }
      }
    },
    clearFilters() {
      this.filterStatus = "";
      this.filterPaymentMethod = "";
      this.filterFromDate = "";
      this.filterToDate = "";
    },
    generateReport() {
      alert("Report generation coming soon!");
    },
    printReceipt() {
      if (!this.selectedOrder) return;

      const printWindow = window.open("", "_blank");

      const items = this.selectedOrder.items || [];
      const itemsHtml = items
        .map(
          (item) => `
        <tr>
          <td>${item.product_name || item.name}</td>
          <td>${item.product_sku || item.sku || "—"}</td>
          <td>${item.price || 0}</td>
          <td>${item.quantity || 0}</td>
          <td>${item.subtotal || item.price * item.quantity || 0}</td>
        </tr>
      `,
        )
        .join("");

      const receiptHtml = `
        <!DOCTYPE html>
        <html>
          <head>
            <title>Receipt - ${this.selectedOrder.order_number}</title>
            <style>
              body {
                font-family: Arial, sans-serif;
                margin: 20px;
                font-size: 14px;
              }
              .receipt {
                max-width: 400px;
                margin: 0 auto;
                border: 1px solid #ddd;
                padding: 20px;
              }
              .header {
                text-align: center;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
                margin-bottom: 20px;
              }
              .section {
                margin-bottom: 15px;
              }
              table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
              }
              table th, table td {
                padding: 5px;
                text-align: left;
                border-bottom: 1px solid #ddd;
              }
              .total-section {
                margin-top: 20px;
                border-top: 2px solid #333;
                padding-top: 10px;
              }
              .text-right {
                text-align: right;
              }
              .text-center {
                text-align: center;
              }
              .bold {
                font-weight: bold;
              }
              @media print {
                body { margin: 0; padding: 10px; }
                .no-print { display: none; }
              }
            </style>
          </head>
          <body>
            <div class="receipt">
              <div class="header">
                <h2 style="margin: 0;">Order Details</h2>
              </div>

              <div class="section">
                <p><strong>Order Number:</strong> ${this.selectedOrder.order_number}</p>
                <p><strong>Payment Method:</strong> ${this.formatPaymentMethod(this.selectedOrder.payment_method)}</p>
              </div>

              <div class="section">
                <p><strong>Customer:</strong> ${this.selectedOrder.customer?.name || "Walk-in"}</p>
                <p><strong>Status:</strong> ${this.selectedOrder.status}</p>
              </div>

              <div class="section">
                <p><strong>Cashier:</strong> ${this.selectedOrder.user?.name || "—"}</p>
                <p><strong>Paid Amount:</strong> ${this.selectedOrder.paid_amount || this.selectedOrder.total}</p>
              </div>

              <div class="section">
                <p><strong>Date:</strong> ${new Date(this.selectedOrder.created_at).toLocaleString()}</p>
                <p><strong>Change:</strong> ${this.selectedOrder.change_amount || 0}</p>
              </div>

              <div class="section">
                <h3>Order Items</h3>
                <table>
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>SKU</th>
                      <th>Price</th>
                      <th>Qty</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${itemsHtml}
                  </tbody>
                </table>
              </div>

              <div class="total-section">
                <p><strong>Subtotal:</strong></p>
                <p>\[ ${this.selectedOrder.subtotal || this.selectedOrder.total} \]</p>
                
                <p><strong>Tax:</strong></p>
                <p>\[ ${this.selectedOrder.tax || 0} \]</p>
                
                ${
                  this.selectedOrder.discount
                    ? `
                  <p><strong>Discount:</strong></p>
                  <p>\[ -${this.selectedOrder.discount} \]</p>
                `
                    : ""
                }
                
                <p><strong>Total:</strong></p>
                <p>\[ ${this.selectedOrder.total} \]</p>
              </div>

              <div class="section text-center">
                <p>Thank you for your purchase!</p>
              </div>
            </div>
            
            <div class="no-print" style="text-align: center; margin-top: 20px;">
              <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px;">
                Print Receipt
              </button>
              <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; margin-left: 10px;">
                Close
              </button>
            </div>
          </body>
        </html>
      `;

      printWindow.document.write(receiptHtml);
      printWindow.document.close();
      printWindow.focus();

      // Auto print after a short delay
      setTimeout(() => {
        printWindow.print();
      }, 500);
    },
    formatDate(date) {
      return (
        new Date(date).toLocaleDateString() +
        " " +
        new Date(date).toLocaleTimeString()
      );
    },
    formatPaymentMethod(method) {
      const methods = {
        cash: "Cash",
        card: "Card",
        mobile: "Mobile Payment",
        credit: "Store Credit",
      };
      return methods[method] || method;
    },
    getStatusBadge(status) {
      const badges = {
        completed: "badge-success",
        pending: "badge-warning",
        cancelled: "badge-danger",
      };
      return badges[status] || "badge-secondary";
    },
    getPaymentBadge(method) {
      const badges = {
        cash: "badge-success",
        card: "badge-info",
        mobile: "badge-primary",
        credit: "badge-warning",
      };
      return badges[method] || "badge-secondary";
    },
    getOrderItemsCount(order) {
      return (order.items || []).reduce((sum, item) => sum + item.quantity, 0);
    },
  },
  mounted() {
    this.fetchOrders();
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

@media print {
  .modal-header,
  .modal-footer,
  .card-tools,
  button {
    display: none;
  }
}
</style>
