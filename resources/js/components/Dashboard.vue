<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">POS System - Dashboard & Reports</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button @click="generateReport" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Generate Report
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <!-- Date Range Selector -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="form-row">
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
                <div class="form-group col-md-3">
                  <label>&nbsp;</label>
                  <button
                    @click="applyDateFilter"
                    class="btn btn-primary btn-block"
                  >
                    <i class="fas fa-search"></i> Filter
                  </button>
                </div>
                <div class="form-group col-md-3">
                  <label>&nbsp;</label>
                  <button
                    @click="resetDateFilter"
                    class="btn btn-secondary btn-block"
                  >
                    <i class="fas fa-redo"></i> Reset
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="row mb-3">
        <div class="col-md-3">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>{{ totalOrders }}</h3>
              <p>Total Orders</p>
            </div>
            <div class="icon">
              <i class="fas fa-shopping-bag"></i>
            </div>
            <a href="#" class="small-box-footer"
              >More info <i class="fas fa-arrow-circle-right"></i
            ></a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ totalRevenue.toFixed(2) }}</h3>
              <p>Total Revenue (₱)</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="#" class="small-box-footer"
              >More info <i class="fas fa-arrow-circle-right"></i
            ></a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ averageOrderValue.toFixed(2) }}</h3>
              <p>Average Order Value</p>
            </div>
            <div class="icon">
              <i class="fas fa-chart-pie"></i>
            </div>
            <a href="#" class="small-box-footer"
              >More info <i class="fas fa-arrow-circle-right"></i
            ></a>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ totalTax.toFixed(2) }}</h3>
              <p>Total Tax Collected</p>
            </div>
            <div class="icon">
              <i class="fas fa-percentage"></i>
            </div>
            <a href="#" class="small-box-footer"
              >More info <i class="fas fa-arrow-circle-right"></i
            ></a>
          </div>
        </div>
      </div>

      <!-- Secondary KPI Cards -->
      <div class="row mb-3">
        <div class="col-md-3">
          <div class="small-box bg-primary">
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
          <div class="small-box bg-secondary">
            <div class="inner">
              <h3>{{ pendingOrders }}</h3>
              <p>Pending Orders</p>
            </div>
            <div class="icon">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-dark">
            <div class="inner">
              <h3>{{ cancelledOrders }}</h3>
              <p>Cancelled Orders</p>
            </div>
            <div class="icon">
              <i class="fas fa-ban"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-indigo">
            <div class="inner">
              <h3>{{ totalCustomers }}</h3>
              <p>Customers Used Credit</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Methods Breakdown -->
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Sales by Payment Method</h3>
            </div>
            <div class="card-body">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Payment Method</th>
                    <th>Count</th>
                    <th>Amount</th>
                    <th>%</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="method in paymentMethods" :key="method.method">
                    <td>
                      <strong>{{ method.label }}</strong>
                    </td>
                    <td>{{ method.count }}</td>
                    <td>{{ method.total }}</td>
                    <td>
                      <div class="progress progress-sm">
                        <div
                          class="progress-bar"
                          :style="{ width: method.percentage + '%' }"
                        ></div>
                      </div>
                      {{ method.percentage.toFixed(1) }}%
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Top Products -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Top 5 Products Sold</h3>
            </div>
            <div class="card-body">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Revenue</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(product, index) in topProducts" :key="index">
                    <td>{{ product.name }}</td>
                    <td>{{ product.quantity }}</td>
                    <td>{{ product.revenue }}</td>
                  </tr>
                  <tr v-if="topProducts.length === 0">
                    <td colspan="3" class="text-center text-muted">
                      No sales data available
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Daily Sales Breakdown -->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Recent Orders Summary</h3>
            </div>
            <div class="card-body">
              <table class="table table-striped table-hover table-sm">
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="order in recentOrders"
                    :key="order.id"
                    :class="{ 'table-danger': order.status === 'cancelled' }"
                  >
                    <td>
                      <strong>{{ order.order_number }}</strong>
                    </td>
                    <td>{{ order.customer?.name || "Walk-in" }}</td>
                    <td>{{ getOrderItemsCount(order) }}</td>
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
                  </tr>
                  <tr v-if="recentOrders.length === 0">
                    <td colspan="7" class="text-center text-muted">
                      No orders in this period
                    </td>
                  </tr>
                </tbody>
              </table>
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
  name: "Dashboard",
  data() {
    return {
      orders: [],
      filterFromDate: "",
      filterToDate: "",
      topProducts: [],
    };
  },
  computed: {
    filteredOrders() {
      return this.orders.filter((order) => {
        const orderDate = new Date(order.created_at)
          .toISOString()
          .split("T")[0];
        const matchesFromDate =
          !this.filterFromDate || orderDate >= this.filterFromDate;
        const matchesToDate =
          !this.filterToDate || orderDate <= this.filterToDate;
        return matchesFromDate && matchesToDate;
      });
    },
    recentOrders() {
      return this.filteredOrders.slice(0, 10);
    },
    totalOrders() {
      return this.filteredOrders.length;
    },
    completedOrders() {
      return this.filteredOrders.filter((o) => o.status === "completed").length;
    },
    pendingOrders() {
      return this.filteredOrders.filter((o) => o.status === "pending").length;
    },
    cancelledOrders() {
      return this.filteredOrders.filter((o) => o.status === "cancelled").length;
    },
    totalRevenue() {
      return this.filteredOrders
        .filter((o) => o.status === "completed")
        .reduce((sum, o) => sum + parseFloat(o.total || 0), 0);
    },
    totalTax() {
      return this.filteredOrders.reduce(
        (sum, o) => sum + parseFloat(o.tax || 0),
        0,
      );
    },
    averageOrderValue() {
      if (this.completedOrders === 0) return 0;
      return this.totalRevenue / this.completedOrders;
    },
    totalCustomers() {
      return new Set(
        this.filteredOrders
          .filter((o) => o.customer_id)
          .map((o) => o.customer_id),
      ).size;
    },
    paymentMethods() {
      const methods = {};
      this.filteredOrders.forEach((order) => {
        if (!methods[order.payment_method]) {
          methods[order.payment_method] = {
            method: order.payment_method,
            count: 0,
            total: 0,
          };
        }
        methods[order.payment_method].count++;
        methods[order.payment_method].total += parseFloat(order.total || 0);
      });

      const methodLabels = {
        cash: "Cash",
        card: "Card",
        mobile: "Mobile Payment",
        credit: "Store Credit",
      };

      return Object.values(methods).map((m) => ({
        ...m,
        label: methodLabels[m.method] || m.method,
        percentage:
          this.totalRevenue > 0 ? (m.total / this.totalRevenue) * 100 : 0,
      }));
    },
  },
  methods: {
    async fetchOrders() {
      try {
        const response = await axios.get("/admin/pos/orders");
        this.orders = response.data.data || response.data;
        this.computeTopProducts();
      } catch (error) {
        console.error("Error fetching orders:", error);
      }
    },
    computeTopProducts() {
      const products = {};
      this.filteredOrders.forEach((order) => {
        if (order.items) {
          order.items.forEach((item) => {
            if (!products[item.product_name]) {
              products[item.product_name] = {
                name: item.product_name,
                quantity: 0,
                revenue: 0,
              };
            }
            products[item.product_name].quantity += item.quantity;
            products[item.product_name].revenue += parseFloat(
              item.subtotal || 0,
            );
          });
        }
      });

      this.topProducts = Object.values(products)
        .sort((a, b) => b.quantity - a.quantity)
        .slice(0, 5);
    },
    applyDateFilter() {
      this.computeTopProducts();
    },
    resetDateFilter() {
      this.filterFromDate = "";
      this.filterToDate = "";
      this.computeTopProducts();
    },
    generateReport() {
      alert("Report generation coming soon!");
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
.small-box-footer {
  display: block;
  padding: 10px;
  background: rgba(0, 0, 0, 0.1);
  text-decoration: none;
  color: #000;
}

.small-box-footer:hover {
  background: rgba(0, 0, 0, 0.15);
  text-decoration: none;
}

.table-danger {
  background-color: #f8d7da !important;
}

.progress-sm {
  height: 10px;
}

.bg-indigo {
  background-color: #6c5ce7 !important;
  color: white;
}
</style>
