<template>
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Product Search Section -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Product Search</h3>
                    </div>
                    <div class="card-body">
                        <!-- Search Input -->
                        <div class="form-group">
                            <label for="searchInput">Search Product by Name or Barcode</label>
                            <input 
                                v-model="searchQuery" 
                                @keyup="searchProducts" 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="searchInput"
                                placeholder="Enter product name or barcode..."
                                @keyup.enter="addFirstResult">
                        </div>

                        <!-- Search Results -->
                        <div v-if="searchResults.length > 0" class="list-group mb-3" style="max-height: 300px; overflow-y: auto;">
                            <button 
                                v-for="product in searchResults" 
                                :key="product.id"
                                @click="selectProduct(product)"
                                type="button"
                                class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ product.name }}</h6>
                                    <span class="badge badge-primary">{{ formatCurrency(product.price) }}</span>
                                </div>
                                <p class="mb-1"><small>Barcode: {{ product.barcode || 'N/A' }} | SKU: {{ product.sku }}</small></p>
                                <p class="mb-0"><small class="text-success">Stock: {{ product.stock }} units</small></p>
                            </button>
                        </div>

                        <div v-if="searchQuery && searchResults.length === 0 && !isSearching" class="alert alert-info">
                            No products found
                        </div>

                        <div v-if="isSearching" class="alert alert-info">
                            <i class="fas fa-spinner fa-spin"></i> Searching...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-md-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Order Summary</h3>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <!-- Order Items -->
                        <div v-if="orderItems.length === 0" class="alert alert-warning">
                            No items in cart
                        </div>

                        <div v-for="(item, index) in orderItems" :key="index" class="card card-sm mb-2">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ item.name }}</h6>
                                        <small class="text-muted">Price: {{ formatCurrency(item.price) }}</small>
                                    </div>
                                    <button 
                                        @click="removeItem(index)" 
                                        type="button"
                                        class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <button 
                                        @click="decrementQuantity(index)" 
                                        type="button"
                                        class="btn btn-sm btn-secondary">-</button>
                                    <input 
                                        v-model.number="item.quantity" 
                                        @change="updateTotal"
                                        type="number" 
                                        class="form-control form-control-sm mx-2" 
                                        style="width: 60px; text-align: center;"
                                        min="1">
                                    <button 
                                        @click="incrementQuantity(index)" 
                                        type="button"
                                        class="btn btn-sm btn-secondary">+</button>
                                    <span class="ml-2">= {{ formatCurrency(item.quantity * item.price) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div v-if="orderItems.length > 0" class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>{{ formatCurrency(subtotal) }}</strong>
                            </div>
                            <div class="form-group">
                                <label for="discountInput">Discount</label>
                                <input 
                                    v-model.number="discount" 
                                    @change="updateTotal"
                                    type="number" 
                                    class="form-control"
                                    id="discountInput"
                                    min="0"
                                    step="0.01">
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total:</span>
                                <h5><strong>{{ formatCurrency(total) }}</strong></h5>
                            </div>

                            <!-- Payment Section -->
                            <div class="form-group mt-3">
                                <label for="paidAmount">Amount Paid (Cash)</label>
                                <input 
                                    v-model.number="paidAmount" 
                                    @change="updateTotal"
                                    type="number" 
                                    class="form-control form-control-lg"
                                    id="paidAmount"
                                    min="0"
                                    step="0.01"
                                    placeholder="0.00">
                            </div>

                            <div v-if="paidAmount >= total" class="alert alert-success">
                                <strong>Change: {{ formatCurrency(paidAmount - total) }}</strong>
                            </div>

                            <div v-else-if="paidAmount > 0" class="alert alert-danger">
                                <small>Remaining: {{ formatCurrency(total - paidAmount) }}</small>
                            </div>

                            <!-- Customer Selection -->
                            <div class="form-group mt-2">
                                <label for="customerSelect">Customer (Optional)</label>
                                <select v-model="selectedCustomer" class="form-control" id="customerSelect">
                                    <option :value="null">Walk-in Customer</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Checkout Button -->
                            <button 
                                @click="completeOrder" 
                                :disabled="orderItems.length === 0 || paidAmount < total || isProcessing"
                                type="button"
                                class="btn btn-success btn-block btn-lg mt-3">
                                <i v-if="!isProcessing" class="fas fa-credit-card mr-2"></i>
                                <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                                {{ isProcessing ? 'Processing...' : 'Complete Order' }}
                            </button>

                            <!-- Clear Cart Button -->
                            <button 
                                @click="clearCart" 
                                type="button"
                                class="btn btn-secondary btn-block mt-2">
                                Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Recent Orders</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
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
                                <tr v-for="order in recentOrders" :key="order.id">
                                    <td><strong>{{ order.order_number }}</strong></td>
                                    <td>{{ order.items ? order.items.length : 0 }}</td>
                                    <td>{{ formatCurrency(order.total) }}</td>
                                    <td>{{ formatCurrency(order.paid_amount) }}</td>
                                    <td><span class="badge badge-success">{{ formatCurrency(order.change_amount) }}</span></td>
                                    <td><small>{{ formatTime(order.created_at) }}</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Order Completed Successfully</h5>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <p><strong>Order #{{ lastOrder.order_number }}</strong></p>
                        <p>Total: <strong>{{ formatCurrency(lastOrder.total) }}</strong></p>
                        <p>Change: <strong class="text-success">{{ formatCurrency(lastOrder.change) }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showSuccessModal = false; clearCart(); resetForm()" type="button" class="btn btn-success">
                            New Transaction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Cashier',
    data() {
        return {
            searchQuery: '',
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
            lastOrder: {}
        }
    },
    mounted() {
        this.loadCustomers();
        this.loadRecentOrders();
    },
    methods: {
        searchProducts() {
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }

            this.isSearching = true;

            axios.get('/admin/cashier/search-products', {
                params: { query: this.searchQuery }
            })
            .then(response => {
                if (response.data.success) {
                    this.searchResults = response.data.data;
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                this.$toast.error('Error searching products');
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
            // Check if product already in cart
            const existingItem = this.orderItems.find(item => item.id === product.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.orderItems.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: 1,
                    product_id: product.id
                });
            }

            this.searchQuery = '';
            this.searchResults = [];
            this.updateTotal();
        },

        incrementQuantity(index) {
            this.orderItems[index].quantity++;
            this.updateTotal();
        },

        decrementQuantity(index) {
            if (this.orderItems[index].quantity > 1) {
                this.orderItems[index].quantity--;
            }
            this.updateTotal();
        },

        removeItem(index) {
            this.orderItems.splice(index, 1);
            this.updateTotal();
        },

        updateTotal() {
            this.subtotal = this.orderItems.reduce((sum, item) => {
                return sum + (item.quantity * item.price);
            }, 0);

            this.total = this.subtotal - this.discount;
        },

        clearCart() {
            this.orderItems = [];
            this.discount = 0;
            this.paidAmount = 0;
            this.selectedCustomer = null;
            this.updateTotal();
        },

        completeOrder() {
            if (this.orderItems.length === 0) {
                this.$toast.warning('Please add items to the order');
                return;
            }

            if (this.paidAmount < this.total) {
                this.$toast.error('Insufficient payment amount');
                return;
            }

            this.isProcessing = true;

            const payload = {
                items: this.orderItems.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    price: item.price
                })),
                paid_amount: this.paidAmount,
                discount: this.discount,
                customer_id: this.selectedCustomer
            };

            axios.post('/admin/cashier/complete-order', payload)
            .then(response => {
                if (response.data.success) {
                    this.lastOrder = response.data.data;
                    this.showSuccessModal = true;
                    this.loadRecentOrders();
                    this.$toast.success('Order completed successfully');
                }
            })
            .catch(error => {
                console.error('Order error:', error);
                const message = error.response?.data?.message || 'Error completing order';
                this.$toast.error(message);
            })
            .finally(() => {
                this.isProcessing = false;
            });
        },

        loadCustomers() {
            axios.get('/admin/pos/customers')
            .then(response => {
                if (response.data && response.data.data) {
                    this.customers = response.data.data;
                }
            })
            .catch(error => {
                console.error('Error loading customers:', error);
            });
        },

        loadRecentOrders() {
            axios.get('/admin/cashier/order-history')
            .then(response => {
                if (response.data.success) {
                    this.recentOrders = response.data.data;
                }
            })
            .catch(error => {
                console.error('Error loading orders:', error);
            });
        },

        resetForm() {
            this.searchQuery = '';
            this.searchResults = [];
            this.discount = 0;
            this.paidAmount = 0;
            this.selectedCustomer = null;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(value || 0);
        },

        formatTime(dateTime) {
            if (!dateTime) return '';
            const date = new Date(dateTime);
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }
}
</script>

<style scoped>
.card-sm {
    border: 1px solid #dee2e6;
}

.modal.show {
    z-index: 1000;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.badge {
    font-size: 0.85rem;
}
</style>
