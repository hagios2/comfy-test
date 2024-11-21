<template>
    <div class="py-4 container-fluid">
        <div class="mt-4 user">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header border-bottom">
                        <div class="user d-flex align-items-center">
                            <div class="col-6">
                                <h5 class="mb-0 ms-0">My Customers</h5>
                            </div>
                            <div class="col-6 text-end">
                                <material-button class="float-right btn btm-sm" @click="this.$router.push('/customers/create');">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Add Customer
                                </material-button>
                            </div>
                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="px-0 pb-0 card-body">
                        <!-- Table -->
                        <table class="table table-flush mt-3">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>CreatedAt</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                              <tr v-for="customer in customers" :key="customer.id">
                                <td class="text-sm font-weight-normal">{{customer.name}}</td>
                                <td class="text-sm font-weight-normal">{{customer.email}}</td>
                                <td class="text-sm font-weight-normal">{{customer.phone}}</td>
                                <td class="text-sm font-weight-normal">{{customer.address}}</td>
                                <td class="text-sm font-weight-normal">{{customer.created_at}}</td>
                                <td class="text-sm font-weight-normal">
                                    <div class="">
                                        <material-button @click="this.$router.push(`/edit/customer/${customer.id}`)" class="m-1 btn-circle" title="edit" size="sm">
                                            <i class="fas fa-pen"></i>
                                        </material-button>
                                        <material-button id="delBut" @click="deleteCustomer(customer.id)" class="m-1 btn-circle" size="sm" color="danger"
                                            title="delete">
                                            <i class="fas fa-trash"></i>
                                        </material-button>
                                    </div>
                                </td>
                              </tr>
                            </tbody>
                        </table>
                        <Paginator :meta="pagination.meta" @page-change="fetchCustomers" />
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
import Paginator from "@/views/components/Paginator.vue";

export default {
    name: "Invoice",
    components: {
      Paginator,
        MaterialButton,
    },
    data() {
        return {
          customers: [],
          pagination: {
              meta: {
                links: []
              },
              links: {}
            },
        }
    },
    async mounted() {
      await this.fetchCustomers()
    },
    methods: {
      async fetchCustomers(url = null) {
        try {
            url = url ?? process.env.VUE_APP_API_BASE_URL + '/customer'
            const { data } = await axios.get(url, {
                 headers: authHeader()
            })
            this.customers = data.data
            this.pagination.meta = data.meta;
            this.pagination.links = data.links;
        } catch (err) {
          this.showProMessage('error', 'Failed to load customers')
        }
      },
      showProMessage(type, message) {
          showSwal.methods.showSwal({
              type,
              message,
              width: 500
          });
      },
      async deleteCustomer(id) {
        const response = await showSwal.methods.showSwalConfirmationDelete('Are you sure you want to delete this customer')

        if (response.isConfirmed) {
          try {
            const url = process.env.VUE_APP_API_BASE_URL + `/customer/${id}`
            const { data } = await axios.delete(url, {
                 headers: authHeader()
            })
           await this.fetchCustomers()
        } catch (err) {
          this.showProMessage('error', 'Failed to delete customers')
        }
        }
      }
    }
};
</script>

<style scoped>
table thead tr th {
    padding-left: 1.5 rem !important;
}
</style>
