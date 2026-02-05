<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import CategoryBadge from "@/Components/Category/CategoryBadge.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    categories: Array,
});

// État pour gérer si on est en train d'éditer une catégorie
const editingCategory = ref(null);

// Formulaire de Création et Modification
const form = useForm({
    name: "",
    color: "#3b82f6", // Couleur par défaut (bleu) pour les nouvelles catégories
});

const submit = () => {
    if (editingCategory.value) {
        form.put(route("categories.update", editingCategory.value.id), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post(route("categories.store"), {
            onSuccess: () => form.reset(),
        });
    }
};

const editCategory = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.color = category.color || "#3b82f6";
};

const cancelEdit = () => {
    editingCategory.value = null;
    form.reset();
};

const deleteCategory = (id) => {
    if (confirm("Supprimer cette catégorie ?")) {
        useForm({}).delete(route("categories.destroy", id));
    }
};
</script>

<template>
    <Head title="Gestion des Catégories" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Catégories
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{
                            editingCategory
                                ? "Modifier la catégorie"
                                : "Créer une nouvelle catégorie"
                        }}
                    </h3>

                    <form
                        @submit.prevent="submit"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start"
                    >
                        <div class="md:col-span-2">
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Nom de la catégorie"
                                class="w-full focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <div class="flex items-center space-x-2">
                                <input
                                    v-model="form.color"
                                    type="color"
                                    class="h-10 w-full rounded-md shadow-sm cursor-pointer"
                                />
                                <span class="text-xs text-gray-500 uppercase">{{
                                    form.color
                                }}</span>
                            </div>
                            <InputError
                                :message="form.errors.color"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex space-x-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                {{
                                    editingCategory
                                        ? "Mettre à jour"
                                        : "Ajouter"
                                }}
                            </button>

                            <button
                                v-if="editingCategory"
                                @click="cancelEdit"
                                type="button"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Sno
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Aperçu
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Nom
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Tâches liées
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="(category, index) in categories"
                                :key="category.id"
                                :class="{
                                    'bg-blue-50':
                                        editingCategory?.id === category.id,
                                }"
                            >
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <CategoryBadge :category="category" />
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                >
                                    {{ category.name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ category.tasks_count }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3"
                                >
                                    <button
                                        @click="editCategory(category)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Modifier
                                    </button>
                                    <button
                                        @click="deleteCategory(category.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td
                                    colspan="4"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    Aucune catégorie trouvée
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
