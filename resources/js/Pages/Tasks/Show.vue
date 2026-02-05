<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    task: Object,
});

const deleteTask = () => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?")) {
        router.delete(route("tasks.destroy", props.task.id));
    }
};

const toggleStatus = () => {
    router.post(
        route("tasks.toggle-status", props.task.id),
        {},
        {
            preserveScroll: true,
        },
    );
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
    <Head :title="task.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détails de la tâche
                </h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('tasks.edit', task.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        Modifier
                    </Link>
                    <button
                        @click="deleteTask"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <!-- Title and Status Toggle -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ task.title }}
                                </h1>
                            </div>
                            <button
                                @click="toggleStatus"
                                class="ml-4 flex-shrink-0"
                            >
                                <svg
                                    v-if="task.status === 'completed'"
                                    class="h-8 w-8 text-green-600"
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
                                    class="h-8 w-8 text-gray-400 hover:text-gray-600"
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
                        </div>

                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                    getPriorityColor(task.priority),
                                ]"
                            >
                                Priorité: {{ task.priority }}
                            </span>
                            <span
                                :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                    getStatusColor(task.status),
                                ]"
                            >
                                {{ task.status.replace("_", " ") }}
                            </span>
                            <span
                                v-if="task.category"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800"
                            >
                                <span
                                    v-if="task.category.color"
                                    class="w-2 h-2 rounded-full mr-2"
                                    :style="{
                                        backgroundColor: task.category.color,
                                    }"
                                ></span>
                                {{ task.category.name }}
                            </span>
                        </div>

                        <!-- Description -->
                        <div v-if="task.description">
                            <h2
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                Description
                            </h2>
                            <p class="text-gray-700 whitespace-pre-line">
                                {{ task.description }}
                            </p>
                        </div>

                        <!-- Metadata -->
                        <div class="border-t pt-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-if="task.due_date">
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Date d'échéance
                                    </dt>
                                    <dd
                                        :class="[
                                            'mt-1 text-sm',
                                            task.is_overdue
                                                ? 'text-red-600 font-semibold'
                                                : 'text-gray-900',
                                        ]"
                                    >
                                        {{
                                            new Date(
                                                task.due_date,
                                            ).toLocaleDateString("fr-FR", {
                                                weekday: "long",
                                                year: "numeric",
                                                month: "long",
                                                day: "numeric",
                                            })
                                        }}
                                        <span
                                            v-if="task.is_overdue"
                                            class="ml-2"
                                            >⚠️ En retard</span
                                        >
                                    </dd>
                                </div>

                                <div v-if="task.completed_at">
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Complétée le
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{
                                            new Date(
                                                task.completed_at,
                                            ).toLocaleDateString("fr-FR", {
                                                weekday: "long",
                                                year: "numeric",
                                                month: "long",
                                                day: "numeric",
                                                hour: "2-digit",
                                                minute: "2-digit",
                                            })
                                        }}
                                    </dd>
                                </div>

                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Créée le
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{
                                            new Date(
                                                task.created_at,
                                            ).toLocaleDateString("fr-FR", {
                                                year: "numeric",
                                                month: "long",
                                                day: "numeric",
                                            })
                                        }}
                                    </dd>
                                </div>

                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Dernière modification
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{
                                            new Date(
                                                task.updated_at,
                                            ).toLocaleDateString("fr-FR", {
                                                year: "numeric",
                                                month: "long",
                                                day: "numeric",
                                            })
                                        }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Actions -->
                        <div class="border-t pt-6">
                            <Link
                                :href="route('tasks.index')"
                                class="text-indigo-600 hover:text-indigo-800 font-medium"
                            >
                                ← Retour à la liste
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
