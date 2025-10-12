
export const sidebarLinks = {
    admin: [
        {
            title: "Home",
            items: [
                { label: "Dashboard", link: "/admin/dashboard", icon: "bx bx-home" },
            ],
        },
        {
            title: "Manage",
            items: [
                { label: "Accounts", link: "/admin/account", icon: "bx bx-user" },
            ],
        },
        {
            title: "Order",
            items: [
                { label: "Add Order", link: "/order/create", icon: "bx bx-cart-plus" },
                { label: "Order Lists", link: "/order/list", icon: "bx bx-list-ul-square" },
                { label: "Received Orders", link: "/receive-order/list", icon: "bx bx-list-ul-square" },
            ],
        },
        {
            title: "Container",
            items: [
                { label: "List of Containers", link: "/container/list", icon: "bx bx-box-alt" },
                { label: "View Containers", link: "/container/view", icon: "bx bx-box-alt" },
            ],
        },
        {
            title: "Branch",
            items: [
                { label: "Add Branch", link: "/admin/branch/create", icon: "bx bx-buildings" },
                { label: "Branch Lists", link: "/admin/branch", icon: "bx bx-list-ul-square" },
            ],
        },
        {
            title: "Destination",
            items: [
                { label: "Destination List", link: "/admin/destination", icon: "bx bx-location-plus" },
            ],
        },
        {
            title: "Box",
            items: [
                { label: "Add Box", link: "/box/create", icon: "bx bx-box-alt" },
                { label: "Box Lists", link: "/box/list", icon: "bx bx-list-ul-square" },
            ],
        },
        {
            title: "Tracking",
            items: [
                { label: "Tracking Status", link: "/track/status", icon: "bx bx-map" },
            ],
        },
    ],

    branch_admin: [
        {
            title: "Home",
            items: [
                { label: "Dashboard", link: "/branch/dashboard", icon: "bx bx-home" },
            ],
        },
        {
            title: "Manage",
            items: [
                { label: "Accounts", link: "/branch/account/list", icon: "bx bx-user" },
            ],
        },
        {
            title: "Order",
            items: [
                { label: "Add Order", link: "/order/create", icon: "bx bx-cart-plus" },
                { label: "Order Lists", link: "/order/list", icon: "bx bx-list-ul-square" },
                { label: "Warehouse Orders", link: "/collect/orders", icon: "bx bx-warehouse" },
            ],
        },
        {
            title: "Container",
            items: [
                { label: "Load to Container", link: "/container/load", icon: "bx bx-ship" },
            ],
        },
        {
            title: "Box",
            items: [
                { label: "Add Box", link: "/box/create", icon: "bx bx-box-alt" },
                { label: "Box Lists", link: "/box/list", icon: "bx bx-list-ul-square" }
            ],
        },
    ],

    agent: [

    ],
};
