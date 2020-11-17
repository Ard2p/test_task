<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Редактирование</h5>
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
                    v-model="dataName"
                    type="text"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="article">Артикул</label>
                  <input
                    id="article"
                    v-model="dataArticle"
                    type="text"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="price">Цена</label>
                  <input
                    id="price"
                    v-model="dataPrice"
                    type="number"
                    class="form-control mb-3 px-2 py-2 bg-white shadow-sm border-0"
                    required
                  />
                </div>
                <div class="form-group">
                  <label for="description">Описание</label>
                  <textarea
                    id="description"
                    v-model="dataDescription"
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
                    v-for="(image, index) in dataImages"
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
  name: "editProduct",
  props: {
    id: "",
    name: "",
    description: "",
    article: "",
    price: "",
    images: {},
  },
  data() {
    return {
      dataName: "",
      dataDescription: "",
      dataArticle: "",
      dataPrice: "",
      dataImages: [],

      status_msg: "",
      status: "",
      isPosting: false,
    };
  },
  mounted() {
    this.dataName = this.name;
    this.dataDescription = this.description;
    this.dataArticle = this.article;
    this.dataPrice = this.price;
    if (this.images)
      this.images.forEach(async (image) => {
        this.dataImages.push(
          "data:image/png;base64," + (await this.getBase64(image.image_path))
        );
      });
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
          vm.dataImages.push(imageUrl);
        };
        reader.readAsDataURL(files[index]);
      }
    },
    removeImage(index) {
      this.dataImages.splice(index, 1);
    },
    createProduct(e) {
      e.preventDefault();
      if (!this.validateForm()) {
        return false;
      }
      this.isPosting = true;

      const formDataEdit = new FormData();
      formDataEdit.append("name", this.dataName);
      formDataEdit.append("price", this.dataPrice);
      formDataEdit.append("article", this.dataArticle);
      formDataEdit.append("description", this.dataDescription);
      $.each(this.dataImages, function (key, image) {
        formDataEdit.append(`images[${key}]`, image);
      });

      formDataEdit.append("_method", "PATCH");
      axios
        .post("/products/" + this.id, formDataEdit, {
          headers: { "Content-Type": "multipart/form-data" },
        })
        .then((res) => {
          this.dataName = "";
          this.dataPrice = "";
          this.dataArticle = "";
          this.dataDescription = "";
          this.dataImages = [];
          this.status = true;
          this.showNotification("Товар обновлен");
          this.isPosting = false;
          this.$emit("update:closemodal", false);
          this.$store.dispatch("getProducts");
        });
    },
    validateForm() {
      if (!this.dataName) {
        this.status = false;
        this.showNotification("Имя не может быть пустым");
        return false;
      }
      if (!this.dataDescription) {
        this.status = false;
        this.showNotification("Описание не может быть пустым");
        return false;
      }
      if (!this.dataArticle) {
        this.status = false;
        this.showNotification("Артикул не может быть пустым");
        return false;
      }
      if (!this.dataPrice) {
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
    getBase64(url) {
      return axios({
        url: url,
        baseURL: "/storage",
        method: "get",
        responseType: "arraybuffer",
      }).then((response) =>
        new Buffer(response.data, "binary").toString("base64")
      );
    },
  },
};
</script>