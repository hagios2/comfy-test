<template>
  <material-pagination>
    <material-pagination-item
      v-for="(link, index) in paginationLinks"
      :key="index"
      :label="sanitizeLabel(link.label)"
      :disabled="!link.url"
      :active="link.active"
      @click="navigateTo(link.url)"
    />
  </material-pagination>
</template>

<script>
import MaterialPagination from "@/components/MaterialPagination.vue";
import MaterialPaginationItem from "@/components/MaterialPaginationItem.vue";

export default {
  components: { MaterialPagination, MaterialPaginationItem },
  props: {
    meta: {
      type: Object,
      required: true
    },
  },
  computed: {
    paginationLinks() {
      return this.meta.links.map((link) => ({
        ...link,
        label: this.formatLabel(link.label),
      }));
    },
  },
  methods: {
    sanitizeLabel(label) {
      const replacements = { "&laquo;": "<<", "&raquo;": ">>" };
      return label.replace(/&laquo;|&raquo;/g, (entity) => replacements[entity] || entity);
    },
    navigateTo(url) {
      if (url) {
        this.$emit("page-change", url);
      }
    },
    formatLabel(label) {
      const sanitizedLabel = label.replace(/&laquo;|&raquo;/g, "").trim();
      return sanitizedLabel === "Previous" ? "Prev" : sanitizedLabel;
  },
  },
};
</script>
