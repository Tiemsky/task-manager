<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const props = defineProps({ task: Object, categories: Array });

const form = useForm({
    title: props.task.title,
    description: props.task.description || "",
    category_id: props.task.category_id || "",
    priority: props.task.priority,
    status: props.task.status,
    due_date: props.task.due_date ? props.task.due_date.split("T")[0] : "",
});

const submit = () => form.put(route("tasks.update", props.task.id));
</script>

<template>
    <Head title="Modifier Tâche" />
    <AuthenticatedLayout>
        <div class="max-w-3xl mx-auto py-12">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-6">
                    Modifier : {{ task.title }}
                </h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Titre</label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full border-gray-300 rounded mt-1"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium"
                                >Statut</label
                            >
                            <select
                                v-model="form.status"
                                class="w-full border-gray-300 rounded mt-1"
                            >
                                <option value="pending">En attente</option>
                                <option value="in_progress">En cours</option>
                                <option value="completed">Terminé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium"
                                >Priorité</label
                            >
                            <select
                                v-model="form.priority"
                                class="w-full border-gray-300 rounded mt-1"
                            >
                                <option value="low">Basse</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Haute</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700"
                        >
                            Metre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
