<template>
    <div class="py-4 container-fluid">
        <div class="mt-4 user">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header border-bottom">
                        <div class="user d-flex align-items-center">
                            <div class="col-6">
                                <h5 class="mb-0 ms-0">Insurance Policies</h5>
                            </div>
                            <div class="col-6 text-end">
                                <material-button class="float-right btn btm-sm" @click="this.$router.push('/policies/create');">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Add Policy
                                </material-button>
                            </div>
                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="px-0 pb-0 card-body">

                      <div class="row m-lg-2">
                        <div class="col-md-2">
                          <material-input id="search" label="Search" v-model:value="filterBy.keyword" name="search" type="text" />
                        </div>
                        <div class="col-md-2">
                          <label>Filter by Policy Type</label>
                          <select v-model="filterBy.policy_type" class="form-control">
                            <option selected value="">Select Policy Type</option>
                            <option v-for="policy_type in policyTypes" :key="policy_type.id" :value="policy_type.name">{{policy_type.name}}</option>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <label>Filter by Status</label>
                          <select v-model="filterBy.status" class="form-control">
                             <option selected value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Active">Active</option>
                            <option value="Expired">Expired</option>
                          </select>
                        </div>
                        <div class="col-md-2">
                          <material-input class="inline" id="start_date" label="Start Date" variant="static" v-model:value="filterBy.start_date" name="start_date" type="date" />
                        </div>
                        <div class="col-md-2">
                          <material-input class="inline" id="end_date" label="End Date" variant="static" v-model:value="filterBy.end_date" name="end_date" type="date"/>
                        </div>
                      </div>
                        <!-- Table -->
                        <table class="table table-flush mt-3 table-responsive">
                            <thead class="thead-light">
                                <tr>
                                    <th>Policy Number</th>
                                    <th>Policy Type</th>
                                    <th>Customer</th>
                                    <th>Premium Amount</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
<!--                                    <th>Created At</th>-->
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                              <tr v-for="policy in policies" :key="policy.id">
                                <td class="text-sm font-weight-normal">{{policy.policy_no}}</td>
                                <td class="text-sm font-weight-normal">{{policy.policy_type}}</td>
                                <td class="text-sm font-weight-normal">{{policy?.customer?.name}}</td>
                                <td class="text-sm font-weight-normal">{{policy?.premium_amount}}</td>
                                <td class="text-sm font-weight-normal">
                                  <material-badge variant="gradient" :color="policy.status === 'Pending' ? 'secondary' : 'success'" > {{policy.status}}</material-badge>
                                </td>
                                <td class="text-sm sm font-weight-normal">{{policy.formatted_start_date}}</td>
                                <td class="text-sm font-weight-normal">{{policy.formatted_end_date}}</td>
<!--                                <td class="text-sm font-weight-normal">{{invoice.total}}</td>-->
<!--                                <td class="text-sm font-weight-normal">{{invoice.currency}}</td>-->
<!--                                <td class="text-sm font-weight-normal">{{policy.created_at}}</td>-->
                                <td class="text-sm font-weight-normal">
                                    <div class="text-end">
                                        <material-button color="warning" @click="this.$router.push(`/policies/update/${policy.id}`)" class="m-1 btn-circle" title="Edit" size="sm">
                                            <i class="fas fa-pen"></i>
                                        </material-button>
                                        <material-button id="delBut" @click="deletePolicy(policy.id)" class="m-1 btn-circle" size="sm" color="danger"
                                            title="delete">
                                            <i class="fas fa-trash"></i>
                                        </material-button>
                                    </div>
                                </td>
                              </tr>
                            </tbody>
                        </table>

                        <Paginator :meta="pagination.meta" @page-change="fetchPolicies" />

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MaterialButton from "@/components/MaterialButton.vue";
import showSwal from "@/mixins/showSwal";
import authHeader from "@/services/auth-header.js";
import axios from "axios";
import MaterialBadge from "@/components/MaterialBadge.vue";
import MaterialInput from "@/components/MaterialInput.vue";
import Paginator from "@/views/components/Paginator.vue";

export default {
    name: "Policies",
    components: {
      Paginator,
      MaterialInput,
      MaterialBadge,
      MaterialButton,
    },
    data() {
        return {
            policies: [],
          pagination: {
            meta: {
              links: []
            },
            links: {}
          },
            filterBy: {
              keyword: null,
              policy_type: null,
              status: null,
              start_date: null,
              end_date: null
            },
            policyTypes: [
              { id: 1, name: "Health" },
              { id: 2, name: "Life" },
              { id: 3, name: "Auto" },
              { id: 4, name: "Property" },
              { id: 5, name: "Travel" },
            ]
        }
    },
    async mounted() {
        await this.fetchPolicies();
    },
    watch: {
        'filterBy.keyword': 'applyFilters',
        'filterBy.policy_type': 'applyFilters',
        'filterBy.status': 'applyFilters',
        'filterBy.start_date': 'applyFilters',
        'filterBy.end_date': 'applyFilters'
    },
    methods: {
      async fetchPolicies(url = null) {
        try {
            url  =  url ??  process.env.VUE_APP_API_BASE_URL + '/insurancePolicy';
            const params = {
              keyword: this.filterBy.keyword,
              policy_type: this.filterBy.policy_type,
              status: this.filterBy.status,
              start_date: this.filterBy.start_date,
              end_date: this.filterBy.end_date
            };
            const { data } = await axios.get(url, {
              params,
              headers: authHeader()
            });
            this.policies = data.data;
            this.pagination.meta = data.meta;
            this.pagination.links = data.links;
            console.log(this.pagination.meta, this.pagination.links)
        } catch (error) {
            this.showMessage('error', 'Failed to fetch policies');
        }
      },
      applyFilters() {
        this.fetchPolicies();
      },
      async deletePolicy(id) {
        const response = await showSwal.methods.showSwalConfirmationDelete('Are you sure you want to delete this policy');

        if (response.isConfirmed) {
          try {
            const url = process.env.VUE_APP_API_BASE_URL + `/insurancePolicy/${id}`;
            await axios.delete(url, {
              headers: authHeader()
            });
            await this.fetchPolicies();
          } catch (err) {
            this.showMessage('error', 'Failed to delete policy');
          }
        }
      },
      getPageFromUrl(url) {
        const params = new URL(url).searchParams;
        return params.get("page");
      },
      showMessage(type, message) {
          showSwal.methods.showSwal({
              type,
              message,
              width: 500
          });
      }
    }
};
</script>


<style scoped>
table thead tr th {
    padding-left: 1.5 rem !important;
}
</style>
