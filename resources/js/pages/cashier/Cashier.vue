<template>
  <div class="container-fluid mt-4">
    <div class="row">
      <!-- Left Column: Product Search -->
      <div class="col-md-7">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-search mr-2"></i>Product Search
            </h3>
          </div>
          <div class="card-body">
            <!-- Search Input -->
            <div class="form-group">
              <label for="searchInput">Search Product by Name or Barcode</label>
              <input
                v-model="searchQuery"
                @input="searchProducts"
                type="text"
                class="form-control form-control-lg"
                id="searchInput"
                name="searchInput"
                placeholder="Enter product name or barcode..."
                @keyup.enter="addFirstResult"
                autocomplete="off"
                autofocus
              />
            </div>

            <!-- Search Results -->
            <div
              v-if="searchResults.length > 0"
              class="list-group"
              style="max-height: 400px; overflow-y: auto"
            >
              <button
                v-for="product in searchResults"
                :key="product.id"
                @click="selectProduct(product)"
                type="button"
                class="list-group-item list-group-item-action"
              >
                <div
                  class="d-flex w-100 justify-content-between align-items-center"
                >
                  <div>
                    <h6 class="mb-1">{{ product.name }}</h6>
                    <p class="mb-1">
                      <small class="text-muted">
                        Barcode: {{ product.barcode || "N/A" }} | SKU:
                        {{ product.sku }}
                      </small>
                    </p>
                    <p class="mb-0">
                      <small
                        :class="
                          product.stock > 0 ? 'text-success' : 'text-danger'
                        "
                      >
                        Stock: {{ product.stock }}
                      </small>
                    </p>
                  </div>
                  <span class="badge badge-primary badge-lg">
                    {{ formatCurrency(product.price) }}
                  </span>
                </div>
              </button>
            </div>

            <!-- No Results Message -->
            <div
              v-else-if="searchQuery && !isSearching"
              class="alert alert-info"
            >
              <i class="fas fa-info-circle mr-2"></i>No products found matching
              "{{ searchQuery }}"
            </div>

            <!-- Loading Indicator -->
            <div v-if="isSearching" class="text-center py-3">
              <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
              <p class="mt-2">Searching...</p>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="card card-info mt-4">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-history mr-2"></i>Recent Orders
            </h3>
          </div>
          <div class="card-body p-0">
            <div
              class="table-responsive"
              style="max-height: 300px; overflow-y: auto"
            >
              <table class="table table-striped table-hover mb-0">
                <thead class="bg-light">
                  <tr>
                    <th>Order #</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Change</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="recentOrders.length === 0">
                    <td colspan="6" class="text-center text-muted py-3">
                      No recent orders
                    </td>
                  </tr>
                  <tr v-for="order in recentOrders" :key="order.id">
                    <td>
                      <strong>{{ order.order_number }}</strong>
                    </td>
                    <td>{{ getOrderItemsCount(order) }}</td>
                    <td>{{ formatCurrency(order.total) }}</td>
                    <td>{{ formatCurrency(order.paid_amount) }}</td>
                    <td>
                      <span class="text-success font-weight-bold">
                        {{
                          formatCurrency(
                            order.change_amount || order.change || 0,
                          )
                        }}
                      </span>
                    </td>
                    <td>{{ formatTime(order.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Shopping Cart -->
      <div class="col-md-5">
        <div
          class="card card-success"
          :class="{ 'border-warning': orderItems.length === 0 }"
        >
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-shopping-cart mr-2"></i>Shopping Cart
              <span v-if="orderItems.length > 0" class="badge badge-light ml-2">
                {{ orderItems.length }} item(s)
              </span>
            </h3>
          </div>
          <div class="card-body">
            <!-- Empty Cart Message -->
            <div v-if="orderItems.length === 0" class="text-center py-5">
              <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
              <p class="text-muted">Your cart is empty</p>
              <small class="text-muted"
                >Search and add products to get started</small
              >
            </div>

            <!-- Cart Items -->
            <div
              v-else
              class="cart-items"
              style="max-height: 350px; overflow-y: auto"
            >
              <div
                v-for="(item, index) in orderItems"
                :key="index"
                class="card mb-2 shadow-sm"
              >
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between mb-2">
                    <div class="flex-grow-1">
                      <h6 class="mb-1">{{ item.name }}</h6>
                      <small class="text-muted">
                        {{ formatCurrency(item.price) }} each
                      </small>
                    </div>
                    <button
                      @click="removeItem(index)"
                      type="button"
                      class="btn btn-sm btn-danger ml-2"
                      title="Remove item"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                  <div
                    class="d-flex align-items-center justify-content-between"
                  >
                    <div class="btn-group" role="group">
                      <button
                        @click="decrementQuantity(index)"
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                      >
                        <i class="fas fa-minus"></i>
                      </button>
                      <input
                        v-model.number="item.quantity"
                        @change="updateTotal"
                        type="number"
                        class="form-control form-control-sm text-center"
                        :id="'quantity-' + index"
                        :name="'quantity-' + index"
                        style="width: 60px"
                        min="1"
                        :max="item.stock"
                        autocomplete="off"
                      />
                      <button
                        @click="incrementQuantity(index)"
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                      >
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                    <strong class="text-primary">
                      {{ formatCurrency(item.quantity * item.price) }}
                    </strong>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cart Summary -->
            <div v-if="orderItems.length > 0" class="mt-3 pt-3 border-top">
              <!-- Subtotal -->
              <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <strong>{{ formatCurrency(subtotal) }}</strong>
              </div>

              <!-- Discount Input -->
              <div class="form-group mb-2">
                <label for="discountInput">
                  <small>Discount</small>
                </label>
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input
                    v-model.number="discount"
                    @input="updateTotal"
                    type="number"
                    class="form-control"
                    id="discountInput"
                    name="discountInput"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    autocomplete="off"
                  />
                </div>
              </div>

              <!-- Total -->
              <div
                class="d-flex justify-content-between mb-3 pb-2 border-bottom"
              >
                <span class="h5 mb-0">Total:</span>
                <strong class="h5 mb-0 text-primary">
                  {{ formatCurrency(total) }}
                </strong>
              </div>

              <!-- Customer Selection -->
              <div class="form-group">
                <label for="customerSelect">
                  <small>Customer (Optional)</small>
                </label>
                <select
                  v-model="selectedCustomer"
                  class="form-control form-control-sm"
                  id="customerSelect"
                  name="customerSelect"
                  autocomplete="off"
                >
                  <option :value="null">-- Walk-in Customer --</option>
                  <option
                    v-for="customer in customers"
                    :key="customer.id"
                    :value="customer.id"
                  >
                    {{ customer.name }}
                  </option>
                </select>
              </div>

              <!-- Payment Input -->
              <div class="form-group">
                <label for="paidInput">
                  <small>Amount Paid</small>
                </label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input
                    v-model.number="paidAmount"
                    @input="updateTotal"
                    type="number"
                    class="form-control form-control-lg"
                    id="paidInput"
                    name="paidInput"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    autocomplete="off"
                  />
                </div>
              </div>

              <!-- Change Display -->
              <div
                v-if="paidAmount > 0"
                class="alert"
                :class="paidAmount >= total ? 'alert-success' : 'alert-warning'"
              >
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Change:</strong>
                  <span class="h5 mb-0">
                    {{ formatCurrency(Math.max(0, paidAmount - total)) }}
                  </span>
                </div>
                <small v-if="paidAmount < total" class="d-block mt-1">
                  Insufficient payment
                </small>
              </div>

              <!-- Action Buttons -->
              <button
                @click="completeOrder"
                :disabled="
                  orderItems.length === 0 || paidAmount < total || isProcessing
                "
                type="button"
                class="btn btn-success btn-block btn-lg mb-2"
              >
                <i v-if="!isProcessing" class="fas fa-check-circle mr-2"></i>
                <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                {{ isProcessing ? "Processing..." : "Complete Order" }}
              </button>

              <button
                @click="clearCart"
                type="button"
                class="btn btn-outline-secondary btn-block"
                :disabled="isProcessing"
              >
                <i class="fas fa-times mr-2"></i>Clear Cart
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Button Row -->
    <div class="row mt-3">
      <div class="col-12">
        <button
          @click="exportReportsCSV"
          type="button"
          class="btn btn-info"
          :disabled="recentOrders.length === 0"
        >
          <i class="fas fa-download mr-2"></i>Export Orders (CSV)
        </button>
      </div>
    </div>

    <!-- Success Modal with Receipt - Placeholder -->
    <div v-if="false"></div>
  </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
  name: "Cashier",
  data() {
    return {
      searchQuery: "",
      searchResults: [],
      orderItems: [],
      subtotal: 0,
      total: 0,
      discount: 0,
      paidAmount: 0,
      selectedCustomer: null,
      customers: [],
      recentOrders: [],
      isSearching: false,
      isProcessing: false,
      showSuccessModal: false,
      lastOrder: {},
      receiptItems: [],
      receiptSubtotal: 0,
      receiptDiscount: 0,
      searchTimeout: null,
    };
  },
  mounted() {
    this.loadCustomers();
    this.loadRecentOrders();
    console.log("Cashier component mounted successfully");
  },
  methods: {
    searchProducts() {
      // Clear previous timeout
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }

      if (!this.searchQuery.trim()) {
        this.searchResults = [];
        return;
      }

      // Debounce search
      this.searchTimeout = setTimeout(() => {
        this.performSearch();
      }, 300);
    },

    performSearch() {
      this.isSearching = true;

      axios
        .get("/admin/cashier/search-products", {
          params: { query: this.searchQuery },
        })
        .then((response) => {
          if (response.data.success) {
            this.searchResults = response.data.data;
          } else {
            this.searchResults = [];
          }
        })
        .catch((error) => {
          console.error("Search error:", error);
          this.searchResults = [];
          Swal.fire({
            title: "Error",
            text: "Error searching products",
            icon: "error",
            confirmButtonColor: "#3085d6",
          });
        })
        .finally(() => {
          this.isSearching = false;
        });
    },

    addFirstResult() {
      if (this.searchResults.length > 0) {
        this.selectProduct(this.searchResults[0]);
      }
    },

    selectProduct(product) {
      // Check stock availability
      if (product.stock <= 0) {
        Swal.fire({
          title: "Out of Stock",
          text: `${product.name} is currently out of stock`,
          icon: "warning",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      // Check if product already in cart
      const existingItem = this.orderItems.find(
        (item) => item.product_id === product.id,
      );

      if (existingItem) {
        if (existingItem.quantity < product.stock) {
          existingItem.quantity += 1;
        } else {
          Swal.fire({
            title: "Stock Limit Reached",
            text: `Only ${product.stock} units available`,
            icon: "warning",
            confirmButtonColor: "#3085d6",
          });
          return;
        }
      } else {
        this.orderItems.push({
          product_id: product.id,
          name: product.name,
          price: parseFloat(product.price),
          quantity: 1,
          stock: product.stock,
        });
      }

      this.searchQuery = "";
      this.searchResults = [];
      this.updateTotal();

      // Success notification
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      });

      Toast.fire({
        icon: "success",
        title: `${product.name} added to cart`,
      });
    },

    incrementQuantity(index) {
      const item = this.orderItems[index];
      if (item.quantity < item.stock) {
        item.quantity += 1;
        this.updateTotal();
      } else {
        Swal.fire({
          title: "Stock Limit",
          text: `Only ${item.stock} units available`,
          icon: "warning",
          confirmButtonColor: "#3085d6",
        });
      }
    },

    decrementQuantity(index) {
      if (this.orderItems[index].quantity > 1) {
        this.orderItems[index].quantity -= 1;
        this.updateTotal();
      } else {
        this.removeItem(index);
      }
    },

    removeItem(index) {
      const item = this.orderItems[index];
      Swal.fire({
        title: "Remove Item?",
        text: `Remove ${item.name} from cart?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, remove it",
      }).then((result) => {
        if (result.isConfirmed) {
          this.orderItems.splice(index, 1);
          this.updateTotal();
        }
      });
    },

    updateTotal() {
      this.subtotal = this.orderItems.reduce((sum, item) => {
        return sum + item.quantity * item.price;
      }, 0);

      const discountAmount = parseFloat(this.discount) || 0;
      this.total = Math.max(0, this.subtotal - discountAmount);
    },

    clearCart() {
      if (this.orderItems.length === 0) {
        return;
      }

      Swal.fire({
        title: "Clear Cart?",
        text: "This will remove all items from the cart",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, clear it",
      }).then((result) => {
        if (result.isConfirmed) {
          this.orderItems = [];
          this.discount = 0;
          this.paidAmount = 0;
          this.selectedCustomer = null;
          this.updateTotal();
        }
      });
    },

    completeOrder() {
      if (this.orderItems.length === 0) {
        Swal.fire({
          title: "Empty Cart",
          text: "Please add items to the order",
          icon: "warning",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      if (this.paidAmount < this.total) {
        Swal.fire({
          title: "Insufficient Payment",
          text: `Payment required: ${this.formatCurrency(this.total)}`,
          icon: "error",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      this.isProcessing = true;

      const payload = {
        items: this.orderItems.map((item) => ({
          product_id: item.product_id,
          quantity: item.quantity,
          price: item.price,
        })),
        paid_amount: parseFloat(this.paidAmount),
        discount: parseFloat(this.discount) || 0,
        customer_id: this.selectedCustomer,
      };

      axios
        .post("/admin/cashier/complete-order", payload)
        .then((response) => {
          if (response.data.success) {
            // Store receipt data before clearing
            this.receiptItems = [...this.orderItems];
            this.receiptSubtotal = this.subtotal;
            this.receiptDiscount = this.discount;

            this.lastOrder = response.data.data;
            this.showSuccessModal = true;
            this.loadRecentOrders();

            Swal.fire({
              title: "Success!",
              text: `Order #${this.lastOrder.order_number} completed`,
              icon: "success",
              confirmButtonColor: "#28a745",
              timer: 2000,
            });
          }
        })
        .catch((error) => {
          console.error("Order error:", error);
          const message =
            error.response?.data?.message || "Error completing order";

          Swal.fire({
            title: "Error",
            text: message,
            icon: "error",
            confirmButtonColor: "#3085d6",
          });
        })
        .finally(() => {
          this.isProcessing = false;
        });
    },

    loadCustomers() {
      axios
        .get("/admin/pos/customers")
        .then((response) => {
          if (response.data && response.data.data) {
            this.customers = response.data.data;
          }
        })
        .catch((error) => {
          console.error("Error loading customers:", error);
        });
    },

    loadRecentOrders() {
      // Using the same endpoint as Dashboard for consistency
      axios
        .get("/admin/pos/orders")
        .then((response) => {
          if (response.data && response.data.data) {
            // Take only the most recent orders and format them properly
            this.recentOrders = response.data.data
              .slice(0, 10)
              .map((order) => ({
                id: order.id,
                order_number: order.order_number,
                items: order.items || [],
                total: parseFloat(order.total || 0),
                paid_amount: parseFloat(order.paid_amount || order.total || 0),
                change_amount: parseFloat(
                  order.change_amount || order.change || 0,
                ),
                created_at: order.created_at,
              }));
          } else if (Array.isArray(response.data)) {
            // Fallback if data is directly an array
            this.recentOrders = response.data.slice(0, 10).map((order) => ({
              id: order.id,
              order_number: order.order_number,
              items: order.items || [],
              total: parseFloat(order.total || 0),
              paid_amount: parseFloat(order.paid_amount || order.total || 0),
              change_amount: parseFloat(
                order.change_amount || order.change || 0,
              ),
              created_at: order.created_at,
            }));
          }
        })
        .catch((error) => {
          console.error("Error loading orders:", error);
          this.recentOrders = [];
        });
    },

    // Helper method to calculate items count from order items array
    getOrderItemsCount(order) {
      if (order.items && Array.isArray(order.items)) {
        // Sum up all item quantities
        return order.items.reduce(
          (total, item) => total + (item.quantity || 0),
          0,
        );
      }
      // Fallback to items_count if available
      return order.items_count || 0;
    },

    resetForm() {
      this.searchQuery = "";
      this.searchResults = [];
      this.discount = 0;
      this.paidAmount = 0;
      this.selectedCustomer = null;
      this.orderItems = [];
      this.updateTotal();
    },

    closeModal() {
      this.showSuccessModal = false;
    },

    closeModalAndReset() {
      this.showSuccessModal = false;
      this.resetForm();
    },

    formatCurrency(value) {
      return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
      }).format(value || 0);
    },

    formatTime(dateTime) {
      if (!dateTime) return "";
      const date = new Date(dateTime);
      return date.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      });
    },

    getCurrentDateTime() {
      const date = new Date();
      const formattedDate = date.toLocaleDateString("en-PH", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
      const formattedTime = date.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      });
      return `${formattedDate} ${formattedTime}`;
    },

    printReceipt() {
      if (!this.lastOrder) return;

      const printWindow = window.open("", "_blank");

      // Use receiptItems that was stored during completeOrder
      const items = this.receiptItems || [];
      const itemsHtml = items
        .map(
          (item) => `
        <tr>
          <td>${item.name}</td>
          <td>${item.sku || "—"}</td>
          <td>${item.price}</td>
          <td>${item.quantity}</td>
          <td>${(item.price * item.quantity).toFixed(2)}</td>
        </tr>
      `,
        )
        .join("");

      const receiptHtml = `
        <!DOCTYPE html>
        <html>
          <head>
            <title>Receipt - ${this.lastOrder.order_number}</title>
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
                <p><strong>Order Number:</strong> ${this.lastOrder.order_number}</p>
                <p><strong>Payment Method:</strong> Cash</p>
              </div>

              <div class="section">
                <p><strong>Customer:</strong> ${this.lastOrder.customer_name || "Walk-in"}</p>
                <p><strong>Status:</strong> completed</p>
              </div>

              <div class="section">
                <p><strong>Cashier:</strong> ${this.lastOrder.cashier_name || "—"}</p>
                <p><strong>Paid Amount:</strong> ${this.lastOrder.paid_amount || this.paidAmount}</p>
              </div>

              <div class="section">
                <p><strong>Date:</strong> ${this.getCurrentDateTime()}</p>
                <p><strong>Change:</strong> ${this.lastOrder.change_amount || Math.max(0, this.paidAmount - this.total)}</p>
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
                <p>\[ ${this.receiptSubtotal || this.subtotal} \]</p>
                
                <p><strong>Tax:</strong></p>
                <p>\[ 0.00 \]</p>
                
                ${
                  this.receiptDiscount
                    ? `
                  <p><strong>Discount:</strong></p>
                  <p>\[ -${this.receiptDiscount} \]</p>
                `
                    : ""
                }
                
                <p><strong>Total:</strong></p>
                <p>\[ ${this.total} \]</p>
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

    exportReportsCSV() {
      if (this.recentOrders.length === 0) {
        Swal.fire({
          title: "No Data",
          text: "No orders found to export",
          icon: "info",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      // Prepare CSV headers
      const headers = [
        "Order #",
        "Date",
        "Time",
        "Items",
        "Subtotal",
        "Discount",
        "Total",
        "Paid",
        "Change",
      ];

      const rows = this.recentOrders.map((order) => {
        const orderDate = new Date(order.created_at);
        const itemsCount = this.getOrderItemsCount(order);
        const subtotal = order.subtotal || order.total + (order.discount || 0);

        return [
          order.order_number,
          orderDate.toLocaleDateString("en-PH"),
          orderDate.toLocaleTimeString("en-US"),
          itemsCount,
          subtotal,
          order.discount || 0,
          order.total,
          order.paid_amount,
          order.change_amount || order.change || 0,
        ];
      });

      // Create CSV content
      const csvContent = [
        headers.join(","),
        ...rows.map((row) => row.join(",")),
      ].join("\n");

      // Create blob and download
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = `cashier-orders-${new Date().toISOString().split("T")[0]}.csv`;
      link.click();
      window.URL.revokeObjectURL(url);

      Swal.fire({
        title: "Success!",
        text: "Orders exported successfully",
        icon: "success",
        confirmButtonColor: "#3085d6",
        timer: 2000,
      });
    },
  },
};
</script>
