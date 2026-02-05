<script setup>
import { computed } from "vue";

const props = defineProps({
    category: {
        type: Object,
        default: null,
    },
});

const style = computed(() => {
    if (!props.category || !props.category.color) return {};
    return {
        backgroundColor: `${props.category.color}20`, // Ajoute 20% d'opacité hex
        color: props.category.color,
        borderColor: `${props.category.color}40`,
    };
});
</script>

<template>
    <span
        v-if="category"
        :style="style"
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border border-transparent"
    >
        {{ category.name }}
    </span>
    <span
        v-else
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
    >
        Général
    </span>
</template>
