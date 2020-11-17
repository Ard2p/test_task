<template>
  <div>
    <ProductsModal v-if="showCreate" :closemodal.sync="showCreate" />
    <ProductsEditModal
      v-if="showEdit"
      v-bind="{...products[indexEdit]}"
      :closemodal.sync="showEdit"
    />

    <button class="btn btn-success float-right my-3" @click="showCreate = true">
      Добавить
    </button>
    <table class="table table-striped" v-if="products">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">ID</th>
          <th scope="col">Артикул</th>
          <th scope="col">Название</th>
          <th scope="col">Описание</th>
          <th scope="col">Цена</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(product, index) in products" :key="product.id">
          <th scope="row">{{ index + 1 }}</th>
          <td>{{ product.id }}</td>
          <td>{{ product.article }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.description }}</td>
          <td>{{ product.price }}</td>
          <td>
            <button class="btn btn-warning" @click="editProduct(index)">
              Ред
            </button>
            <button class="btn btn-danger" @click="delProduct(index)">
              Удалить
            </button>
            <button class="btn btn-info" @click="openProduct(product.id)">
              JSON
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import ProductsModal from "./../components/ProductsModal.vue";
import ProductsEditModal from "./../components/ProductsEditModal.vue";

export default {
  name: "Products",
  components: { ProductsModal, ProductsEditModal },
  data() {
    return {
      showCreate: false,
      showEdit: false,
      indexEdit: null,
    };
  },
  mounted() {
    this.$store.dispatch("getProducts");
  },
  methods: {
    editProduct(index) {
      this.indexEdit = index;
      this.showEdit = true;
    },
    openProduct(id) {
      window.open("/api/products/" + id, "_blank");
    },
    delProduct(index) {
      this.$store.dispatch("delProduct", index);
    },
  },
  computed: {
    products() {
      return this.$store.state.products;
    },
  },
};
</script>

<style>
</style>
