<template>
    <div
        class="w-full bg-white border border-gray-300 p-3 min-h-[600px] rounded shadow-md flex flex-col justify-between">
        <div>
            <div class="p-3 mb-3">
                <slot name="header-content"></slot>
            </div>

            <div>
                <slot name="table-content"></slot>
            </div>
        </div>

        <div>
            <slot name="pagination-content"></slot>
        </div>
    </div>
</template>