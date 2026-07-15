<script setup>
import OrganizationListPage from '../../components/organization/OrganizationListPage.vue';
</script>

<template>
    <OrganizationListPage
        title="Branches"
        description="Manage branch locations across your organization."
        empty-message="No branches yet. Branch management will be available soon."
        :columns="['Name', 'Code', 'Address', 'Phone', 'Status']"
    />
</template>
