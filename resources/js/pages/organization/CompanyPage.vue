<script setup>
import OrganizationListPage from '../../components/organization/OrganizationListPage.vue';
</script>

<template>
    <OrganizationListPage
        title="Company"
        description="Manage company profile and organization settings."
        empty-message="No company records yet. Company management will be available soon."
        :columns="['Name', 'Code', 'Email', 'Phone', 'Status']"
    />
</template>
