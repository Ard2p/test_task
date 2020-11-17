<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">             
              <h5 class="modal-title">Новый товар</h5>
               <button
                type="button"
                class="close"
                @click="$emit('update:closemodal', false)"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div
                v-if="status_msg"
                :class="{
                  'alert-success': status,
                  'alert-danger': !status,
                }"
                class="alert"
                role="alert"
              >
                {{ status_msg }}
              </div>
              <form>
                <div class="form-group">
                  <label for="name">Название</label>
                  <input
                    id="name"
                    v-model="name"
                    type="text"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="article">Артикул</label>
                  <input
                    id="article"
                    v-model="article"
                    type="text"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="price">Цена</label>
                  <input
                    id="price"
                    v-model="price"
                    type="number"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="description">Описание</label>
                  <textarea
                    id="description"
                    v-model="description"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    rows="3"
                    required
                  />
                </div>

                <div
                  class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm"
                >
                  <input
                    id="upload"
                    type="file"
                    ref="upload"
                    multiple
                    @change="onFileChange"
                    class="form-control border-0"
                    accept="image/x-png,image/gif,image/jpeg"
                  />
                  <label
                    id="upload-label"
                    for="upload"
                    class="font-weight-light text-muted"
                    >Изображения...</label
                  >
                  <div class="input-group-append">
                    <label
                      for="upload"
                      class="btn btn-light m-0 rounded-pill px-4"
                    >
                      <i class="fa fa-cloud-upload mr-2 text-muted"></i
                      ><small class="text-uppercase font-weight-bold text-muted"
                        >Выберите файлы</small
                      ></label
                    >
                  </div>
                </div>

                <div v-if="images" class="row">
                  <div
                    v-for="(image, index) in images"
                    :key="index"
                    class="col-3 preview"
                  >
                    <img :src="image" class="img-thumbnail" />

                    <svg
                      width="1em"
                      height="1em"
                      viewBox="0 0 16 16"
                      class="bi bi-x-circle btn-cross"
                      xmlns="http://www.w3.org/2000/svg"
                      @click.prevent="removeImage(index)"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"
                      />
                      <path
                        fill-rule="evenodd"
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
                      />
                    </svg>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-success float-right"
                @click="createProduct"
              >
                {{ isPosting ? "Отправка..." : "Отправить" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style>
#upload {
  opacity: 0;
}

#upload-label {
  position: absolute;
  top: 50%;
  left: 1rem;
  transform: translateY(-50%);
}
.preview .btn-cross {
  position: absolute;
  top: 0.5rem;
  right: 1.5rem;
  fill: red;
}
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
</style>

<script>
import { mapActions } from "vuex";
export default {
  name: "newProduct",
  data() {
    return {
      name: "",
      description: "",
      article: "",
      price: "",
      images: [],

      status_msg: "",
      status: "",
      isPosting: false,
    };
  },
  methods: {
    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.createImage(files);
    },
    createImage(files) {
      var vm = this;
      for (var index = 0; index < files.length; index++) {
        var reader = new FileReader();
        reader.onload = function (event) {
          const imageUrl = event.target.result;
          vm.images.push(imageUrl);
        };
        reader.readAsDataURL(files[index]);
      }
    },
    removeImage(index) {
      this.images.splice(index, 1);
    },
    createProduct(e) {
      e.preventDefault();
      if (!this.validateForm()) {
        return false;
      }
      // const that = this;
      this.isPosting = true;
      e;
      const formData = new FormData();
      formData.append("name", this.name);
      formData.append("price", this.price);
      formData.append("article", this.article);
      formData.append("description", this.description);
      $.each(this.images, function (key, image) {
        formData.append(`images[${key}]`, image);
      });
      axios
        .post("/products", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        })
        .then((res) => {
          this.name = "";
          this.price = "";
          this.article = "";
          this.description = "";
          this.images = [];
          this.status = true;
          this.showNotification("Товар создан");
          this.isPosting = false;
          this.$emit("update:closemodal", false);
          this.$store.dispatch("getProducts");
        });
    },
    validateForm() {
      if (!this.name) {
        this.status = false;
        this.showNotification("Имя не может быть пустым");
        return false;
      }
      if (!this.description) {
        this.status = false;
        this.showNotification("Описание не может быть пустым");
        return false;
      }
      if (!this.article) {
        this.status = false;
        this.showNotification("Артикул не может быть пустым");
        return false;
      }
      if (!this.price) {
        this.status = false;
        this.showNotification("Цена не может быть пустым");
        return false;
      }
      return true;
    },
    showNotification(message) {
      this.status_msg = message;
      setTimeout(() => {
        this.status_msg = "";
      }, 3000);
    },
  },
};
</script>