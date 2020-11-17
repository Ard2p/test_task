
import Axios from 'axios';
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    products: [],
  },

  actions: {
    async getProducts({ commit }) {
      return commit('setProducts', await axios.get('/products'))
    },
    async delProduct({ commit, state }, index) {
      await axios.delete('/products/' + state.products[index].id);
      await commit('delProduct', index)
      return commit('setProducts', await axios.get('/products'))
    },
  },

  mutations: {
    delProduct(state, index) {
      state.products.splice(index);
    },
    setProducts(state, response) {
      state.products = response.data;
    },
  }
});
