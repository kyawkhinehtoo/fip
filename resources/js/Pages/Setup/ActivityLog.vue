<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Activity Log
      </h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- 
        <div v-for="(activity, index) in activities.data" :key="index" class="activity-log">
          <p><strong>Activity:</strong> {{ activity.description }}</p>
          <p><strong>Changed by:</strong> {{ activity.causer }}</p>
          <p><strong>Date:</strong> {{ activity.date }}</p>

          <table v-if="Object.keys(activity.changes).length > 0" class="table-auto w-full border">
            <thead>
              <tr>
                <th class="border px-4 py-2">Field</th>
                <th class="border px-4 py-2">From</th>
                <th class="border px-4 py-2">To</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(change, field) in activity.changes" :key="field">
                <td class="border px-4 py-2">{{ field }}</td>
                <td class="border px-4 py-2">{{ change.from ?? 'N/A' }}</td>
                <td class="border px-4 py-2">{{ change.to ?? 'N/A' }}</td>
              </tr>
            </tbody>
          </table>
          <p v-else>No changes recorded.</p>

          <hr class="my-4" />
        </div> -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

          <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="activities.data">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    No.
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Activity</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Change By</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    #</th>



                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <template v-for="(row, index) in activities.data " v-bind:key="row.id">
                  <tr>
                    <td class="px-6 py-2 text-sm whitespace-nowrap">{{ activities.from + index }}</td>
                    <td class="px-6 py-2 text-sm whitespace-nowrap">{{ row.description }}</td>
                    <td class="px-6 py-2 text-sm whitespace-nowrap">{{ row.causer }}</td>
                    <td class="px-6 py-2 text-sm whitespace-nowrap">{{ row.date }}</td>
                    <td class="px-6 py-2 text-sm whitespace-nowrap">
                      <a href="#" @click="toggleDetails(index)" class="ml-2">
                        <span v-if="Object.keys(row.changes).length > 0">
                          <span v-if="expandedRow === index">
                            <i class="fa fa-circle-chevron-down text-indigo-400"></i>
                          </span>
                          <span v-else> <i class="fa fa-circle-chevron-right text-gray-400"></i></span>
                        </span>


                      </a>
                    </td>
                  </tr>
                  <tr v-if="Object.keys(row.changes).length > 0 && (expandedRow === index)"
                    class="bg-indigo-100 text-gray-800">

                    <th class="px-6 text-sm text-left" scope="col">Field</th>
                    <th class="px-6 text-sm text-left" scope="col">From</th>
                    <th class="px-6 text-sm text-left" scope="col" colspan="3">To</th>
                  </tr>
                  <tr v-if="Object.keys(row.changes).length > 0 && (expandedRow === index)"
                    v-for="(change, field) in row.changes" :key="field" class="bg-indigo-50 text-gray-600">

                    <td class="px-6 text-sm capitalize">{{ field.replace("_", " ") }}</td>
                    <td class="px-6 text-sm">{{ change.from ?? 'N/A' }}</td>
                    <td class="px-6 text-sm" colspan="3">{{ change.to ?? 'N/A' }}</td>
                  </tr>



                </template>
              </tbody>
            </table>

          </div>
          <span v-if="activities.links">
            <pagination class="mt-6" :links="activities.links" />
          </span>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { reactive, ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia'
export default {
  name: 'ActivityLog',
  components: {
    AppLayout,
    Pagination
  },
  props: {
    activities: Object,
    errors: Object
  },
  setup(props) {
    let expandedRow = ref(false);
    function toggleDetails(index) {
      // Toggle the clicked row and collapse others
      expandedRow.value = expandedRow.value === index ? null : index;
    }
    return { expandedRow, toggleDetails };
  }



}
</script>
