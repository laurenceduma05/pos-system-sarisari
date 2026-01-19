<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Events data - will be loaded from API
const events = ref([]);
const isLoading = ref(true);

// Filter options
const filterOptions = ref({
  status: 'all', // all, upcoming, past, current
  dateRange: 'all' // all, today, week, month
});

// Available filter options
const statusOptions = [
  { value: 'all', label: 'All Events' },
  { value: 'upcoming', label: 'Upcoming' },
  { value: 'current', label: 'Current' },
  { value: 'past', label: 'Past' }
];

const dateRangeOptions = [
  { value: 'all', label: 'All Time' },
  { value: 'today', label: 'Today' },
  { value: 'week', label: 'This Week' },
  { value: 'month', label: 'This Month' }
];

// Load events from API
const loadEvents = async () => {
  isLoading.value = true;
  try {
    let url = '/api/admin/events';

    // Add query parameters based on filters
    const params = {};
    if (filterOptions.value.status !== 'all') {
      if (filterOptions.value.status === 'upcoming') {
        url = '/api/admin/events/upcoming';
      } else if (filterOptions.value.status === 'past') {
        url = '/api/admin/events/past';
      }
    }

    const response = await axios.get(url, { params });
    events.value = response.data.events?.data || response.data.events || [];
  } catch (error) {
    console.error('Error loading events:', error);
    events.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Check if event is currently ongoing
const isEventCurrent = (event) => {
  const now = new Date();
  const start = new Date(event.start_time);
  const end = new Date(event.end_time);
  return now >= start && now <= end;
};

// Check if event is upcoming
const isEventUpcoming = (event) => {
  const now = new Date();
  const start = new Date(event.start_time);
  return now < start;
};

// Get event status badge class
const getEventStatus = (event) => {
  if (isEventCurrent(event)) return { text: 'Ongoing', class: 'badge-success' };
  if (isEventUpcoming(event)) return { text: 'Upcoming', class: 'badge-primary' };
  return { text: 'Completed', class: 'badge-secondary' };
};

// Handle filter changes
const handleFilterChange = () => {
  loadEvents();
};

// Initialize component
onMounted(() => {
  loadEvents();
});
</script>

<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">View Events</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <router-link to="/admin/dashboard">Home</router-link>
            </li>
            <li class="breadcrumb-item">
              <router-link to="/admin/events">Events</router-link>
            </li>
            <li class="breadcrumb-item active">View Events</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <!-- Filter Controls -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Filter Events</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Event Status</label>
                <select
                  v-model="filterOptions.status"
                  @change="handleFilterChange"
                  class="form-control"
                >
                  <option
                    v-for="option in statusOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Date Range</label>
                <select
                  v-model="filterOptions.dateRange"
                  @change="handleFilterChange"
                  class="form-control"
                >
                  <option
                    v-for="option in dateRangeOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button
                @click="loadEvents"
                class="btn btn-primary"
                :disabled="isLoading"
              >
                <span v-if="isLoading" class="spinner-border spinner-border-sm"></span>
                Refresh
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Events List -->
      <div class="card mt-4">
        <div class="card-header">
          <h3 class="card-title">Events List</h3>
        </div>
        <div class="card-body">
          <!-- Loading State -->
          <div v-if="isLoading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading events...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="events.length === 0" class="text-center py-4">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h4>No events found</h4>
            <p class="text-muted">No events match your current filters.</p>
            <router-link to="/admin/events/create" class="btn btn-primary">
              <i class="fas fa-plus mr-1"></i>
              Create New Event
            </router-link>
          </div>

          <!-- Events Grid -->
          <div v-else class="row">
            <div
              v-for="event in events"
              :key="event.id"
              class="col-lg-6 col-xl-4 mb-4"
            >
              <div class="card h-100 event-card">
                <div class="card-header">
                  <h5 class="card-title mb-0">{{ event.title }}</h5>
                  <span
                    class="badge float-right"
                    :class="getEventStatus(event).class"
                  >
                    {{ getEventStatus(event).text }}
                  </span>
                </div>
                <div class="card-body">
                  <div class="event-details">
                    <p class="text-muted mb-2">
                      <i class="fas fa-user mr-2"></i>
                      <strong>By:</strong> {{ event.by }}
                    </p>

                    <p class="mb-2">
                      <i class="fas fa-play-circle mr-2"></i>
                      <strong>Starts:</strong> {{ formatDate(event.start_time) }}
                    </p>

                    <p class="mb-3">
                      <i class="fas fa-stop-circle mr-2"></i>
                      <strong>Ends:</strong> {{ formatDate(event.end_time) }}
                    </p>

                    <div class="event-description">
                      <strong>Details:</strong>
                      <p class="mt-1">{{ event.details }}</p>
                    </div>

                    <div v-if="event.link" class="mt-3">
                      <a
                        :href="event.link"
                        target="_blank"
                        class="btn btn-sm btn-outline-primary"
                      >
                        <i class="fas fa-external-link-alt mr-1"></i>
                        Event Link
                      </a>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <small class="text-muted">
                    Created: {{ formatDate(event.created_at) }}
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.event-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  border: 1px solid #e3e6f0;
}

.event-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.event-details {
  font-size: 0.9rem;
}

.event-description {
  max-height: 100px;
  overflow-y: auto;
  padding: 8px;
  background-color: #f8f9fa;
  border-radius: 4px;
  border-left: 3px solid #007bff;
}

.badge {
  font-size: 0.7rem;
  padding: 4px 8px;
}

.card-title {
  font-size: 1.1rem;
  font-weight: 600;
}

.text-muted {
  font-size: 0.8rem;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .col-lg-6 {
    flex: 0 0 100%;
    max-width: 100%;
  }
}
</style>
