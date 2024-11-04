<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SN Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tabs -->

        <div class="inline-flex w-full divide-y divide-gray-200">
          <ul id="tabs" class="flex">
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(1)" preserve-state>Genaral</a></li>
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(2)" preserve-state>Details</a></li>
          </ul>
        </div>

        <div class="flex justify-between space-x-2 items-end mb-2 px-1 mt-2 md:px-0">
          <div class="relative flex flex-wrap">
            <div v-show="tab==2">
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10" id="search" v-model="search" v-on:keyup.enter="searchPort" />
            </div>
          </div>
          <button
            @click="
              () => {
                (showSN = true), (editMode = false);
              }
            "
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
          >
            Create
          </button>
        </div>

        <div v-show="tab == 2">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="sns.data">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DN/Port</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN Name</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ports</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(row, index) in sns.data" v-bind:key="row.id">
                  <td class="px-6 py-3 whitespace-nowrap">{{ index + 1 }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ dns.filter((d) => d.id == row.dn_id)[0].name }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.port }}</td>
                  <td class="px-6 py-3 whitespace-nowrap">{{ row.description }}</td>
                  <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                    <a href="#" @click="editSN(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                    <a href="#" @click="confirmDelete(row.id)" class="text-red-600 hover:text-red-900">Delete</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
           <span v-if="sns.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ sns.data.length }} SN Port List in Current Page. Total Number of SN Ports :  {{ sns.total }}</label>
        </span>
          <span v-if="sns.links">
            <pagination class="mt-6" :links="sns.links" />
          </span>
        </div>
        <div v-show="tab == 1">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall.data" >
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SN Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DN Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ports</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in overall.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ dns.filter((d) => d.id == row.dn_id)[0].name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ports }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="confirmDelete(row.dn_id)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="overall.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ overall.data.length }} SN List in Current Page. Total Number of SNs :  {{ overall.total }}</label>
        </span>
          <span v-if="overall.links">
            <pagination class="mt-6" :links="overall.links" />
          </span>
      </div>
      </div>
    </div>
  </app-layout>
  <jet-confirmation-modal :show="id" @close="id = null">
    <template #title> Delete Node</template>
    <template #content> Are you sure you would like to delete this API token? </template>
    <template #footer>
      <jet-secondary-button @click="id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>
  <jet-dialog-modal :show="showSN" @close="showSN = false">
    <template #title> Add New Port </template>
    <template #content>
      <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
      <div>
        <div class="mt-4 text-sm">
          <div class="mt-1 flex rounded-md shadow-sm" v-if="dns.length !== 0">
            <multiselect deselect-label="Selected already" :options="dns" track-by="id" label="name" v-model="form.dn_id" :allow-empty="true"></multiselect>
          </div>
          <jet-input type="text" class="mt-1 block w-full" placeholder="SN Name" ref="text" v-model="form.name" v-if="tab==1" />
          <div class="mt-1 flex rounded-md shadow-sm" v-if="overall.length !== 0 && tab==2" >
            <multiselect deselect-label="Selected already" :options="overall" track-by="id" label="name" v-model="form.sn" :allow-empty="true"></multiselect>
          </div>
          <jet-input-error :message="form.error" class="mt-2" />
          <jet-input type="number" class="mt-1 block w-full" placeholder="SN Port Total" ref="number" v-model="form.port" />

          <jet-input-error :message="form.error" class="mt-2" />
          <jet-input type="text" class="mt-1 block w-full" placeholder="SN Description" ref="text" v-model="form.description" />

          <jet-input-error :message="form.error" class="mt-2" />
        </div>
      </div>
    </template>
    <template #footer>
      <jet-secondary-button @click="cancelSN"> Close </jet-secondary-button>
      <jet-button class="ml-2" @click="saveSN"> Save </jet-button>
    </template>
  </jet-dialog-modal>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import JetButton from "@/Jetstream/Button";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import { reactive, ref, onMounted, onUpdated } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "SNPorts",
  components: {
    AppLayout,
    Pagination,
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetConfirmationModal,
    JetInput,
    JetInputError,
    useForm,
    Multiselect,
  },
  props: {
    overall: Object,
    sns: Object,
    dns: Object,
    sns_all: Object,
    errors: Object,
  },

  setup(props) {
    let id = ref(null);
    let showSN = ref(false);
    let editMode = ref(false);
    let search = ref('');
    let tab = ref(1);
    function tabClick(val) {
      tab.value = val;
      if(tab.value == 1){
        form.port = 16;
        form.tab = 1;
      }else{
        form.port = 1;
        form.tab = 2;
      }
    }
    const form = useForm({
      id: null,
      sn: null,
      dn_id: null,
      name: null,
      port: 16,
      description: null,
      tab:1,
    });
    function confirmDelete(data) {
      id.value = data;
    }
    function resetForm() {
      form.id = null;
      form.dn_id = null;
      form.sn = null;
      form.name = null;
      form.port = 16;
      form.description = null;
      form.tab = tab.value;
    }
    function editSN(data) {
      form.id = data.id;
      form.dn_id = props.dns.filter((d) => d.id == data.dn_id)[0];
      form.sn = props.sns_all.filter((d) => d.name == data.name)[0];
      form.name = data.name;
      form.port = data.port;
      form.description = data.description;
      showSN.value = true;
      editMode.value = true;
      form.tab = 2;
    }
    function saveSN() {
      if (!editMode.value) {
       if (form.sn)
       form.name = form.sn.name;
       form._method = "POST";
          form.post("/snport", {
            preserveState: true,
            onSuccess: (page) => {
              showSN.value = false;
              resetForm();
              Toast.fire({
                icon: "success",
                title: page.props.flash.message,
              });
            },
            onError: (errors) => {
             
              console.log("error ..".errors);
            },
          });
        
      } else {
        form._method = "PUT";
        form.put("/snport/" + form.id, {
          preserveState: true,
          onSuccess: (page) => {
            showSN.value = false;
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            
            console.log("error ..".errors);
          },
        });
      }
    }
    function cancelSN() {
      showSN.value = false;
      resetForm();
    }
    function deleteNode() {
      let data = Object({});
      data.id = id.value;
      data._method = "DELETE";
      if(tab.value == 2){
          Inertia.post("/snport/" + data.id, data, {
          preserveScroll: true,
          preserveState: true,
          onSuccess: (page) => {
            id.value = false;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            showSN.value = false;
            console.log("error ..".errors);
          },
          });
      
      }else if(tab.value == 1 ){
          Inertia.post("/snport/group/" + data.id, data, {
          preserveScroll: true,
          preserveState: true,
          onSuccess: (page) => {
            id.value = false;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            showSN.value = false;
            console.log("error ..".errors);
          },
          });

      }
    }
    function searchPort(){
      Inertia.get('/snport/', {keyword : search.value}, { preserveState: true })
    }

    return { id, deleteNode, saveSN, editSN, cancelSN, showSN, form, editMode, confirmDelete, tabClick, tab,searchPort,search };
  },
};
</script>

<style>
</style>