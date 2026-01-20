<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">POS System - Orders Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Orders</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <!-- Stats Cards -->
      <div class="row mb-3">
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>₱{{ totalSales.toFixed(2) }}</h3>
              <p>Total Sales</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
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
      </div>

      <!-- Filters Card -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
              <div class="card-tools">
                <button
                  type="button"
                  class="btn btn-tool"
                  data-card-widget="collapse"
                >
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Status</label>
                    <select v-model="filterStatus" class="form-control">
                      <option value="">All Statuses</option>
                      <option value="completed">Completed</option>
                      <option value="pending">Pending</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Payment Method</label>
                    <select v-model="filterPaymentMethod" class="form-control">
                      <option value="">All Methods</option>
                      <option value="cash">Cash</option>
                      <option value="card">Card</option>
                      <option value="mobile">Mobile Payment</option>
                      <option value="credit">Store Credit</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>From Date</label>
                    <input
                      v-model="filterFromDate"
                      type="date"
                      class="form-control"
                    />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>To Date</label>
                    <input
                      v-model="filterToDate"
                      type="date"
                      class="form-control"
                    />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <button @click="clearFilters" class="btn btn-default btn-sm">
                    <i class="fas fa-redo"></i> Clear Filters
                  </button>
                  <button
                    @click="generateExcelReport"
                    class="btn btn-success btn-sm ml-2"
                    :disabled="isExporting"
                  >
                    <i
                      class="fas"
                      :class="
                        isExporting ? 'fa-spinner fa-spin' : 'fa-file-excel'
                      "
                    ></i>
                    {{ isExporting ? "Exporting..." : "Export to Excel" }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Table Card -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-list"></i> All Orders</h3>
              <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 300px">
                  <input
                    v-model="searchQuery"
                    type="text"
                    class="form-control"
                    placeholder="Search by order number, customer..."
                  />
                  <div class="input-group-append">
                    <span class="input-group-text">
                      <i class="fas fa-search"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-striped table-hover table-head-fixed">
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Cashier</th>
                    <th>Items</th>
                    <th>Subtotal</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in filteredOrders" :key="order.id">
                    <td>
                      <strong>{{ order.order_number }}</strong>
                    </td>
                    <td>
                      <small>{{ formatDate(order.created_at) }}</small>
                    </td>
                    <td>{{ order.customer?.name || "Walk-in" }}</td>
                    <td>{{ order.user?.name || "—" }}</td>
                    <td>
                      <span class="badge badge-secondary">
                        {{ getOrderItemsCount(order) }}
                      </span>
                    </td>
                    <td>₱{{ order.subtotal }}</td>
                    <td>₱{{ order.tax }}</td>
                    <td>
                      <strong>₱{{ order.total }}</strong>
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
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button
                          @click="viewOrder(order)"
                          class="btn btn-info"
                          title="View Details"
                        >
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredOrders.length === 0">
                    <td colspan="11" class="text-center text-muted">
                      <i class="fas fa-inbox fa-2x mb-2"></i>
                      <p>No orders found</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer clearfix">
              <div class="float-left">
                Showing {{ filteredOrders.length }} of
                {{ orders.length }} orders
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Details Modal -->
      <div
        v-if="showDetailModal"
        class="modal fade show"
        style="display: block; background-color: rgba(0, 0, 0, 0.5)"
      >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h4 class="modal-title">
                <i class="fas fa-receipt"></i> Order Details
              </h4>
              <button
                @click="showDetailModal = false"
                type="button"
                class="close text-white"
              >
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="selectedOrder">
                <!-- Order Info -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <dl class="row">
                      <dt class="col-sm-5">Order Number:</dt>
                      <dd class="col-sm-7">
                        <strong>{{ selectedOrder.order_number }}</strong>
                      </dd>

                      <dt class="col-sm-5">Customer:</dt>
                      <dd class="col-sm-7">
                        {{ selectedOrder.customer?.name || "Walk-in" }}
                      </dd>

                      <dt class="col-sm-5">Cashier:</dt>
                      <dd class="col-sm-7">
                        {{ selectedOrder.user?.name || "—" }}
                      </dd>

                      <dt class="col-sm-5">Date:</dt>
                      <dd class="col-sm-7">
                        {{ formatDate(selectedOrder.created_at) }}
                      </dd>
                    </dl>
                  </div>
                  <div class="col-md-6">
                    <dl class="row">
                      <dt class="col-sm-5">Payment Method:</dt>
                      <dd class="col-sm-7">
                        <span
                          class="badge"
                          :class="getPaymentBadge(selectedOrder.payment_method)"
                        >
                          {{
                            formatPaymentMethod(selectedOrder.payment_method)
                          }}
                        </span>
                      </dd>

                      <dt class="col-sm-5">Status:</dt>
                      <dd class="col-sm-7">
                        <span
                          class="badge"
                          :class="getStatusBadge(selectedOrder.status)"
                        >
                          {{ selectedOrder.status }}
                        </span>
                      </dd>

                      <dt class="col-sm-5">Paid Amount:</dt>
                      <dd class="col-sm-7">₱{{ selectedOrder.paid_amount }}</dd>

                      <dt class="col-sm-5" v-if="selectedOrder.change_amount">
                        Change:
                      </dt>
                      <dd class="col-sm-7" v-if="selectedOrder.change_amount">
                        ₱{{ selectedOrder.change_amount }}
                      </dd>
                    </dl>
                  </div>
                </div>

                <!-- Order Items -->
                <div class="card card-outline card-primary">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-shopping-cart"></i> Order Items
                    </h3>
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-sm table-striped">
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
                          <td>₱{{ item.price }}</td>
                          <td>{{ item.quantity }}</td>
                          <td>₱{{ item.subtotal }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Order Summary -->
                <div class="row">
                  <div class="col-md-6 offset-md-6">
                    <div class="table-responsive">
                      <table class="table">
                        <tr>
                          <th>Subtotal:</th>
                          <td class="text-right">
                            ₱{{ selectedOrder.subtotal }}
                          </td>
                        </tr>
                        <tr>
                          <th>Tax:</th>
                          <td class="text-right">₱{{ selectedOrder.tax }}</td>
                        </tr>
                        <tr v-if="selectedOrder.discount">
                          <th>Discount:</th>
                          <td class="text-right text-danger">
                            -₱{{ selectedOrder.discount }}
                          </td>
                        </tr>
                        <tr class="bg-light">
                          <th>Total:</th>
                          <th class="text-right">₱{{ selectedOrder.total }}</th>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>

                <!-- Notes -->
                <div v-if="selectedOrder.notes" class="alert alert-info mt-3">
                  <h5><i class="icon fas fa-info"></i> Notes:</h5>
                  {{ selectedOrder.notes }}
                </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button
                @click="showDetailModal = false"
                type="button"
                class="btn btn-default"
              >
                <i class="fas fa-times"></i> Close
              </button>
              <button
                @click="printReceipt"
                type="button"
                class="btn btn-primary"
              >
                <i class="fas fa-print"></i> Print Receipt
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
      isExporting: false,
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
    pendingOrders() {
      return this.orders.filter((o) => o.status === "pending").length;
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
        this.showError("Failed to fetch orders", error.message);
      }
    },
    viewOrder(order) {
      this.selectedOrder = order;
      this.showDetailModal = true;
    },
    clearFilters() {
      this.filterStatus = "";
      this.filterPaymentMethod = "";
      this.filterFromDate = "";
      this.filterToDate = "";

      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: "info",
          title: "Filters Cleared",
          text: "All filters have been reset.",
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: "top-end",
        });
      }
    },
    async generateExcelReport() {
      if (this.isExporting) return;

      try {
        this.isExporting = true;

        // Show loading indicator if SweetAlert is available
        let loadingAlert;
        if (typeof Swal !== "undefined") {
          loadingAlert = Swal.fire({
            title: "Generating Excel Report",
            html: "Please wait while we prepare your report...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });
        }

        // Prepare data for Excel
        const currentDate = new Date().toISOString().split("T")[0];
        const currentTime = new Date().toLocaleTimeString();

        // Create workbook data
        const worksheetData = [];

        // Add title and metadata
        worksheetData.push(["POS System - Orders Report"]);
        worksheetData.push([`Generated: ${currentDate} ${currentTime}`]);
        worksheetData.push([]);

        // Add summary
        worksheetData.push(["Summary"]);
        worksheetData.push(["Total Orders:", this.orders.length]);
        worksheetData.push(["Total Sales:", `₱${this.totalSales.toFixed(2)}`]);
        worksheetData.push(["Completed Orders:", this.completedOrders]);
        worksheetData.push(["Cancelled Orders:", this.cancelledOrders]);
        worksheetData.push(["Filtered Orders:", this.filteredOrders.length]);
        worksheetData.push([]);

        // Add filter information if any filters are active
        const activeFilters = [];
        if (this.filterStatus)
          activeFilters.push(`Status: ${this.filterStatus}`);
        if (this.filterPaymentMethod)
          activeFilters.push(`Payment: ${this.filterPaymentMethod}`);
        if (this.filterFromDate)
          activeFilters.push(`From: ${this.filterFromDate}`);
        if (this.filterToDate) activeFilters.push(`To: ${this.filterToDate}`);
        if (this.searchQuery)
          activeFilters.push(`Search: "${this.searchQuery}"`);

        if (activeFilters.length > 0) {
          worksheetData.push(["Applied Filters:"]);
          activeFilters.forEach((filter) => {
            worksheetData.push([filter]);
          });
          worksheetData.push([]);
        }

        // Add table headers
        worksheetData.push([
          "Order Number",
          "Customer",
          "Cashier",
          "Items Count",
          "Subtotal",
          "Tax",
          "Discount",
          "Total",
          "Payment Method",
          "Paid Amount",
          "Change Amount",
          "Status",
          "Date",
          "Notes",
        ]);

        // Add order data
        this.filteredOrders.forEach((order) => {
          worksheetData.push([
            order.order_number,
            order.customer?.name || "Walk-in",
            order.user?.name || "—",
            this.getOrderItemsCount(order),
            parseFloat(order.subtotal || 0),
            parseFloat(order.tax || 0),
            parseFloat(order.discount || 0),
            parseFloat(order.total || 0),
            this.formatPaymentMethod(order.payment_method),
            parseFloat(order.paid_amount || order.total),
            parseFloat(order.change_amount || 0),
            order.status.charAt(0).toUpperCase() + order.status.slice(1),
            this.formatDate(order.created_at),
            order.notes || "",
          ]);
        });

        // Convert to CSV format (Excel-compatible)
        const csvContent = worksheetData
          .map((row) =>
            row
              .map((cell) => {
                const cellValue =
                  cell === null || cell === undefined ? "" : String(cell);
                const escapedCell = cellValue.replace(/"/g, '""');
                return `"${escapedCell}"`;
              })
              .join(","),
          )
          .join("\n");

        // Add UTF-8 BOM for Excel compatibility
        const excelContent = "\uFEFF" + csvContent;

        // Create blob and download
        const blob = new Blob([excelContent], {
          type: "application/vnd.ms-excel;charset=utf-8",
        });

        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        const fileName = `Orders_Report_${currentDate}_${currentTime.replace(/:/g, "-")}.csv`;

        link.setAttribute("href", url);
        link.setAttribute("download", fileName);
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        // Close loading and show success
        if (loadingAlert) {
          loadingAlert.close();
        }

        if (typeof Swal !== "undefined") {
          Swal.fire({
            icon: "success",
            title: "Excel Report Generated!",
            html: `
              <p>Your Excel report has been downloaded successfully!</p>
              <p class="text-muted mt-2"><small>File: ${fileName}</small></p>
            `,
            showConfirmButton: true,
            confirmButtonText: "OK",
            confirmButtonColor: "#28a745",
            timer: 4000,
            timerProgressBar: true,
          });
        } else {
          alert("Excel Report Generated! Your file has been downloaded.");
        }
      } catch (error) {
        console.error("Error generating Excel report:", error);

        if (typeof Swal !== "undefined") {
          Swal.fire({
            icon: "error",
            title: "Export Failed",
            text:
              error.message ||
              "An unexpected error occurred while generating the report.",
            confirmButtonColor: "#dc3545",
          });
        } else {
          alert(`Error: ${error.message || "Failed to generate Excel report"}`);
        }
      } finally {
        this.isExporting = false;
      }
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
          <td>₱${item.price || 0}</td>
          <td>${item.quantity || 0}</td>
          <td>₱${item.subtotal || item.price * item.quantity || 0}</td>
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
                font-family: 'Courier New', monospace;
                margin: 20px;
                font-size: 12px;
              }
              .receipt {
                max-width: 400px;
                margin: 0 auto;
                border: 2px solid #000;
                padding: 20px;
              }
              .header {
                text-align: center;
                border-bottom: 2px dashed #000;
                padding-bottom: 10px;
                margin-bottom: 20px;
              }
              .header h1 {
                margin: 0;
                font-size: 24px;
              }
              .section {
                margin-bottom: 15px;
              }
              .section p {
                margin: 5px 0;
              }
              table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
              }
              table th,
              table td {
                padding: 8px 5px;
                text-align: left;
                border-bottom: 1px dashed #000;
              }
              table th {
                font-weight: bold;
                background-color: #f0f0f0;
              }
              .total-section {
                margin-top: 20px;
                border-top: 2px solid #000;
                padding-top: 10px;
              }
              .total-row {
                display: flex;
                justify-content: space-between;
                margin: 5px 0;
              }
              .total-row.grand {
                font-size: 16px;
                font-weight: bold;
                border-top: 2px solid #000;
                padding-top: 10px;
                margin-top: 10px;
              }
              .footer {
                text-align: center;
                margin-top: 30px;
                border-top: 2px dashed #000;
                padding-top: 15px;
              }
              @media print {
                body {
                  margin: 0;
                  padding: 10px;
                }
                .no-print {
                  display: none;
                }
              }
            </style>
          </head>
          <body>
            <div class="receipt">
              <div class="header">
                <h1>POS SYSTEM</h1>
                <p>Order Receipt</p>
              </div>

              <div class="section">
                <p><strong>Order #:</strong> ${this.selectedOrder.order_number}</p>
                <p><strong>Date:</strong> ${new Date(this.selectedOrder.created_at).toLocaleString()}</p>
                <p><strong>Customer:</strong> ${this.selectedOrder.customer?.name || "Walk-in"}</p>
                <p><strong>Cashier:</strong> ${this.selectedOrder.user?.name || "—"}</p>
              </div>

              <table>
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  ${itemsHtml}
                </tbody>
              </table>

              <div class="total-section">
                <div class="total-row">
                  <span>Subtotal:</span>
                  <span>₱${this.selectedOrder.subtotal}</span>
                </div>
                <div class="total-row">
                  <span>Tax:</span>
                  <span>₱${this.selectedOrder.tax}</span>
                </div>
                ${
                  this.selectedOrder.discount
                    ? `
                <div class="total-row">
                  <span>Discount:</span>
                  <span>-₱${this.selectedOrder.discount}</span>
                </div>
                `
                    : ""
                }
                <div class="total-row grand">
                  <span>TOTAL:</span>
                  <span>₱${this.selectedOrder.total}</span>
                </div>
                <div class="total-row">
                  <span>Paid:</span>
                  <span>₱${this.selectedOrder.paid_amount || this.selectedOrder.total}</span>
                </div>
                <div class="total-row">
                  <span>Change:</span>
                  <span>₱${this.selectedOrder.change_amount || "0.00"}</span>
                </div>
                <div class="total-row">
                  <span>Payment:</span>
                  <span>${this.formatPaymentMethod(this.selectedOrder.payment_method)}</span>
                </div>
              </div>

              <div class="footer">
                <p><strong>Thank you for your purchase!</strong></p>
                <p>Please come again</p>
              </div>
            </div>

            <div class="no-print" style="text-align: center; margin-top: 20px">
              <button
                onclick="window.print()"
                style="
                  padding: 10px 20px;
                  font-size: 16px;
                  background-color: #007bff;
                  color: white;
                  border: none;
                  border-radius: 4px;
                  cursor: pointer;
                "
              >
                Print Receipt
              </button>
              <button
                onclick="window.close()"
                style="
                  padding: 10px 20px;
                  font-size: 16px;
                  background-color: #6c757d;
                  color: white;
                  border: none;
                  border-radius: 4px;
                  cursor: pointer;
                  margin-left: 10px;
                "
              >
                Close
              </button>
            </div>
          </body>
        </html>
      `;

      printWindow.document.write(receiptHtml);
      printWindow.document.close();
      printWindow.focus();

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

    // SweetAlert helper methods
    showSuccess(title, text) {
      if (typeof Swal === "undefined") {
        alert(`${title}: ${text}`);
        return;
      }
      return Swal.fire({
        icon: "success",
        title: title,
        text: text,
        confirmButtonColor: "#28a745",
        timer: 3000,
        timerProgressBar: true,
      });
    },

    showError(title, text) {
      if (typeof Swal === "undefined") {
        alert(`Error - ${title}: ${text}`);
        return;
      }
      return Swal.fire({
        icon: "error",
        title: title,
        text: text,
        confirmButtonColor: "#dc3545",
      });
    },
  },
  mounted() {
    this.fetchOrders();
  },
};
</script>

<style scoped>
.btn-group-sm > .btn,
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  line-height: 1.5;
}

.table-danger {
  background-color: #f8d7da !important;
}

.modal.show {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1050;
  overflow-y: auto;
}

.modal-dialog {
  margin-top: 3rem;
  margin-bottom: 3rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.fa-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.table-head-fixed thead th {
  position: sticky;
  top: 0;
  z-index: 10;
  background-color: #fff;
}

.small-box {
  border-radius: 0.25rem;
  box-shadow:
    0 0 1px rgba(0, 0, 0, 0.125),
    0 1px 3px rgba(0, 0, 0, 0.2);
  display: block;
  margin-bottom: 20px;
  position: relative;
}

.small-box > .inner {
  padding: 10px;
}

.small-box > .small-box-footer {
  background-color: rgba(0, 0, 0, 0.1);
  color: rgba(255, 255, 255, 0.8);
  display: block;
  padding: 3px 0;
  position: relative;
  text-align: center;
  text-decoration: none;
  z-index: 10;
}

.small-box h3 {
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 10px;
  padding: 0;
  white-space: nowrap;
}

.small-box p {
  font-size: 1rem;
}

.small-box .icon {
  color: rgba(0, 0, 0, 0.15);
  z-index: 0;
}

.small-box .icon > i {
  font-size: 70px;
  position: absolute;
  right: 15px;
  top: 15px;
  transition: all 0.3s linear;
}

.bg-info {
  background-color: #17a2b8 !important;
  color: #fff !important;
}

.bg-success {
  background-color: #28a745 !important;
  color: #fff !important;
}

.bg-warning {
  background-color: #ffc107 !important;
  color: #1f2d3d !important;
}

.bg-danger {
  background-color: #dc3545 !important;
  color: #fff !important;
}

.bg-secondary {
  background-color: #6c757d !important;
  color: #fff !important;
}

@media print {
  .modal-header,
  .modal-footer,
  .card-tools,
  button,
  .breadcrumb,
  .content-header {
    display: none !important;
  }
}
</style>
