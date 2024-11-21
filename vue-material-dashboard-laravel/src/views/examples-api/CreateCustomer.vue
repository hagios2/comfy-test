<script setup>
import { Form as VeeForm } from 'vee-validate';
import MaterialInput from "@/components/MaterialInput.vue";
import MaterialButton from "@/components/MaterialButton.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from 'vue-router';
import authHeader from "@/services/auth-header.js";
import showSwal from "@/mixins/showSwal.js";

const props = defineProps({
  customerId: {
    type: Number,
    default: null,
  }
});

const router = useRouter();
const API_URL = process.env.VUE_APP_API_BASE_URL;

const form = ref({
  name: null,
  email: null,
  phone: null,
  address: null,
});

const loadCustomer = async () => {
  if (props.customerId) {
    try {
      const {data} = await axios.get(`${API_URL}/customer/${props.customerId}`, {
        headers: authHeader(),
      });
      form.value = data.data;
    } catch (error) {
      showMessage('error', 'Failed to load customer data.');
    }
  }
};

const submit = async () => {
  try {
    let response;
    if (props.customerId) {
      response = await axios.put(`${API_URL}/customer/${props.customerId}`, form.value, {
        headers: authHeader(),
      });
    } else {
      response = await axios.post(`${API_URL}/customer`, form.value, {
        headers: authHeader(),
      });
    }

    showMessage('success', response.data.message);
    await router.push('/customers');
  } catch (error) {
    showMessage('error', 'Failed to save customer.');
  }
};

const showMessage = (type, message) => {
  showSwal.methods.showSwal({
    type,
    message,
    width: 500,
  });
};

// Load customer data if editing
onMounted(() => {
  loadCustomer();
});
</script>

<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">{{ customerId ? 'Edit Customer' : 'New Customer' }}</h6>
            </div>
          </div>
          <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
              <VeeForm role="form" class="text-start mt-3" @submit="submit">
                <div class="container">
                  <div class="row">
                    <div class="col-md-3">
                      <material-input id="name" label="Name" placeholder="Customer Name" variant="static" v-model:value="form.name" name="name" :is-required="true" />
                    </div>
                    <div class="col-md-3">
                      <material-input id="email" label="Email" variant="static" v-model:value="form.email" name="email" type="text" :is-required="true" />
                    </div>
                    <div class="col-md-2">
                      <material-input id="phone" label="Phone" variant="static" v-model:value="form.phone" name="phone" type="text" :is-required="true" />
                    </div>
                    <div class="col-md-2">
                      <material-input id="address" label="Address" variant="static" v-model:value="form.address" name="address" type="text" :is-required="true" />
                    </div>
                  </div>
                  <div class="mt-5 mb-5">
                    <div class="row">
                      <div class="col-md-3">
                        <material-button type="submit" class="m-1 btn-circle float-right" size="sm" color="success" title="Submit">
                          <i class="fas fa-mail"></i> {{ customerId ? 'Update' : 'Submit' }}
                        </material-button>
                      </div>
                    </div>
                  </div>
                </div>
              </VeeForm>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
