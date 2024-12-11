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
  policyId: {
    type: Number,
    default: null,
  }
});

const router = useRouter();
const API_URL = process.env.VUE_APP_API_BASE_URL;

const form = ref({
  policy_type: null,
  customer_id: null,
  start_date: null,
  end_date: null,
  premium_amount: null
});
const customers = ref([])

const policyTypes = [
  { id: 1, name: "Health" },
  { id: 2, name: "Life" },
  { id: 3, name: "Auto" },
  { id: 4, name: "Property" },
  { id: 4, name: "Travel" },
];

const loadPolicy = async () => {
  if (props.policyId) {
    try {
      const {data} = await axios.get(`${API_URL}/insurancePolicy/${props.policyId}`, {
        headers: authHeader(),
      });
      form.value = {
      ...data.data,
      customer_id: data.data?.customer?.id,
    };
    } catch (error) {
      showMessage('error', 'Failed to load policy data.');
    }
  }
};

const fetchCustomers = async () => {
  const url = `${API_URL}/customer`;
  const { data } = await axios.get(url, { headers: authHeader() });
  customers.value = data.data;
};

const submit = async () => {
  try {
    let response;
    if (props.policyId) {
      response = await axios.put(`${API_URL}/insurancePolicy/${props.policyId}`, form.value, {
        headers: authHeader(),
      });
    } else {
      response = await axios.post(`${API_URL}/insurancePolicy`, form.value, {
        headers: authHeader(),
      });
    }

    showMessage('success', response.data.message);
    await router.push('/policies');
  } catch (error) {
    console.log(error)
    const errors = error.response.data.errors
    console.log('here', errors)
    if (errors.length > 0) {
      errors.forEach(err => showMessage('error', err.detail))
    } else {
       showMessage('error', error.response.data.message)
    }
    // showMessage('error', 'Failed to save customer.');
  }
};

const showMessage = (type, message) => {
  showSwal.methods.showSwal({
    type,
    message,
    width: 500,
  });
};

onMounted(() => {
  fetchCustomers()
  loadPolicy();
});
</script>

<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">{{ policyId ? 'Edit Policy' : 'New Policy' }}</h6>
            </div>
          </div>
          <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
              <VeeForm role="form" class="text-start mt-3" @submit="submit">
                <div class="container">
                  <div class="row">
                   <div class="col-md-3">
                        <label class="ms-0">Select Customer </label>
                        <select v-model="form.customer_id" class="form form-control" name="customer_id" required>
                          <option selected>Select Customer</option>
                          <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{customer.name}}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                      <material-input id="premium_amount" label="Premium Amount" variant="static" v-model:value="form.premium_amount" name="premium_amount" type="number" :is-required="true" />
                    </div>
                     <div class="col-md-3">
                        <label class="ms-0">Select Policy Type</label>
                        <select v-model="form.policy_type" class="form form-control" required>
                          <option selected value="">Select Policy Type</option>
                          <option v-for="policy_type in policyTypes" :key="policy_type.id" :value="policy_type.name">{{policy_type.name}}</option>
                        </select>
                     </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <material-input id="start_date" label="Start Date" variant="static" v-model:value="form.start_date" name="start_date" type="date" :is-required="true" />
                    </div>
                    <div class="col-md-3">
                      <material-input id="end_date" label="End Date" variant="static" v-model:value="form.end_date" name="end_date" type="date" :is-required="true" />
                    </div>
                  </div>
                  <div class="mt-5 mb-5">
                    <div class="row">
                      <div class="col-md-3">
                        <material-button type="submit" class="m-1 btn-circle float-right" size="sm" color="success" title="Submit">
                          <i class="fas fa-mail"></i> {{ policyId ? 'Update' : 'Submit' }}
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
