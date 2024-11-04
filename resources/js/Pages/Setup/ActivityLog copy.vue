<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">
        Activity Log
      </h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="activities.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  No.
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Log Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Description</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Subject Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Event</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Subject ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Causer Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Causer ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Properties</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Batch UUID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created At</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Updated At</th>

              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in activities.data " v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ activities.from + index }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.log_name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.description }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.subject_type }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.event }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.subject_id }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.causer_type }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.causer_id }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.properties }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.batch_uuid }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.created_at }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.updated_at }}</td>

              </tr>
            </tbody>
          </table>

        </div>
        <span v-if="activities.links">
          <pagination class="mt-6" :links="activities.links" />
        </span>
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

    onMounted(() => {
      console.log("Hello");
    })
  }



}
</script>
