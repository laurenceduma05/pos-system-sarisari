import Dashboard from "./components/Dashboard.vue";
import ListAppointments from "./pages/appointments/ListAppointments.vue";
import AppointmentForm from "./pages/appointments/AppointmentForm.vue";
import UserList from "./pages/users/UserList.vue";
import UpdateSettings from "./pages/settings/UpdateSettings.vue";
import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import ProfileForm from "./pages/profile/ProfileForm.vue";
import Login from "./pages/auth/Login.vue";
import ListTeachings from "./pages/teachings/ListTeachings.vue";
import TeachingForm from "./pages/teachings/TeachingForm.vue";
import ListQuotas from "./pages/quotas/ListQuotas.vue";
import QuotasForm from "./pages/quotas/QuotasForm.vue";
import ListSongs from "./pages/songs/ListSongs.vue";
import SongsForm from "./pages/songs/SongsForm.vue";
import ViewSong from "./pages/songs/ViewSong.vue";
import EventsForm from "./pages/events/EventsForm.vue";
import ListEvents from "./pages/events/ListEvents.vue";
// FIX: Changed the import path for ViewEvents to assume it's in the events folder
import ViewEvents from "./pages/events/ViewEvents.vue";
import ListChildDedication from "./pages/child-dedication/ListChildDedication.vue";
import ChildDedicationForm from "./pages/child-dedication/ChildDedicationForm.vue";
import ListMembers from "./pages/members/ListMembers.vue";
import MembersForm from "./pages/members/MembersForm.vue";
import POS from "./pages/pos/POS.vue";
import Inventory from "./pages/inventory/Inventory.vue";
import POSCategories from "./pages/categories/Categories.vue";
import POSProducts from "./pages/products/Products.vue";
import POSCustomers from "./pages/customers/Customers.vue";
import POSOrders from "./pages/orders/Orders.vue";
import POSDashboard from "./components/Dashboard.vue";
import Cashier from "./pages/cashier/Cashier.vue";

export default [
  {
    path: "/login",
    name: "admin.login",
    component: Login,
  },

  {
    path: "/admin/dashboard",
    name: "Dashboard",
    component: Dashboard,
    icon: "tachometer-alt",
  },

  // Admin - POS Management Routes
  {
    path: "/admin/pos/dashboard",
    name: "POS Dashboard",
    component: POSDashboard,
  },

  // POS Module - Cashier
  {
    path: "/admin/cashier",
    name: "Cashier",
    component: Cashier,
    icon: "cash-register",
  },

  {
    path: "/admin/pos/categories",
    name: "Categories",
    component: POSCategories,
  },

  {
    path: "/admin/pos/products",
    name: "Products",
    component: POSProducts,
  },

  {
    path: "/admin/pos/customers",
    name: "Customers",
    component: POSCustomers,
  },

  {
    path: "/admin/pos/orders",
    name: "Orders",
    component: POSOrders,
  },

  // User - POS Checkout Interface
  {
    path: "/pos",
    name: "POS",
    component: POS,
    icon: "shopping-cart",
  },

  // Inventory System
  {
    path: "/admin/inventory",
    name: "Inventory",
    component: Inventory,
    icon: "warehouse",
  },

  // START COMMENTED OUT - Songs Module
  // {
  //    path: '/admin/songs',
  //    name: 'Songs',
  //    component: ListSongs,
  //    icon: 'calendar-alt',
  // },
  //
  // {
  //    path: '/admin/songs/create',
  //    name: 'admin.songs.create',
  //    component: SongsForm,
  //    // icon: 'calendar-alt',
  // },
  //
  // {
  //    path: '/admin/songs/:id/edit',
  //    name: '/admin.songs.edit',
  //    component: SongsForm,
  // },
  //
  // {
  //    // path: '/admin/songs/:id/view',
  //    path: '/admin/songs/view',
  //    name: '/admin.songs.view',
  //    component: ViewSong,
  // },
  // END COMMENTED OUT - Songs Module

  // START COMMENTED OUT - Teachings Module
  // {
  //     path: '/admin/teachings',
  //     name: 'Teachings',
  //     component: ListTeachings,
  //     icon: 'calendar-alt',
  // },
  //
  // {
  //     path: '/admin/teachings/create',
  //     name: 'admin.teachings.create',
  //     component: TeachingForm,
  //     // icon: 'calendar-alt',
  // },
  //
  // {
  //     path: '/admin/teachings/:id/edit',
  //     name: '/admin.teachings.edit',
  //     component: TeachingForm,
  // },
  // END COMMENTED OUT - Teachings Module

  // start of quotas
  {
    path: "/admin/quotas",
    name: "Quotas",
    component: ListQuotas,
    icon: "calendar-alt",
  },

  {
    path: "/admin/quotas/create",
    name: "admin.quotas.create",
    component: QuotasForm,
    // icon: 'calendar-alt',
  },

  {
    path: "/admin/quotas/:id/edit",
    name: "/admin.quotas.edit",
    component: QuotasForm,
  },

  // end of quotas

  {
    path: "/admin/appointments",
    name: "Appointments",
    component: ListAppointments,
    icon: "calendar-alt",
  },

  {
    path: "/admin/appointments/create",
    name: "admin.appointment.create",
    component: AppointmentForm,
    // icon: 'calendar-alt',
  },

  {
    path: "/admin/appointments/:id/edit",
    name: "/admin.appointments.edit",
    component: AppointmentForm,
  },

  {
    path: "/admin/users",
    name: "Users",
    component: UserList,
    icon: "users",
  },

  {
    path: "/admin/settings",
    name: "Settings",
    component: UpdateSettings,
    icon: "cog",
  },

  {
    path: "/admin/profile",
    name: "Profile",
    component: UpdateProfile,
    icon: "user",
  },
  {
    path: "/admin/profile/:id/edit",
    name: "/admin.profile.edit",
    component: ProfileForm,
  },

  // START COMMENTED OUT - Events Module
  // {
  //    path: '/admin/events',
  //    name: 'Events',
  //    component: ListEvents,
  //    icon: 'calendar-alt',
  // },
  //
  // {
  //    path: '/admin/events/create',
  //    name: 'admin.events.create',
  //    component: EventsForm,
  //    // icon: 'calendar-alt',
  // },
  //
  // {
  //    path: '/admin/events/:id/edit',
  //    name: '/admin.events.edit',
  //    component: EventsForm, // FIX: Changed from SongsForm to EventsForm
  // },
  //
  // {
  //    // path: '/admin/events/:id/view',
  //    path: '/admin/events/view',
  //    name: '/admin.events.view',
  //    component: ViewEvents,
  // },
  // END COMMENTED OUT - Events Module

  // START COMMENTED OUT - Child Dedication Module
  // {
  //    path: '/admin/child-dedications',
  //    name: 'Child-Dedication',
  //    component: ListChildDedication,
  //    icon: 'calendar-alt',
  // },
  //
  // {
  //    path: '/admin/child-dedication/create',
  //    name: 'admin.child-dedication.create',
  //    component:ChildDedicationForm,
  //    // icon: 'calendar-alt',
  // },
  //
  // {
  //    path: '/admin/child-dedication/:id/edit',
  //    name: '/admin.child-dedication.edit',
  //    component: ChildDedicationForm,
  // },
  // END COMMENTED OUT - Child Dedication Module

  // START COMMENTED OUT - Members Module
  // {
  //     path: '/admin/members',
  //     name: 'members',
  //     component: ListMembers,
  //     icon: 'calendar-alt',
  // },
  //
  // {
  //     path: '/admin/members/create',
  //     name: 'admin.members.create',
  //     component:MembersForm,
  // },
  //
  // {
  //     path: '/admin/members/:id/edit',
  //     name: '/admin.members.edit',
  //     component: MembersForm,
  // },
  // END COMMENTED OUT - Members Module
];
