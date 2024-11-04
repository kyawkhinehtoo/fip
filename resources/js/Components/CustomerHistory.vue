<template>
  <div v-if="!logs" wire:loading class=" w-full flex flex-col items-center justify-center">
              <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500"></div>
              <h2 class="text-center text-gray-600 text-sm font-semibold mt-2">Loading...</h2>
  </div>
  <div v-if="logs">
  <table class="min-w-full divide-y divide-gray-200 ">
    <thead class="bg-gray-50 text-left">
      <tr>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tra">No.</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Updated On</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Prev Status</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">New Status</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Actor</th>
        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider ">Details</th>
     
     </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 text-sm text-left">
      <tr v-for="(row, index) in logs" v-bind:key="row.id" >
        <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ index + 1 }}</td>
        <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ row.date }}</td>
        <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ row.old_status_name }}</td>
        <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ row.new_status_name }}</td>
        <td class="px-3 py-3 text-xs font-medium tracking-wider ">{{ row.actor_name }}</td>
        <td class="px-3 py-3 text-xs font-medium tracking-wider ">
          <span v-if="row.start_date">Start Date : {{row.start_date}} </span>
          <span v-if="row.end_date">, End Date : {{row.end_date}} <br /> </span><span v-else><br /> </span>
          <span v-if="row.old_address">Old Address : {{row.old_address}} <br /> </span>
          <span v-if="row.old_location">Old Lat Long : {{row.old_location}} <br /> </span>
          <span v-if="row.new_address">New Address : {{row.new_address}} <br /> </span>
          <span v-if="row.new_location">New Lat Long : {{row.new_location}} <br /> </span>
          <span v-if="row.old_package">Old Package : {{row.old_package_name}} <br /> </span>
          <span v-if="row.new_package">New Package : {{row.new_package_name}} <br /> </span>
          
          <span v-if="row.old_status_name">Previous Status : {{row.old_status_name}} <br /> </span>
          <span v-if="row.new_status_name">Current Status : {{row.new_status_name}} <br /> </span>
        </td>
  

      </tr>
    </tbody>
  </table>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
export default {
  name: "CustomerHistory",
  props: ["data"],
  setup(props) {
    const logs = ref();
    const getLog = async () => {
      let url = "/getCustomerHistory/" + props.data;
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
   0   }
    };
    const calculate = () => {
      getLog().then((d) => {
        logs.value = d;
      });
    };
    onMounted(()=>{
        calculate();
    });
    return {logs};
  },
};
</script>

<style>
</style>