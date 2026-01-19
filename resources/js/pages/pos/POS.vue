<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Point of Sale (POS)</h1>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Products Section -->
        <div class="col-md-8">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-box"></i> Products</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-12 mb-3">
                  <input
                    type="text"
                    v-model="searchProduct"
                    class="form-control"
                    placeholder="Search products..."
                  />
                </div>
              </div>
              <div class="row">
                <div
                  v-for="product in filteredProducts"
                  :key="product.id"
                  class="col-md-6 col-lg-4 mb-3"
                >
                  <div
                    class="card cursor-pointer product-card"
                    @click="addToCart(product)"
                    style="transition: transform 0.2s; cursor: pointer"
                    @mouseover="
                      $event.target.closest('.card').style.transform =
                        'translateY(-5px)'
                    "
                    @mouseleave="
                      $event.target.closest('.card').style.transform =
                        'translateY(0)'
                    "
                  >
                    <div class="card-body text-center">
                      <i class="fas fa-cube fa-3x text-info mb-2"></i>
                      <h6 class="card-title">
                        {{ product.name }}
                      </h6>
                      <p class="text-muted mb-2">
                        {{ product.sku }}
                      </p>
                      <p class="card-text font-weight-bold">
                        ZWL
                        {{ parseFloat(product.price).toFixed(2) }}
                      </p>
                      <small class="text-success"
                        >Stock: {{ product.stock }}</small
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart Section -->
        <div class="col-md-4">
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-shopping-basket"></i> Cart
              </h3>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto">
              <div v-if="cart.length === 0" class="text-center text-muted">
                <p>No items in cart</p>
              </div>
              <div v-else>
                <div
                  v-for="(item, index) in cart"
                  :key="index"
                  class="mb-3 pb-3 border-bottom"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <h6 class="mb-1">
                        {{ item.name }}
                      </h6>
                      <p class="text-muted mb-2 small">
                        ZWL
                        {{ parseFloat(item.price).toFixed(2) }}
                        x {{ item.quantity }}
                      </p>
                    </div>
                    <button
                      @click="removeFromCart(index)"
                      class="btn btn-sm btn-danger"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                  <div class="input-group input-group-sm">
                    <button
                      @click="decrementQuantity(index)"
                      class="btn btn-outline-secondary"
                    >
                      -
                    </button>
                    <input
                      type="number"
                      v-model.number="item.quantity"
                      class="form-control text-center"
                      min="1"
                    />
                    <button
                      @click="incrementQuantity(index)"
                      class="btn btn-outline-secondary"
                    >
                      +
                    </button>
                  </div>
                  <p class="mt-2 font-weight-bold">
                    Subtotal: ZWL
                    {{ (item.price * item.quantity).toFixed(2) }}
                  </p>
                </div>
              </div>
            </div>
            <div class="card-footer bg-light">
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                  <strong>Subtotal:</strong>
                  <span>ZWL {{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                  <strong>Total:</strong>
                  <span class="h5">ZWL {{ total.toFixed(2) }}</span>
                </div>
              </div>
              <button
                @click="checkout"
                :disabled="cart.length === 0"
                class="btn btn-success btn-block"
              >
                <i class="fas fa-money-check"></i> Checkout
              </button>
              <button
                @click="clearCart"
                :disabled="cart.length === 0"
                class="btn btn-secondary btn-block mt-2"
              >
                <i class="fas fa-redo"></i> Clear Cart
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
  name: "POS",
  data() {
    return {
      products: [],
      cart: [],
      searchProduct: "",
    };
  },
  computed: {
    filteredProducts() {
      if (!this.searchProduct) {
        return this.products;
      }
      return this.products.filter(
        (p) =>
          p.name.toLowerCase().includes(this.searchProduct.toLowerCase()) ||
          p.sku.toLowerCase().includes(this.searchProduct.toLowerCase()),
      );
    },
    subtotal() {
      return this.cart.reduce(
        (sum, item) => sum + item.price * item.quantity,
        0,
      );
    },
    total() {
      return this.subtotal;
    },
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get("/admin/products");
        this.products = (response.data.data || response.data).filter(
          (p) => p.is_active && p.stock > 0,
        );
      } catch (error) {
        console.error("Error fetching products:", error);
        alert("Failed to load products");
      }
    },
    addToCart(product) {
      const existingItem = this.cart.find((item) => item.id === product.id);
      if (existingItem) {
        if (existingItem.quantity < product.stock) {
          existingItem.quantity++;
        } else {
          alert("Not enough stock available");
        }
      } else {
        this.cart.push({
          ...product,
          quantity: 1,
        });
      }
    },
    removeFromCart(index) {
      this.cart.splice(index, 1);
    },
    incrementQuantity(index) {
      if (this.cart[index].quantity < this.cart[index].stock) {
        this.cart[index].quantity++;
      }
    },
    decrementQuantity(index) {
      if (this.cart[index].quantity > 1) {
        this.cart[index].quantity--;
      }
    },
    clearCart() {
      this.cart = [];
    },
    async checkout() {
      if (this.cart.length === 0) {
        alert("Cart is empty!");
        return;
      }

      const orderData = {
        items: this.cart.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
          unit_price: item.price,
          subtotal: item.price * item.quantity,
        })),
        subtotal: this.subtotal,
        tax: 0,
        discount: 0,
        total: this.total,
        payment_method: "cash",
        status: "completed",
      };

      try {
        const response = await axios.post("/api/orders", orderData);
        alert(
          `Sale completed! Order #${response.data.order_number || response.data.id} - Total: ZWL ${this.total.toFixed(2)}`,
        );
        this.cart = [];
      } catch (error) {
        console.error("Error processing order:", error);
        alert(
          "Error processing order: " +
            (error.response?.data?.message || error.message),
        );
      }
    },
  },
  mounted() {
    this.fetchProducts();
  },
};
</script>

<style scoped>
.product-card {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.product-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
