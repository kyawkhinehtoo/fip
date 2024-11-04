<template>
  <app-layout
    ><template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Test View</h2>
    </template>

    <div class="py-6">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
       
      </div>
    </div>
  </app-layout>
</template>

<script>
import { ref, reactive, emits } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";

export default {
  components: {
    AppLayout,
  },
  name: "Test",
  setup(props) {
    const form = useForm({
      name: null,
      file: null,
      incident_id: props.data,
    });
    function generateTestPDF(data) {
      Inertia.post("/doTestPDF/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          closeModal();
          console.log("error ..".errors);
        },
        onStart: (pending) => {
          console.log("Loading .." + pending);
          loading.value = true;
        },
      });
    }
    return { form,generateTestPDF };
  },
};
</script>
