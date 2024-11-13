<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer List</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!--  Search -->
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span
              class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i
                class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..."
              class="mb-2 border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10 py-2.5"
              id="search" v-model="search" v-on:keyup.enter="searchTsp" />
            <a href="#" class="text-left font-semibold text-xs " v-on:click="toggleAdv">Advance Search</a>
            <i class="ml-2 fas fa-chevron-down text-blueGray-400" v-show="!show_search"></i>
            <i class="ml-2 fas fa-chevron-up text-blueGray-400" v-show="show_search"></i>
          </div>
          <inertia-link :href="'/customer/create'"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</inertia-link>
        </div>
        <!-- End of Search -->
        <div v-show="show_search">
          <advance-search @search_parameter="goSearch" />
        </div>
        <!-- card -->


        <div class="grid gap-2 grid-cols-1 md:grid-cols-4 xl:grid-cols-4">

          <!-- card item -->
          <div
            class="flex items-center p-2 bg-white rounded-lg shadow-md dark:bg-gray-800 max-w-xs mt-4 cursor-pointer hover:bg-gray-50"
            @click="goActive">
            <div class="py-2 px-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
              <i class="fa fa-users"></i>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ active }}</label> Active
                Customers
              </p>
            </div>
          </div>
          <!-- card item -->
          <!-- card item -->
          <div
            class="flex items-center p-2 bg-white rounded-lg shadow-md dark:bg-gray-800 max-w-xs mt-4 cursor-pointer hover:bg-gray-50"
            @click="goRequest">
            <div class="py-2 px-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
              <i class="fa fa-chart-line"></i>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ installation_request }}</label>
                Installation Request
              </p>
            </div>
          </div>
          <!-- card item -->
          <!-- card item -->
          <div
            class="flex items-center p-2 bg-white rounded-lg shadow-md dark:bg-gray-800 max-w-xs mt-4 cursor-pointer hover:bg-gray-50"
            @click="goSuspense">
            <div
              class="py-2 px-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full dark:text-yellow-100 dark:bg-yellow-500">
              <i class="fa fas fa-pause"></i>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ suspense }}</label> Suspense
                Customer
              </p>
            </div>
          </div>
          <!-- card item -->
          <!-- card item -->
          <div
            class="flex items-center p-2 bg-white rounded-lg shadow-md dark:bg-gray-800 max-w-xs mt-4 cursor-pointer hover:bg-gray-50"
            @click="goTerminate">
            <div class="py-2 px-3 mr-4 text-red-500 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-500">
              <i class="fa fas fa-stop"></i>
            </div>
            <div>
              <p class="text-xs font-medium text-gray-600 dark:text-gray-400">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ terminate }}</label> Terminated
                Customer
              </p>
            </div>

          </div>

          <!-- card item -->

        </div>

        <!-- card -->

        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="customers.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  v-if="radius">Radius</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Order
                  Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prefer Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Package</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Township</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status</th>
                <th scope="col" class="relative px-6 py-3"> <a @click="goAll"
                    class="cursor-pointer items-center py-2 px-3 w-auto bg-white border border-transparent rounded-md font-semibold text-xs text-gray-200 tracking-widest hover:shadow-sm active:shadow-inner focus:outline-none focus:border-gray-50 focus:ring focus:ring-gray-100 disabled:opacity-25 transition"><i
                      class="fa fas fa-undo"></i></a></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="row in customers.data" v-bind:key="row.id" :class='" text-" + row.color'>
                <td class="px-3 py-3 text-xs font-medium whitespace-nowrap" v-if="radius">
                  <div
                    class="text-xs inline-flex items-center font-medium leading-sm capitalize  px-3 py-1 rounded-full"
                    :class="{
                      'bg-green-200 text-green-700 ': row.radius_status == 'online',
                      'bg-blue-200 text-blue-700': row.radius_status == 'offline',
                      'bg-red-200 text-red-700': row.radius_status == 'disabled',
                      'bg-orange-200 text-orange-700': row.radius_status == 'not found',
                      'bg-indigo-400 text-white border': row.radius_status == 'expired',
                      'bg-white text-gray-700 border': row.radius_status == 'no account'

                    }">{{ row.radius_status }}</div>
                </td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.order_date }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.prefer_install_date }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.package }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.township }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.status }}</td>

                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <inertia-link :href="route('customer.edit', row.id)"
                    class="text-indigo-400 hover:text-indigo-600 mr-2"><i class="fas fa-folder"></i></inertia-link>
                  <span v-if="role.delete_customer"> |
                    <a href="#" @click="deleteRow(row)" class="text-yellow-600 hover:text-yellow-900 ml-2"><i
                        class="fas fa-trash"></i></a>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>


        </div>
        <span v-if="customers.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ customers.data.length }} Customers in Current Page. Total Number of
            Customers : {{ customers.total }}</label>
        </span>
        <span v-if="customers.links">
          <pagination class="mt-6" :links="customers.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { onMounted, onUpdated, provide, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AdvanceSearch from "@/Components/AdvanceSearch";
export default {
  name: "Customer",
  components: {
    AppLayout,
    Pagination,
    AdvanceSearch,
  },
  props: {
    customers: Object,
    packages: Object,
    package_speed: Object,
    projects: Object,
    townships: Object,
    status: Object,
    errors: Object,
    dn: Object,
    active: Object,
    suspense: Object,
    installation_request: Object,
    terminate: Object,
    radius: Object,
    user: Object,
    role: Object,
    salePersons: Object,
    installationTeams: Object,
    onuSerials: Object,
  },
  setup(props) {
    provide('packages', props.packages);
    provide('projects', props.projects);
    provide('townships', props.townships);
    provide('status', props.status);
    provide('dn', props.dn);
    provide('package_speed', props.package_speed);
    provide('salePersons', props.salePersons);
    provide('installationTeams', props.installationTeams);
    provide('onuSerials', props.onuSerials);
    provide('role', props.role);
    const search = ref("");
    const sort = ref("");
    let show_search = ref(false);
    const searchTsp = () => {
      Inertia.post("/customer/search", { keyword: search.value }, { preserveState: true });
    };
    const goSearch = (parm) => {
      let url = "/customer/search";
      Inertia.post(url, parm, { preserveState: true });
    }
    const toggleAdv = () => {
      show_search.value = !show_search.value;
    }
    // const sortBy = (d) => {
    //   sort.value = d;
    //   sort.order = 'asc';
    //   if(sort.order == 'asc'){
    //     sort.order = 'desc';
    //   }
    //   console.log("search value is" + sort.value);
    //   Inertia.post('/customer/all/',{sort: sort.value, order:sort.order},{ preserveState: true });
    // };
    function goActive() {
      let url = "/customer/search";
      let parm = Object.create({});
      parm.status_type = 'active';
      Inertia.post(url, parm, { preserveState: true });
    }
    function goRequest() {
      let url = "/customer/search";
      let parm = Object.create({});
      parm.status = 1;
      Inertia.post(url, parm, { preserveState: true });
    }
    function goSuspense() {
      let url = "/customer/search";
      let parm = Object.create({});
      parm.status_type = 'suspense';
      Inertia.post(url, parm, { preserveState: true });
    }
    function goTerminate() {
      let url = "/customer/search";
      let parm = Object.create({});
      parm.status_type = 'terminate';
      Inertia.post(url, parm, { preserveState: true });


    }
    function goAll() {
      let url = "/customer/search";
      let parm = Object.create({});
      parm.status = 0;
      Inertia.post(url, parm, { preserveState: true });
    }
    const deleteRow = (data) => {
      if (!confirm("Are you sure want to Delete?")) return;
      data._method = "DELETE";
      Inertia.post("/customer/" + data.id, data);
    }
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.name} - ${x.type.toUpperCase()}`);
      });
      props.package_speed.map(function (x) {
        return (x.item_data = `${x.speed} Mbps - ${x.type.toUpperCase()}`);
      });
    });
    return { deleteRow, searchTsp, toggleAdv, goSearch, sort, search, show_search, goActive, goRequest, goSuspense, goTerminate, goAll };
  },
};
</script>
