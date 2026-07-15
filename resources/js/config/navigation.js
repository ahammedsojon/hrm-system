export const navigation = [
    {
        label: 'Dashboard',
        to: { name: 'dashboard' },
        icon: 'dashboard',
    },
    {
        label: 'Organization',
        icon: 'organization',
        children: [
            {
                label: 'Company',
                to: { name: 'company' },
                icon: 'company',
            },
            {
                label: 'Branches',
                to: { name: 'branches' },
                icon: 'branches',
            },
            {
                label: 'Departments',
                to: { name: 'departments' },
                icon: 'building',
            },
            {
                label: 'Designations',
                to: { name: 'designations' },
                icon: 'badge',
            },
            {
                label: 'Employment Types',
                to: { name: 'employment-types' },
                icon: 'briefcase',
            },
            {
                label: 'Shifts',
                to: { name: 'shifts' },
                icon: 'shift',
            },
        ],
    },
    {
        label: 'Employees',
        icon: 'employees',
        children: [
            {
                label: 'Employees',
                to: { name: 'employees' },
                icon: 'user',
            },
            {
                label: 'Documents',
                to: { name: 'documents' },
                icon: 'document',
            },
            {
                label: 'Emergency Contacts',
                to: { name: 'emergency-contacts' },
                icon: 'phone',
            },
        ],
    },
];
