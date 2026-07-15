<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { navigation } from '../../config/navigation';
import NavIcon from '../ui/NavIcon.vue';

const route = useRoute();

const employeesRoutes = ['employees', 'departments', 'designations'];

const isEmployeesActive = computed(() =>
    employeesRoutes.includes(route.name),
);

function isActive(item) {
    if (item.to?.name) {
        return route.name === item.to.name;
    }

    return false;
}

function linkClasses(active) {
    return active
        ? 'bg-slate-900 text-white shadow-sm'
        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
}

function childLinkClasses(active) {
    return active
        ? 'bg-slate-100 text-slate-900 font-medium'
        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900';
}
</script>

<template>
    <nav class="flex-1 space-y-1 px-3 py-4">
        <template v-for="item in navigation" :key="item.label">
            <router-link
                v-if="item.to"
                :to="item.to"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors"
                :class="linkClasses(isActive(item))"
            >
                <NavIcon :name="item.icon" />
                <span>{{ item.label }}</span>
            </router-link>

            <div v-else class="pt-2">
                <div
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"
                    :class="isEmployeesActive ? 'text-slate-900' : 'text-slate-500'"
                >
                    <NavIcon :name="item.icon" />
                    <span>{{ item.label }}</span>
                </div>

                <div class="mt-1 space-y-0.5 border-l border-slate-200 pl-3 ml-5">
                    <router-link
                        v-for="child in item.children"
                        :key="child.label"
                        :to="child.to"
                        class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors"
                        :class="childLinkClasses(isActive(child))"
                    >
                        <NavIcon :name="child.icon" />
                        <span>{{ child.label }}</span>
                    </router-link>
                </div>
            </div>
        </template>
    </nav>
</template>
