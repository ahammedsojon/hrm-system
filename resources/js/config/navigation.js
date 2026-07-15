export const navigation = [
    {
        label: 'Dashboard',
        to: { name: 'dashboard' },
        icon: 'dashboard',
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
                label: 'Departments',
                to: { name: 'departments' },
                icon: 'building',
            },
            {
                label: 'Designations',
                to: { name: 'designations' },
                icon: 'badge',
            },
        ],
    },
];
