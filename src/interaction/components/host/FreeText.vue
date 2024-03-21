<script setup>
const props = defineProps(["slide"]);

console.log("slide *************");
console.log(props.slide);
</script>

<template>
  <div class="d-flex flex-column">
    <div
      v-if="slide.content.image.hasImage"
      class="slide-image align-self-center my-3"
    >
      <img
        :src="
          'https://dev.learnloop.co.uk/api/interaction/uploads/img/' +
          slide.content.image.src
        "
        class="img-fluid"
      />
    </div>
    <div class="free-text-container d-flex flex-column justify">
      <div
        class="card m-1 p-2"
        v-for="(submission, index) in slide.submissions"
      >
        <table>
          <tr>
            <td>{{ submission.response }}</td>
            <td class="cell-btnClose">
              <button
                type="button"
                class="btn-close float-right mx-2"
                @click="slide.submissions.splice(index, 1)"
              ></button>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cell-btnClose {
  width: 35px;
}
.free-text-container {
  overflow-y: auto;
}
.slide-image {
  width: 50%;
}
</style>
