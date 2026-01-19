<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Inventory Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button @click="toggleForm" class="btn btn-primary">
            <i :class="showAddForm ? 'fas fa-minus' : 'fas fa-plus'"></i>
            {{ showAddForm ? "Hide Form" : "Add Item" }}
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
              <h3>{{ totalItems }}</h3>
              <p>Total Items</p>
            </div>
            <div class="icon">
              <i class="fas fa-boxes"></i>
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
        <div class="col-md-3">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ lowStockItems }}</h3>
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
              <h3>{{ outOfStockItems }}</h3>
              <p>Out of Stock</p>
            </div>
            <div class="icon">
              <i class="fas fa-ban"></i>
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
                {{ editingId ? "Edit Item" : "Add New Item" }}
              </h3>
            </div>
            <div class="card-body">
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
                    placeholder="SKU"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Category</label>
                  <select
                    v-model="formData.category_id"
                    class="form-control form-control-sm"
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
                    placeholder="Barcode (optional)"
                  />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-3">
                  <label class="small">Cost Price (₱)</label>
                  <input
                    v-model.number="formData.cost"
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Enter cost price"
                    step="0.01"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Selling Price (₱)</label>
                  <input
                    v-model.number="formData.price"
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Enter selling price"
                    step="0.01"
                    required
                  />
                </div>
                <div class="form-group col-md-3">
                  <label class="small">Quantity in Stock</label>
                  <input
                    v-model.number="formData.stock"
                    type="number"
                    class="form-control form-control-sm"
                    placeholder="Enter quantity"
                    min="0"
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
                    min="0"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="small">Description</label>
                <textarea
                  v-model="formData.description"
                  class="form-control form-control-sm"
                  placeholder="Product description"
                  rows="2"
                ></textarea>
              </div>
            </div>
            <div class="card-footer">
              <button @click="saveItem" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> Save
              </button>
              <button @click="cancelEdit" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="row mb-3">
        <div class="col-md-12">
          <input
            v-model="searchQuery"
            type="text"
            class="form-control"
            placeholder="Search inventory items..."
          />
        </div>
      </div>

      <!-- Inventory Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Inventory Items</h3>
            </div>
            <div class="card-body">
              <table class="table table-hover table-striped">
                <thead>
                  <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Stock Value</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in filteredItems" :key="item.id">
                    <td>{{ item.sku }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.category?.name || "—" }}</td>
                    <td>
                      <span :class="getStockClass(item)">
                        {{ item.stock }}
                      </span>
                    </td>
                    <td>₱ {{ parseFloat(item.cost).toFixed(2) }}</td>
                    <td>₱ {{ parseFloat(item.price).toFixed(2) }}</td>
                    <td>
                      ₱ {{ (item.stock * parseFloat(item.cost)).toFixed(2) }}
                    </td>
                    <td>
                      <span v-if="item.stock === 0" class="badge badge-danger"
                        >Out of Stock</span
                      >
                      <span
                        v-else-if="item.stock <= item.min_stock"
                        class="badge badge-warning"
                        >Low Stock</span
                      >
                      <span v-else class="badge badge-success">In Stock</span>
                    </td>
                    <td>
                      <button
                        @click="editItem(item)"
                        class="btn btn-sm btn-warning"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        @click="deleteItem(item.id)"
                        class="btn btn-sm btn-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
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
  name: "Inventory",
  data() {
    return {
      items: [],
      categories: [],
      showAddForm: false,
      editingId: null,
      searchQuery: "",
      formData: {
        name: "",
        sku: "",
        category_id: "",
        stock: null,
        cost: null,
        price: null,
        min_stock: null,
        description: "",
        barcode: "",
      },
    };
  },
  computed: {
    filteredItems() {
      if (!this.searchQuery) {
        return this.items;
      }
      const q = this.searchQuery.toLowerCase();
      return this.items.filter(
        (item) =>
          item.name.toLowerCase().includes(q) ||
          item.sku.toLowerCase().includes(q) ||
          (item.category?.name || "").toLowerCase().includes(q),
      );
    },
    totalItems() {
      return this.items.reduce((sum, item) => sum + item.stock, 0);
    },
    totalValue() {
      return this.items.reduce((sum, item) => sum + item.stock * item.cost, 0);
    },
    lowStockItems() {
      return this.items.filter(
        (item) => item.stock > 0 && item.stock <= item.min_stock,
      ).length;
    },
    outOfStockItems() {
      return this.items.filter((item) => item.stock === 0).length;
    },
  },
  methods: {
    async fetchItems() {
      try {
        const response = await axios.get("/admin/pos/products");
        this.items = response.data.data || response.data;
      } catch (error) {
        console.error("Error fetching inventory:", error);
        alert("Failed to load inventory");
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
    getStockClass(item) {
      if (item.stock === 0) {
        return "badge badge-danger";
      } else if (item.stock <= item.min_stock) {
        return "badge badge-warning";
      }
      return "badge badge-success";
    },
    toggleForm() {
      this.showAddForm = !this.showAddForm;
      if (!this.showAddForm) {
        this.cancelEdit();
      }
    },
    editItem(item) {
      this.editingId = item.id;
      this.formData = {
        name: item.name,
        sku: item.sku,
        category_id: item.category_id,
        stock: item.stock,
        cost: item.cost,
        price: item.price,
        min_stock: item.min_stock,
        description: item.description,
        barcode: item.barcode,
      };
      this.showAddForm = true;
    },
    async saveItem() {
      if (!this.formData.name || !this.formData.sku) {
        alert("Please fill in all required fields");
        return;
      }
      try {
        if (this.editingId) {
          await axios.put(
            `/admin/pos/products/${this.editingId}`,
            this.formData,
          );
        } else {
          await axios.post("/admin/pos/products", this.formData);
        }
        this.fetchItems();
        this.cancelEdit();
      } catch (error) {
        console.error("Error saving item:", error);
        alert(
          "Error saving item: " +
            (error.response?.data?.message || error.message),
        );
      }
    },
    async deleteItem(id) {
      if (confirm("Are you sure you want to delete this item?")) {
        try {
          await axios.delete(`/admin/pos/products/${id}`);
          this.fetchItems();
        } catch (error) {
          console.error("Error deleting item:", error);
          alert(
            "Error deleting item: " +
              (error.response?.data?.message || error.message),
          );
        }
      }
    },
    cancelEdit() {
      this.showAddForm = false;
      this.editingId = null;
      this.formData = {
        name: "",
        sku: "",
        category_id: "",
        stock: null,
        cost: null,
        price: null,
        min_stock: null,
        description: "",
        barcode: "",
      };
    },
  },
  mounted() {
    this.fetchItems();
    this.fetchCategories();
  },
};
</script>

<style scoped>
.small-box {
  border-radius: 4px;
  position: relative;
  display: block;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 15px;
  color: white;
}

.small-box .icon {
  font-size: 40px;
  opacity: 0.15;
  position: absolute;
  right: 10px;
  top: 10px;
}

.small-box .inner {
  padding: 10px 0;
}

.small-box h3 {
  margin: 0 0 10px 0;
  font-weight: bold;
  font-size: 24px;
}

.small-box p {
  margin: 0;
  font-size: 14px;
}

.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}
</style>
