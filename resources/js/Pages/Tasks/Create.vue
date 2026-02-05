<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({ categories: Array });
const form = useForm({
    title: "",
    category_id: "",
    description: "",
    priority: "medium",
    due_date: "",
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto py-12 px-4">
            <form
                @submit.prevent="form.post(route('tasks.store'))"
                class="bg-white p-8 rounded-3xl shadow-xl space-y-6"
            >
                <h2 class="text-2xl font-black">Nouvelle Tâche</h2>
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Titre de la tâche"
                    class="w-full border-gray-200 rounded-2xl p-4"
                />
                <textarea
                    v-model="form.description"
                    placeholder="Description de la tâche"
                    class="w-full border-gray-200 rounded-2xl p-4"
                ></textarea>
                <input
                    v-model="form.due_date"
                    type="date"
                    class="w-full border-gray-200 rounded-2xl p-4"
                />
                <div class="grid grid-cols-2 gap-4">
                    <select
                        v-model="form.category_id"
                        class="border-gray-200 rounded-2xl"
                    >
                        <option value="">Catégorie</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                    <select
                        v-model="form.priority"
                        class="border-gray-200 rounded-2xl"
                    >
                        <option value="low">Basse</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Haute</option>
                    </select>
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-indigo-600 text-white font-bold py-2 rounded-2xl hover:bg-indigo-700 transition"
                >
                    CRÉER
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
