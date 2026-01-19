<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">POS System - Products Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button @click="showAddForm = true" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
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
              <h3>{{ products.length }}</h3>
              <p>Total Products</p>
            </div>
            <div class="icon">
              <i class="fas fa-box"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ lowStockCount }}</h3>
              <p>Low Stock Items</p>
            </div>
            <div class="icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ outOfStockCount }}</h3>
              <p>Out of Stock</p>
            </div>
            <div class="icon">
              <i class="fas fa-ban"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ totalValue.toFixed(2) }}</h3>
              <p>Total Value (₱)</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
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
                {{ editingId ? "Edit Product" : "Add New Product" }}
              </h3>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto">
              <div class="form-row">
                <div class="form-group col-md-3">
                  <label class="small">Product Name</label>
                  <input
                    v-model="formData.name"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="Product name"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">SKU</label>
                  <input
                    v-model="formData.sku"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="e.g., PROD001"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Category</label>
                  <select
                    v-model="formData.category_id"
                    class="form-control form-control-sm"
                    required
                  >
                    <option value="">Select Category</option>
                    <option
                      v-for="cat in categories"
                      :key="cat.id"
                      :value="cat.id"
                    >
                      {{ cat.name }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Barcode</label>
                  <input
                    v-model="formData.barcode"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="Barcode"
                  />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-3">
                  <label class="small">Cost Price (₱)</label>
                  <input
                    v-model.number="formData.cost"
                    type="number"
                    step="0.01"
                    class="form-control form-control-sm"
                    placeholder="Enter cost price"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Selling Price (₱)</label>
                  <input
                    v-model.number="formData.price"
                    type="number"
                    step="0.01"
                    class="form-control form-control-sm"
                    placeholder="Enter selling price"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Profit Margin</label>
                  <input
                    type="text"
                    class="form-control form-control-sm"
                    :value="calculateMargin(formData.cost, formData.price)"
                    readonly
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Active</label>
                  <div class="custom-control custom-checkbox mt-2">
                    <input
                      v-model="formData.is_active"
                      type="checkbox"
                      class="custom-control-input"
                      id="isActive"
                    />
                    <label class="custom-control-label small" for="isActive"
                      >Enable this product</label
                    >
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-3">
                  <label class="small">Current Stock</label>
                  <input
                    v-model.number="formData.stock"
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Enter stock quantity"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Minimum Stock Level</label>
                  <input
                    v-model.number="formData.min_stock"
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Enter minimum stock"
                    required
                  />
                </div>
                <div class="form-group col-md-6">
                  <label class="small">Description</label>
                  <textarea
                    v-model="formData.description"
                    class="form-control form-control-sm"
                    rows="1"
                    placeholder="Product description..."
                  ></textarea>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button @click="saveProduct" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> Save
              </button>
              <button @click="cancelForm" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">All Products</h3>
              <div class="card-tools">
                <input
                  v-model="searchQuery"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Search by name, SKU, or barcode..."
                  style="width: 300px"
                />
              </div>
            </div>
            <div class="card-body">
              <table class="table table-striped table-hover table-sm">
                <thead>
                  <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Cost</th>
                    <th>Price</th>
                    <th>Margin</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="product in filteredProducts"
                    :key="product.id"
                    :class="{
                      'table-danger': product.stock === 0,
                      'table-warning':
                        product.stock > 0 && product.stock <= product.min_stock,
                    }"
                  >
                    <td>
                      <strong>{{ product.sku }}</strong>
                    </td>
                    <td>{{ product.name }}</td>
                    <td>{{ product.category?.name || "—" }}</td>
                    <td>{{ product.cost }}</td>
                    <td>{{ product.price }}</td>
                    <td>
                      {{ calculateMargin(product.cost, product.price) }}
                    </td>
                    <td>
                      <span
                        v-if="product.stock === 0"
                        class="badge badge-danger"
                      >
                        Out of Stock
                      </span>
                      <span
                        v-else-if="product.stock <= product.min_stock"
                        class="badge badge-warning"
                      >
                        Low ({{ product.stock }})
                      </span>
                      <span v-else class="badge badge-success">
                        {{ product.stock }}
                      </span>
                    </td>
                    <td>
                      <span
                        v-if="product.is_active"
                        class="badge badge-success"
                      >
                        Active
                      </span>
                      <span v-else class="badge badge-danger"> Inactive </span>
                    </td>
                    <td>
                      <button
                        @click="editProduct(product)"
                        class="btn btn-xs btn-info"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        @click="deleteProduct(product.id)"
                        class="btn btn-xs btn-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredProducts.length === 0">
                    <td colspan="9" class="text-center text-muted">
                      No products found
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
  name: "Products",
  data() {
    return {
      products: [],
      categories: [],
      formData: {
        name: "",
        sku: "",
        description: "",
        category_id: "",
        price: null,
        cost: null,
        stock: null,
        min_stock: null,
        barcode: "",
        is_active: true,
      },
      editingId: null,
      showAddForm: false,
      searchQuery: "",
    };
  },
  computed: {
    lowStockCount() {
      return this.products.filter((p) => p.stock > 0 && p.stock <= p.min_stock)
        .length;
    },
    outOfStockCount() {
      return this.products.filter((p) => p.stock === 0).length;
    },
    totalValue() {
      return this.products.reduce((sum, p) => sum + p.cost * p.stock, 0);
    },
    filteredProducts() {
      if (!this.searchQuery) return this.products;
      const q = this.searchQuery.toLowerCase();
      return this.products.filter(
        (p) =>
          p.name.toLowerCase().includes(q) ||
          p.sku.toLowerCase().includes(q) ||
          (p.barcode && p.barcode.toLowerCase().includes(q)),
      );
    },
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get("/admin/pos/products");
        this.products = response.data.data || response.data;
      } catch (error) {
        console.error("Error fetching products:", error);
      }
    },
    async fetchCategories() {
      try {
        const response = await axios.get("/admin/pos/categories/list/all");
        this.categories = response.data;
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    },
    calculateMargin(cost, price) {
      if (!cost || cost === 0) return "0%";
      const margin = ((price - cost) / cost) * 100;
      return margin.toFixed(2) + "%";
    },
    async saveProduct() {
      try {
        if (this.editingId) {
          await axios.put(
            `/admin/pos/products/${this.editingId}`,
            this.formData,
          );
        } else {
          await axios.post("/admin/pos/products", this.formData);
        }
        this.fetchProducts();
        this.cancelForm();
      } catch (error) {
        console.error("Error saving product:", error);
        alert(
          "Error saving product: " + error.response?.data?.message ||
            error.message,
        );
      }
    },
    editProduct(product) {
      this.editingId = product.id;
      this.formData = { ...product };
      this.showAddForm = true;
    },
    async deleteProduct(id) {
      if (confirm("Are you sure you want to delete this product?")) {
        try {
          await axios.delete(`/admin/pos/products/${id}`);
          this.fetchProducts();
        } catch (error) {
          console.error("Error deleting product:", error);
          alert(
            "Error deleting product: " + error.response?.data?.message ||
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
        sku: "",
        description: "",
        category_id: "",
        price: null,
        cost: null,
        stock: null,
        min_stock: null,
        barcode: "",
        is_active: true,
      };
    },
  },
  mounted() {
    this.fetchProducts();
    this.fetchCategories();
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
</style>
