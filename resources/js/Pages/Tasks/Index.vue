<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    tasks: Object,
    categories: Array,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const priority = ref(props.filters.priority || "");
const categoryId = ref(props.filters.category_id || "");

const applyFilters = () => {
    router.get(
        route("tasks.index"),
        {
            search: search.value,
            status: status.value,
            priority: priority.value,
            category_id: categoryId.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const clearFilters = () => {
    search.value = "";
    status.value = "";
    priority.value = "";
    categoryId.value = "";
    applyFilters();
};

const toggleStatus = (taskId) => {
    router.post(
        route("tasks.toggle-status", taskId),
        {},
        {
            preserveScroll: true,
        },
    );
};

const deleteTask = (taskId) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?")) {
        router.delete(route("tasks.destroy", taskId), {
            preserveScroll: true,
        });
    }
};

const getPriorityColor = (priority) => {
    const colors = {
        high: "bg-red-100 text-red-800",
        medium: "bg-yellow-100 text-yellow-800",
        low: "bg-green-100 text-green-800",
    };
    return colors[priority] || "bg-gray-100 text-gray-800";
};

const getStatusColor = (status) => {
    const colors = {
        pending: "bg-gray-100 text-gray-800",
        in_progress: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};
</script>

<template>
    <Head title="Mes Tâches" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mes Tâches
                </h2>
                <Link
                    :href="route('tasks.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Nouvelle tâche
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <div class="text-sm text-gray-600">Total</div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ stats.total }}
                        </div>
                    </div>
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <div class="text-sm text-gray-600">En attente</div>
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ stats.pending }}
                        </div>
                    </div>
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <div class="text-sm text-gray-600">En cours</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ stats.in_progress }}
                        </div>
                    </div>
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <div class="text-sm text-gray-600">Terminées</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ stats.completed }}
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Recherche</label
                            >
                            <input
                                type="text"
                                v-model="search"
                                @input="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Rechercher..."
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Statut</label
                            >
                            <select
                                v-model="status"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="in_progress">En cours</option>
                                <option value="completed">Terminée</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Priorité</label
                            >
                            <select
                                v-model="priority"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Toutes</option>
                                <option value="low">Basse</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Catégorie</label
                            >
                            <select
                                v-model="categoryId"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Toutes</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div
                        v-if="search || status || priority || categoryId"
                        class="mt-4"
                    >
                        <button
                            @click="clearFilters"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            Effacer les filtres
                        </button>
                    </div>
                </div>

                <!-- Tasks List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div
                        v-if="tasks.data.length > 0"
                        class="divide-y divide-gray-200"
                    >
                        <div
                            v-for="task in tasks.data"
                            :key="task.id"
                            class="p-6 hover:bg-gray-50"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1 flex items-start space-x-3">
                                    <button
                                        @click="toggleStatus(task.id)"
                                        class="flex-shrink-0 mt-1"
                                    >
                                        <svg
                                            v-if="task.status === 'completed'"
                                            class="h-6 w-6 text-green-600"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="h-6 w-6 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke-width="2"
                                            />
                                        </svg>
                                    </button>
                                    <div class="flex-1 min-w-0">
                                        <Link
                                            :href="route('tasks.show', task.id)"
                                            class="block"
                                        >
                                            <h3
                                                :class="[
                                                    'text-lg font-medium hover:text-indigo-600',
                                                    task.status === 'completed'
                                                        ? 'line-through text-gray-500'
                                                        : 'text-gray-900',
                                                ]"
                                            >
                                                {{ task.title }}
                                            </h3>
                                        </Link>
                                        <p
                                            v-if="task.description"
                                            class="mt-1 text-sm text-gray-600 line-clamp-2"
                                        >
                                            {{ task.description }}
                                        </p>
                                        <div
                                            class="mt-2 flex flex-wrap items-center gap-2 text-sm"
                                        >
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    getPriorityColor(
                                                        task.priority,
                                                    ),
                                                ]"
                                            >
                                                {{ task.priority }}
                                            </span>
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    getStatusColor(task.status),
                                                ]"
                                            >
                                                {{
                                                    task.status.replace(
                                                        "_",
                                                        " ",
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="task.category"
                                                class="inline-flex items-center text-gray-600"
                                            >
                                                <span
                                                    v-if="task.category.color"
                                                    class="w-2 h-2 rounded-full mr-1"
                                                    :style="{
                                                        backgroundColor:
                                                            task.category.color,
                                                    }"
                                                ></span>
                                                {{ task.category.name }}
                                            </span>
                                            <span
                                                v-if="task.due_date"
                                                :class="[
                                                    task.is_overdue
                                                        ? 'text-red-600 font-medium'
                                                        : 'text-gray-500',
                                                ]"
                                            >
                                                📅
                                                {{
                                                    new Date(
                                                        task.due_date,
                                                    ).toLocaleDateString(
                                                        "fr-FR",
                                                    )
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex space-x-2">
                                    <Link
                                        :href="route('tasks.edit', task.id)"
                                        class="text-indigo-600 hover:text-indigo-800"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="deleteTask(task.id)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                            Aucune tâche
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
