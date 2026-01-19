<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">POS System - Categories Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button @click="showAddForm = true" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Category
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
              <h3>{{ categories.length }}</h3>
              <p>Total Categories</p>
            </div>
            <div class="icon">
              <i class="fas fa-list"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ activeCategories }}</h3>
              <p>Active Categories</p>
            </div>
            <div class="icon">
              <i class="fas fa-check-circle"></i>
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
                {{ editingId ? "Edit Category" : "Add New Category" }}
              </h3>
              <button
                @click="cancelForm"
                type="button"
                class="btn btn-sm btn-secondary"
              >
                <i class="fas fa-times"></i> Close
              </button>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="small">Category Name *</label>
                  <input
                    v-model="formData.name"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="e.g., Electronics"
                    required
                  />
                </div>
                <div class="form-group col-md-6">
                  <label class="small">Active</label>
                  <div class="custom-control custom-checkbox mt-2">
                    <input
                      v-model="formData.is_active"
                      type="checkbox"
                      class="custom-control-input"
                      id="catActive"
                    />
                    <label class="custom-control-label small" for="catActive"
                      >Enable this category</label
                    >
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="small">Description</label>
                <textarea
                  v-model="formData.description"
                  class="form-control form-control-sm"
                  rows="2"
                  placeholder="Category description..."
                ></textarea>
              </div>
            </div>
            <div class="card-footer">
              <button @click="saveCategory" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> Save
              </button>
              <button @click="cancelForm" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories Table -->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">All Categories</h3>
              <div class="card-tools">
                <input
                  v-model="searchQuery"
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Search categories..."
                  style="width: 200px"
                />
              </div>
            </div>
            <div class="card-body">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="category in filteredCategories" :key="category.id">
                    <td>{{ category.name }}</td>
                    <td>{{ category.description }}</td>
                    <td>
                      <span
                        v-if="category.is_active"
                        class="badge badge-success"
                      >
                        Active
                      </span>
                      <span v-else class="badge badge-danger"> Inactive </span>
                    </td>
                    <td>{{ category.products_count || 0 }}</td>
                    <td>
                      <button
                        @click="editCategory(category)"
                        class="btn btn-xs btn-info"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        @click="deleteCategory(category.id)"
                        class="btn btn-xs btn-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredCategories.length === 0">
                    <td colspan="5" class="text-center text-muted">
                      No categories found
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
  name: "Categories",
  data() {
    return {
      categories: [],
      formData: {
        name: "",
        description: "",
        is_active: true,
      },
      editingId: null,
      showAddForm: false,
      searchQuery: "",
    };
  },
  computed: {
    activeCategories() {
      return this.categories.filter((c) => c.is_active).length;
    },
    filteredCategories() {
      if (!this.searchQuery) return this.categories;
      return this.categories.filter((c) =>
        c.name.toLowerCase().includes(this.searchQuery.toLowerCase()),
      );
    },
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get("/admin/pos/categories");
        this.categories = response.data.data || response.data;
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    },
    async saveCategory() {
      try {
        if (this.editingId) {
          await axios.put(
            `/admin/pos/categories/${this.editingId}`,
            this.formData,
          );
        } else {
          await axios.post("/admin/pos/categories", this.formData);
        }
        this.fetchCategories();
        this.cancelForm();
      } catch (error) {
        console.error("Error saving category:", error);
        alert("Error saving category: " + error.response.data.message);
      }
    },
    editCategory(category) {
      this.editingId = category.id;
      this.formData = { ...category };
      this.showAddForm = true;
    },
    async deleteCategory(id) {
      if (confirm("Are you sure you want to delete this category?")) {
        try {
          await axios.delete(`/admin/pos/categories/${id}`);
          this.fetchCategories();
        } catch (error) {
          console.error("Error deleting category:", error);
          alert("Error deleting category: " + error.response.data.message);
        }
      }
    },
    cancelForm() {
      this.showAddForm = false;
      this.editingId = null;
      this.formData = {
        name: "",
        description: "",
        is_active: true,
      };
    },
  },
  mounted() {
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
</style>
